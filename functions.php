<?php
// functions.php - VERSI PROFESIONAL DIPERBAIKI
include 'config.php';

// Fungsi untuk mendapatkan semua produk
function getProducts($kategori = null) {
    global $conn;
    
    $sql = "SELECT ProdukID, NamaProduk, Harga, Stok, Kategori, Deskripsi 
            FROM `kasir produk`";
    
    if ($kategori) {
        $sql .= " WHERE Kategori = ?";
    }
    $sql .= " ORDER BY NamaProduk";
    
    $stmt = $conn->prepare($sql);
    if ($kategori) {
        $stmt->bind_param("s", $kategori);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

// Fungsi untuk mendapatkan kategori
function getCategories() {
    global $conn;
    $sql = "SELECT DISTINCT Kategori FROM `kasir produk` WHERE Kategori IS NOT NULL AND Kategori != ''";
    $result = $conn->query($sql);
    
    $categories = [];
    while($row = $result->fetch_assoc()) {
        $categories[] = $row['Kategori'];
    }
    
    if (empty($categories)) {
        $categories = ['Makanan', 'Minuman', 'Snack', 'Lainnya'];
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan pelanggan
function getCustomers() {
    global $conn;
    
    $sql = "SELECT pelangganID, NamaPelanggan, Alamat, NomorTelepon, Email 
            FROM `kasir pelanggan` 
            ORDER BY NamaPelanggan";
    
    $result = $conn->query($sql);
    
    $customers = [];
    while($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    return $customers;
}

// Fungsi untuk menambah penjualan
function addSale($data) {
    global $conn;
    $sql = "INSERT INTO `kasir penjualan` 
            (TanggalPenjualan, TotalHarga, PelangganID, MetodePembayaran) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdds", 
        $data['tanggal'], 
        $data['total_harga'], 
        $data['pelanggan_id'],
        $data['metode_pembayaran']
    );
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

// Fungsi untuk menambah detail penjualan
function addSaleDetail($penjualanID, $produkID, $jumlah, $subtotal) {
    global $conn;
    $sql = "INSERT INTO `kasir detailpenjualan` (PenjualanID, ProdukID, JumlahProduk, Subtotal) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $penjualanID, $produkID, $jumlah, $subtotal);
    
    return $stmt->execute();
}

// Fungsi untuk update stok
function updateStock($produkID, $jumlah) {
    global $conn;
    $sql = "UPDATE `kasir produk` SET Stok = Stok - ? WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $jumlah, $produkID);
    
    return $stmt->execute();
}

// Fungsi untuk mendapatkan riwayat penjualan
function getSalesHistory($startDate = null, $endDate = null) {
    global $conn;
    $sql = "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, 
                   p.MetodePembayaran, pl.NamaPelanggan
            FROM `kasir penjualan` p
            LEFT JOIN `kasir pelanggan` pl ON p.PelangganID = pl.pelangganID
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($startDate) {
        $sql .= " AND p.TanggalPenjualan >= ?";
        $params[] = $startDate;
        $types .= "s";
    }
    
    if ($endDate) {
        $sql .= " AND p.TanggalPenjualan <= ?";
        $params[] = $endDate;
        $types .= "s";
    }
    
    $sql .= " ORDER BY p.TanggalPenjualan DESC, p.PenjualanID DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sales = [];
    while($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
    return $sales;
}

// Fungsi untuk laporan penjualan
function getSalesReport($period = 'hari') {
    global $conn;
    
    switch($period) {
        case 'hari':
            $sql = "SELECT DATE(TanggalPenjualan) as Periode, 
                           COUNT(*) as JumlahTransaksi,
                           SUM(TotalHarga) as TotalPenjualan
                    FROM `kasir penjualan` 
                    WHERE TanggalPenjualan >= CURDATE() 
                    GROUP BY DATE(TanggalPenjualan)";
            break;
        case 'minggu':
            $sql = "SELECT YEAR(TanggalPenjualan) as Tahun, 
                           WEEK(TanggalPenjualan) as Minggu,
                           COUNT(*) as JumlahTransaksi,
                           SUM(TotalHarga) as TotalPenjualan
                    FROM `kasir penjualan` 
                    WHERE TanggalPenjualan >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    GROUP BY YEAR(TanggalPenjualan), WEEK(TanggalPenjualan)";
            break;
        case 'bulan':
            $sql = "SELECT YEAR(TanggalPenjualan) as Tahun, 
                           MONTH(TanggalPenjualan) as Bulan,
                           COUNT(*) as JumlahTransaksi,
                           SUM(TotalHarga) as TotalPenjualan
                    FROM `kasir penjualan` 
                    WHERE TanggalPenjualan >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                    GROUP BY YEAR(TanggalPenjualan), MONTH(TanggalPenjualan)";
            break;
    }
    
    $result = $conn->query($sql);
    $reports = [];
    while($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    return $reports;
}

// Fungsi untuk menambah produk
function addProduct($data) {
    global $conn;
    $sql = "INSERT INTO `kasir produk` 
            (NamaProduk, Harga, Stok, Kategori, Deskripsi) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiss", 
        $data['nama_produk'],
        $data['harga'],
        $data['stok'],
        $data['kategori'],
        $data['deskripsi']
    );
    
    return $stmt->execute();
}

// Fungsi untuk update produk
function updateProduct($produkID, $data) {
    global $conn;
    $sql = "UPDATE `kasir produk` SET 
            NamaProduk = ?, Harga = ?, Stok = ?, Kategori = ?, Deskripsi = ? 
            WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", 
        $data['nama_produk'],
        $data['harga'],
        $data['stok'],
        $data['kategori'],
        $data['deskripsi'],
        $produkID
    );
    
    return $stmt->execute();
}

// Fungsi untuk menghapus produk
function deleteProduct($produkID) {
    global $conn;
    $sql = "DELETE FROM `kasir produk` WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $produkID);
    
    return $stmt->execute();
}

// FUNGSI BARU: Get sale data untuk receipt
function getSaleData($penjualanID) {
    global $conn;
    
    // Get sale header
    $sql = "SELECT p.*, pl.NamaPelanggan, pl.Alamat, pl.NomorTelepon 
            FROM `kasir penjualan` p 
            LEFT JOIN `kasir pelanggan` pl ON p.PelangganID = pl.pelangganID 
            WHERE p.PenjualanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $penjualanID);
    $stmt->execute();
    $sale = $stmt->get_result()->fetch_assoc();
    
    if (!$sale) {
        return false;
    }
    
    // Get sale details
    $sql = "SELECT dp.*, pr.NamaProduk, pr.Harga 
            FROM `kasir detailpenjualan` dp 
            JOIN `kasir produk` pr ON dp.ProdukID = pr.ProdukID 
            WHERE dp.PenjualanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $penjualanID);
    $stmt->execute();
    $saleDetails = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return [
        'sale' => $sale,
        'details' => $saleDetails
    ];
}

// Fungsi untuk mencari produk
function searchProducts($keyword) {
    global $conn;
    $sql = "SELECT ProdukID, NamaProduk, Harga, Stok, Kategori 
            FROM `kasir produk` 
            WHERE NamaProduk LIKE ? OR Kategori LIKE ? 
            ORDER BY NamaProduk";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$keyword%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}
?>