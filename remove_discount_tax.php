<?php
// remove_discount_tax.php
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "kasir_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Hapus kolom diskon dan pajak
$queries = [
    "ALTER TABLE `kasir produk` 
     DROP COLUMN IF EXISTS `Diskon`,
     DROP COLUMN IF EXISTS `Pajak`",
    
    "ALTER TABLE `kasir pelanggan`
     DROP COLUMN IF EXISTS `TipeDiskon`,
     DROP COLUMN IF EXISTS `BesarDiskon`",
    
    "ALTER TABLE `kasir penjualan`
     DROP COLUMN IF EXISTS `DiskonPelanggan`,
     DROP COLUMN IF EXISTS `PajakTotal`,
     DROP COLUMN IF EXISTS `TotalSetelahDiskon`",
    
    "ALTER TABLE `kasir detailpenjualan`
     DROP COLUMN IF EXISTS `Pajak`"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Query berhasil: " . substr($query, 0, 50) . "...<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

echo "Hapus kolom diskon & pajak selesai!";
$conn->close();
?>