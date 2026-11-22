<?php
session_start();
include("koneksi.php");

// Proteksi sesi
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

// Ambil data pesanan termasuk berat dan id
$sql = "SELECT `id`, `nama`, `telepon`, `alamat`, `layanan`, `berat`, `catatan`, `status`, `tanggal_pesanan`, `harga` FROM `pesanan` ORDER BY `id` DESC";
$result = mysqli_query($conn, $sql);

// Hitung statistik (case-insensitive sesuai ENUM di database)
$total_pesanan = 0;
$total_pending = 0;
$total_proses = 0;
$total_selesai = 0;

$count_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN LOWER(status) = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN LOWER(status) IN ('proses', 'diproses') THEN 1 ELSE 0 END) as proses,
    SUM(CASE WHEN LOWER(status) IN ('selesai', 'diambil') THEN 1 ELSE 0 END) as selesai
FROM pesanan";
$count_result = mysqli_query($conn, $count_sql);
if ($count_result) {
    $counts = mysqli_fetch_assoc($count_result);
    $total_pesanan = intval($counts['total'] ?? 0);
    $total_pending = intval($counts['pending'] ?? 0);
    $total_proses = intval($counts['proses'] ?? 0);
    $total_selesai = intval($counts['selesai'] ?? 0);
}

// Pesan notifikasi
$msg = $_GET['msg'] ?? '';
$err_detail = isset($_GET['err']) ? ': ' . htmlspecialchars($_GET['err']) : '';
$alert = '';
if ($msg === 'deleted') $alert = '<div class="alert alert-success">✅ Pesanan berhasil dihapus.</div>';
if ($msg === 'updated') $alert = '<div class="alert alert-success">✅ Status pesanan berhasil diperbarui!</div>';
if ($msg === 'price_updated') $alert = '<div class="alert alert-success">✅ Harga pesanan berhasil diperbarui!</div>';
if ($msg === 'edited') $alert = '<div class="alert alert-success">✅ Data pesanan berhasil diperbarui!</div>';
if ($msg === 'error') $alert = '<div class="alert alert-error">⚠️ Terjadi kesalahan saat menyimpan data' . $err_detail . '</div>';
if ($msg === 'invalid') $alert = '<div class="alert alert-warning">⚠️ Data yang dikirim tidak valid.</div>';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard Admin — Sparkleen Laundry</title>
    <link rel="stylesheet" href="../asset/style.css">
    <style>
        .status-form {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }
        .action-group {
            display: flex;
            gap: 6px;
            align-items: center;
        }
    </style>
</head>
<body class="admin-layout">
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <div class="header-left">
                <a href="dashboard_admin.php" class="navbar-brand">
                    <span class="brand-icon">🧺</span>
                    Sparkleen <span class="brand-accent">Laundry</span>
                </a>
                <span class="text-muted text-sm">Panel Admin</span>
            </div>
            <div class="header-right">
                <span class="text-sm" style="color: var(--text-muted);">
                    👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                </span>
                <a href="logout.php" class="btn btn-sm btn-secondary" onclick="return confirm('Apakah Anda yakin ingin logout?');">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-content">
        <?= $alert ?>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="quick-stat-card">
                <div class="stat-icon stat-icon-blue" style="float: right;">📋</div>
                <div class="stat-label">Total Pesanan</div>
                <div class="stat-value"><?= $total_pesanan ?></div>
            </div>
            <div class="quick-stat-card">
                <div class="stat-icon stat-icon-amber" style="float: right;">⏳</div>
                <div class="stat-label">Menunggu (Pending)</div>
                <div class="stat-value"><?= $total_pending ?></div>
            </div>
            <div class="quick-stat-card">
                <div class="stat-icon stat-icon-blue" style="float: right;">🔄</div>
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-value"><?= $total_proses ?></div>
            </div>
            <div class="quick-stat-card">
                <div class="stat-icon stat-icon-green" style="float: right;">✅</div>
                <div class="stat-label">Selesai / Diambil</div>
                <div class="stat-value"><?= $total_selesai ?></div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header">
                <div class="admin-title-row">
                    <div>
                        <h2>Daftar Pesanan</h2>
                        <p>Kelola semua pesanan laundry pelanggan</p>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="mobile-scroll-hint">
                    <span>👉 Geser tabel ke kanan/kiri untuk melihat kolom lengkap &amp; aksi</span>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Layanan</th>
                                <th>Berat</th>
                                <th>Catatan</th>
                                <th>Status Pesanan</th>
                                <th>Tanggal</th>
                                <th>Harga (Rp)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($result && mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)):
                                    $current_status = $row['status'] ?? 'Pending';
                                    $berat = floatval($row['berat'] ?? 0);
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                                <td><?= htmlspecialchars($row['telepon']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($row['layanan']) ?></td>
                                <td><strong><?= $berat ?> Kg</strong></td>
                                <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                                <td>
                                    <form action="update_status.php" method="POST" class="status-form">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                            <option value="Pending" <?= (strcasecmp($current_status, 'Pending') === 0) ? 'selected' : '' ?>>⏳ Pending</option>
                                            <option value="Diproses" <?= (strcasecmp($current_status, 'Diproses') === 0 || strcasecmp($current_status, 'proses') === 0) ? 'selected' : '' ?>>🔄 Diproses</option>
                                            <option value="Selesai" <?= (strcasecmp($current_status, 'Selesai') === 0) ? 'selected' : '' ?>>✅ Selesai</option>
                                            <option value="Diambil" <?= (strcasecmp($current_status, 'Diambil') === 0) ? 'selected' : '' ?>>📦 Diambil</option>
                                            <option value="Dibatalkan" <?= (strcasecmp($current_status, 'Dibatalkan') === 0 || strcasecmp($current_status, 'batal') === 0) ? 'selected' : '' ?>>❌ Dibatalkan</option>
                                        </select>
                                        <button type="submit" class="btn btn-xs btn-blue" style="padding: 5px 8px;" title="Simpan Status">✓</button>
                                    </form>
                                </td>
                                <td style="white-space: nowrap;"><?= htmlspecialchars($row['tanggal_pesanan'] ?? '-') ?></td>
                                <td style="font-weight: 600; color: var(--text-dark); white-space: nowrap;">
                                    Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="edit_pesanan.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-amber" title="Edit Pesanan">
                                            ✏️ Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-xs btn-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan dari <?= htmlspecialchars($row['nama']) ?>?');">
                                            🗑 Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state">
                                        <div class="empty-icon">📭</div>
                                        <h3>Belum ada pesanan</h3>
                                        <p>Pesanan yang masuk dari pelanggan akan tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <strong>Sparkleen Laundry</strong> — Admin Panel</p>
        </div>
    </footer>
</body>
</html>
