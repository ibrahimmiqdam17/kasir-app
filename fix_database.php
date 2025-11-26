<?php
// fix_database.php - VERSI DIPERBAIKI
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "kasir_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "<h2>Memperbaiki Struktur Database KASIR PRO</h2>";

// Tambahkan kolom yang diperlukan
$queries = [
    // Kolom untuk produk
    "ALTER TABLE `kasir produk` 
     ADD COLUMN IF NOT EXISTS `Kategori` VARCHAR(100) DEFAULT 'Umum'",
    
    "ALTER TABLE `kasir produk` 
     ADD COLUMN IF NOT EXISTS `Deskripsi` TEXT",
    
    "ALTER TABLE `kasir produk` 
     ADD COLUMN IF NOT EXISTS `Gambar` VARCHAR(255)",
    
    "ALTER TABLE `kasir produk` 
     ADD COLUMN IF NOT EXISTS `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    
    "ALTER TABLE `kasir produk` 
     ADD COLUMN IF NOT EXISTS `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    
    // Kolom untuk pelanggan
    "ALTER TABLE `kasir pelanggan`
     ADD COLUMN IF NOT EXISTS `Email` VARCHAR(255)",
    
    "ALTER TABLE `kasir pelanggan`
     ADD COLUMN IF NOT EXISTS `MemberSejak` DATE",
    
    // Kolom untuk penjualan
    "ALTER TABLE `kasir penjualan`
     ADD COLUMN IF NOT EXISTS `WaktuPenjualan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    
    "ALTER TABLE `kasir penjualan`
     ADD COLUMN IF NOT EXISTS `MetodePembayaran` ENUM('tunai', 'debit', 'kredit', 'qris') DEFAULT 'tunai'",
    
    "ALTER TABLE `kasir penjualan`
     ADD COLUMN IF NOT EXISTS `Status` ENUM('selesai', 'pending', 'dibatalkan') DEFAULT 'selesai'",
    
    // Buat tabel jika belum ada
    "CREATE TABLE IF NOT EXISTS `kasir produk` (
        `ProdukID` INT AUTO_INCREMENT PRIMARY KEY,
        `NamaProduk` VARCHAR(255) NOT NULL,
        `Harga` DECIMAL(10,2) NOT NULL,
        `Stok` INT NOT NULL,
        `Kategori` VARCHAR(100) DEFAULT 'Umum',
        `Deskripsi` TEXT,
        `Gambar` VARCHAR(255),
        `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS `kasir pelanggan` (
        `pelangganID` INT AUTO_INCREMENT PRIMARY KEY,
        `NamaPelanggan` VARCHAR(255) NOT NULL,
        `Alamat` TEXT NOT NULL,
        `NomorTelepon` VARCHAR(20) NOT NULL,
        `Email` VARCHAR(255),
        `MemberSejak` DATE,
        `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS `kasir penjualan` (
        `PenjualanID` INT AUTO_INCREMENT PRIMARY KEY,
        `TanggalPenjualan` DATE NOT NULL,
        `WaktuPenjualan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `TotalHarga` DECIMAL(10,2) NOT NULL,
        `PelangganID` INT DEFAULT 1,
        `MetodePembayaran` ENUM('tunai', 'debit', 'kredit', 'qris') DEFAULT 'tunai',
        `Status` ENUM('selesai', 'pending', 'dibatalkan') DEFAULT 'selesai',
        `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`PelangganID`) REFERENCES `kasir pelanggan`(`pelangganID`)
    )",
    
    "CREATE TABLE IF NOT EXISTS `kasir detailpenjualan` (
        `DetailID` INT AUTO_INCREMENT PRIMARY KEY,
        `PenjualanID` INT NOT NULL,
        `ProdukID` INT NOT NULL,
        `JumlahProduk` INT NOT NULL,
        `Subtotal` DECIMAL(10,2) NOT NULL,
        `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`PenjualanID`) REFERENCES `kasir penjualan`(`PenjualanID`),
        FOREIGN KEY (`ProdukID`) REFERENCES `kasir produk`(`ProdukID`)
    )",
    
    // Insert sample data jika tabel kosong
    "INSERT IGNORE INTO `kasir pelanggan` (`pelangganID`, `NamaPelanggan`, `Alamat`, `NomorTelepon`, `Email`) VALUES 
    (1, 'Umum', '-', '-', '-')",
    
    "INSERT IGNORE INTO `kasir produk` (`ProdukID`, `NamaProduk`, `Harga`, `Stok`, `Kategori`) VALUES 
    (1, 'Es Teh Manis', 5000, 100, 'Minuman'),
    (2, 'Kopi Hitam', 8000, 50, 'Minuman'),
    (3, 'Nasi Goreng', 15000, 30, 'Makanan'),
    (4, 'Mie Goreng', 12000, 25, 'Makanan'),
    (5, 'Roti Bakar', 10000, 20, 'Snack')"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "<p style='color: green;'>✓ Berhasil: " . substr($query, 0, 80) . "...</p>";
    } else {
        echo "<p style='color: red;'>✗ Error: " . $conn->error . "</p>";
    }
}

echo "<h3 style='color: blue;'>Update database selesai!</h3>";
echo "<p><a href='index.php' style='color: white; background: #007bff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Aplikasi</a></p>";

$conn->close();
?>