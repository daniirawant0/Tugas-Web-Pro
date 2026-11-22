<?php
session_start();
include("koneksi.php");

// Proteksi akses admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM pesanan WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: dashboard_admin.php?msg=deleted");
        } else {
            header("Location: dashboard_admin.php?msg=error");
        }
        $stmt->close();
    } else {
        header("Location: dashboard_admin.php?msg=invalid");
    }
} else {
    header("Location: dashboard_admin.php");
}
exit();
?>
