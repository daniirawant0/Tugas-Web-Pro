<?php
session_start();
include("koneksi.php");

// Proteksi akses admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

// Acuan tarif per Kg
$hargaPerKg = [
    "reguler"  => 6000,
    "kilat"    => 12000,
    "dryclean" => 15000,
    "sepatu"   => 25000,
    "karpet"   => 30000,
    "setrika"  => 4000,
];

$id = intval($_GET['id'] ?? 0);
$error_msg = "";

if ($id <= 0) {
    header("Location: dashboard_admin.php?msg=invalid");
    exit();
}

// Proses Simpan Edit Pesanan
if (isset($_POST['submit'])) {
    $nama    = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');
    $layanan = trim($_POST['layanan'] ?? '');
    $berat   = floatval($_POST['berat'] ?? 1);
    $catatan = trim($_POST['catatan'] ?? '');
    $status  = trim($_POST['status'] ?? 'Pending');
    $harga   = floatval($_POST['harga'] ?? 0);

    // Pemetaan status valid sesuai ENUM
    $allowed_statuses = ['Pending', 'Diproses', 'Selesai', 'Diambil', 'Dibatalkan'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'Pending';
    }

    if (empty($nama) || empty($telepon) || empty($layanan) || empty($alamat) || $berat <= 0) {
        $error_msg = "Harap lengkapi semua kolom wajib dengan benar.";
    } else {
        $update_sql = "UPDATE pesanan SET 
            nama = ?, 
            telepon = ?, 
            alamat = ?, 
            layanan = ?, 
            berat = ?, 
            catatan = ?, 
            status = ?, 
            harga = ? 
            WHERE id = ?";
        
        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("ssssdssdi", $nama, $telepon, $alamat, $layanan, $berat, $catatan, $status, $harga, $id);
            if ($stmt->execute()) {
                header("Location: dashboard_admin.php?msg=edited");
                exit();
            } else {
                $error_msg = "Gagal memperbarui pesanan: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Gagal mempersiapkan query: " . $conn->error;
        }
    }
}

// Ambil data pesanan saat ini
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard_admin.php?msg=invalid");
    exit();
}

$pesanan = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Edit Pesanan #<?= $pesanan['id'] ?> — Sparkleen Laundry</title>
    <link rel="stylesheet" href="../asset/style.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: var(--space-xl) auto;
            padding: 0 var(--space-md);
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-md);
        }
        @media (max-width: 640px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }
        .recalc-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface-ice-light);
            padding: 10px 14px;
            border-radius: var(--rounded);
            border: 1px solid var(--border-subtle);
            margin-top: var(--space-xs);
        }
    </style>
</head>
<body class="admin-layout">
    <!-- Header Admin -->
    <header class="admin-header">
        <div class="container">
            <div class="header-left">
                <a href="dashboard_admin.php" class="navbar-brand">
                    <span class="brand-icon">🧺</span>
                    Sparkleen <span class="brand-accent">Laundry</span>
                </a>
                <span class="text-muted text-sm">Edit Pesanan</span>
            </div>
            <div class="header-right">
                <a href="dashboard_admin.php" class="btn btn-sm btn-secondary">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="edit-container">
        <div class="card">
            <div class="card-header">
                <h2>✏️ Edit Data Pesanan #<?= $pesanan['id'] ?></h2>
                <p>Ubah rincian pesanan jika ada kekeliruan input dari pelanggan</p>
            </div>
            <div class="card-body">
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-error">
                        ⚠️ <?= htmlspecialchars($error_msg) ?>
                    </div>
                <?php endif; ?>

                <form action="edit_pesanan.php?id=<?= $pesanan['id'] ?>" method="POST" id="editForm">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nama">Nama Pelanggan <span class="form-required">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" 
                                   value="<?= htmlspecialchars($pesanan['nama']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="telepon">Nomor Telepon <span class="form-required">*</span></label>
                            <input type="text" class="form-control" name="telepon" id="telepon" 
                                   value="<?= htmlspecialchars($pesanan['telepon']) ?>" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="layanan">Jenis Layanan <span class="form-required">*</span></label>
                            <select class="form-control" name="layanan" id="layanan" required>
                                <option value="reguler" <?= ($pesanan['layanan'] === 'reguler') ? 'selected' : '' ?>>👔 Cuci Kering Setrika (Rp 6.000 / Kg)</option>
                                <option value="kilat" <?= ($pesanan['layanan'] === 'kilat') ? 'selected' : '' ?>>⚡ Laundry Kilat 6 Jam (Rp 12.000 / Kg)</option>
                                <option value="dryclean" <?= ($pesanan['layanan'] === 'dryclean') ? 'selected' : '' ?>>🧥 Dry Clean (Rp 15.000 / Kg)</option>
                                <option value="sepatu" <?= ($pesanan['layanan'] === 'sepatu') ? 'selected' : '' ?>>👟 Cuci Sepatu / Tas (Rp 25.000 / item/Kg)</option>
                                <option value="karpet" <?= ($pesanan['layanan'] === 'karpet') ? 'selected' : '' ?>>🛏️ Cuci Karpet / Bed Cover (Rp 30.000 / item/Kg)</option>
                                <option value="setrika" <?= ($pesanan['layanan'] === 'setrika') ? 'selected' : '' ?>>👕 Setrika Saja (Rp 4.000 / Kg)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="berat">Berat Pakaian (Kg) <span class="form-required">*</span></label>
                            <input type="number" step="0.1" min="0.1" class="form-control" name="berat" id="berat" 
                                   value="<?= htmlspecialchars($pesanan['berat'] ?? '1') ?>" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="status">Status Pesanan <span class="form-required">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="Pending" <?= ($pesanan['status'] === 'Pending') ? 'selected' : '' ?>>⏳ Pending</option>
                                <option value="Diproses" <?= ($pesanan['status'] === 'Diproses') ? 'selected' : '' ?>>🔄 Diproses</option>
                                <option value="Selesai" <?= ($pesanan['status'] === 'Selesai') ? 'selected' : '' ?>>✅ Selesai</option>
                                <option value="Diambil" <?= ($pesanan['status'] === 'Diambil') ? 'selected' : '' ?>>📦 Diambil</option>
                                <option value="Dibatalkan" <?= ($pesanan['status'] === 'Dibatalkan') ? 'selected' : '' ?>>❌ Dibatalkan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="harga">Total Harga (Rp) <span class="form-required">*</span></label>
                            <input type="number" step="1000" min="0" class="form-control" name="harga" id="harga" 
                                   value="<?= htmlspecialchars(intval($pesanan['harga'])) ?>" required>
                            <div class="recalc-box">
                                <span style="font-size: 12px; color: var(--text-muted);">Sesuai acuan tarif:</span>
                                <button type="button" class="btn btn-xs btn-blue" onclick="hitungUlangHarga()">
                                    🔄 Hitung Otomatis
                                </button>
                                <span id="recalcInfo" style="font-size: 12px; color: var(--primary); font-weight: 600;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="alamat">Alamat Penjemputan / Pengantaran <span class="form-required">*</span></label>
                        <textarea class="form-control" name="alamat" id="alamat" rows="3" required><?= htmlspecialchars($pesanan['alamat']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="catatan">Catatan Pesanan</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3"><?= htmlspecialchars($pesanan['catatan'] ?? '') ?></textarea>
                    </div>

                    <div style="display: flex; gap: var(--space-md); margin-top: var(--space-xl); align-items: center;">
                        <button type="submit" name="submit" class="btn btn-blue">
                            💾 Simpan Perubahan
                        </button>
                        <a href="dashboard_admin.php" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> <strong>Sparkleen Laundry</strong> — Admin Panel</p>
        </div>
    </footer>

    <script>
        const tarifHarga = {
            "reguler": 6000,
            "kilat": 12000,
            "dryclean": 15000,
            "sepatu": 25000,
            "karpet": 30000,
            "setrika": 4000
        };

        function hitungUlangHarga() {
            const layanan = document.getElementById('layanan').value;
            const berat = parseFloat(document.getElementById('berat').value) || 0;
            const hargaInput = document.getElementById('harga');
            const info = document.getElementById('recalcInfo');

            if (layanan && tarifHarga[layanan] && berat > 0) {
                const tarif = tarifHarga[layanan];
                const total = Math.round(tarif * berat);
                hargaInput.value = total;
                info.textContent = 'Rp ' + total.toLocaleString('id-ID') + ' (' + berat + ' Kg × Rp ' + tarif.toLocaleString('id-ID') + ')';
            }
        }
    </script>
</body>
</html>
