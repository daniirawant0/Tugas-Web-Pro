<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "laundry";

// Membuat Koneksi
$conn = mysqli_connect($host, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Set charset untuk keamanan
mysqli_set_charset($conn, "utf8mb4");
?>
