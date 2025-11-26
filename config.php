<?php
// config.php - VERSI DIPERBAIKI
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya cek session untuk halaman yang bukan login
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }
}

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "kasir_db";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>