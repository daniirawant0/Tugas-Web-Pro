<?php
// Koneksi ke database MySQL Sparkleen Laundry
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sparkleen_laundry";

// Koneksi tanpa memilih database terlebih dahulu
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS " . $db);
$conn->select_db($db);

// Buat tabel pesanan jika belum ada
$conn->query("
    CREATE TABLE IF NOT EXISTS pesanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        telepon VARCHAR(20) NOT NULL,
        layanan VARCHAR(50) NOT NULL,
        alamat TEXT NOT NULL,
        catatan TEXT,
        tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('baru','diproses','selesai','dikirim','dibatalkan') DEFAULT 'baru',
        total_harga INT DEFAULT 0
    )
");

$conn->set_charset("utf8mb4");
?>
