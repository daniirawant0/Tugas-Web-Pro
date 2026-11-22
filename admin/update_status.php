<?php
session_start();
include("koneksi.php");

// Proteksi akses admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $raw_status = trim($_POST['status']);

    // Pemetaan status fleksibel ke nilai ENUM database: 'Pending','Diproses','Selesai','Diambil','Dibatalkan'
    $status_map = [
        'pending'    => 'Pending',
        'proses'     => 'Diproses',
        'diproses'   => 'Diproses',
        'selesai'    => 'Selesai',
        'diambil'    => 'Diambil',
        'batal'      => 'Dibatalkan',
        'dibatalkan' => 'Dibatalkan'
    ];

    $key = strtolower($raw_status);
    $status = $status_map[$key] ?? null;

    if ($id > 0 && $status !== null) {
        $stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                header("Location: dashboard_admin.php?msg=updated");
                exit();
            } else {
                header("Location: dashboard_admin.php?msg=error&err=" . urlencode($stmt->error));
                exit();
            }
            $stmt->close();
        } else {
            header("Location: dashboard_admin.php?msg=error&err=" . urlencode($conn->error));
            exit();
        }
    } else {
        header("Location: dashboard_admin.php?msg=invalid");
        exit();
    }
} else {
    header("Location: dashboard_admin.php");
    exit();
}
?>
