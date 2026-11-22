<?php
// hapus_pesanan.php - DELETE: Hapus pesanan dari database
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    exit;
}

$stmt = $conn->prepare("DELETE FROM pesanan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: daftar_pesanan.php?deleted=1");
    exit;
} else {
    http_response_code(500);
    echo "Gagal menghapus pesanan: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
