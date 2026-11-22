<?php
// update_status.php - UPDATE: Ubah status pesanan
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

$id      = intval($_POST["id"] ?? 0);
$status  = trim($_POST["status"] ?? "");

$statusValid = ["baru", "diproses", "selesai", "dikirim", "dibatalkan"];
if ($id <= 0 || !in_array($status, $statusValid)) {
    http_response_code(400);
    exit;
}

$stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    header("Location: daftar_pesanan.php?updated=1");
    exit;
} else {
    http_response_code(500);
    echo "Gagal memperbarui status: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
