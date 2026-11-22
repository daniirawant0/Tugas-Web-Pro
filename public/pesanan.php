<?php
session_start();
include("../admin/koneksi.php");

// Acuan tarif harga laundry
$hargaPerKg = [
    "reguler"  => 6000,
    "kilat"    => 12000,
    "dryclean" => 15000,
    "sepatu"   => 25000,
    "karpet"   => 30000,
    "setrika"  => 4000,
];

$pesan = "";
$pesan_type = "";

if (isset($_POST['submit'])) {
    $nama    = trim($_POST["nama"] ?? '');
    $telepon = trim($_POST["telepon"] ?? '');
    $layanan = trim($_POST["layanan"] ?? '');
    $berat   = floatval($_POST["berat"] ?? 1);
    $alamat  = trim($_POST["alamat"] ?? '');
    $catatan = trim($_POST["catatan"] ?? '');

    if (empty($nama) || empty($telepon) || empty($layanan) || empty($alamat) || $berat <= 0) {
        $pesan = "Harap lengkapi semua kolom yang bertanda wajib (*) dengan benar (berat minimal 0.1 Kg).";
        $pesan_type = "error";
    } else {
        // Hitung estimasi harga berdasarkan acuan tarif
        $tarif = $hargaPerKg[$layanan] ?? 0;
        $harga = $berat * $tarif;

        // Simpan pesanan ke database
        $stmt = $conn->prepare("INSERT INTO pesanan (nama, telepon, alamat, layanan, berat, catatan, status, tanggal_pesanan, harga) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), ?)");
        $stmt->bind_param("ssssdsd", $nama, $telepon, $alamat, $layanan, $berat, $catatan, $harga);

        if ($stmt->execute()) {
            $pesan = "Pesanan Anda berhasil dikirim! Estimasi total biaya: Rp " . number_format($harga, 0, ',', '.') . " (" . $berat . " Kg). Kami akan segera memproses pesanan Anda.";
            $pesan_type = "success";
            // Reset form
            $nama = $telepon = $layanan = $alamat = $catatan = "";
            $berat = 1;
        } else {
            $pesan = "Gagal mengirim pesanan: " . $stmt->error;
            $pesan_type = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Form Pemesanan — Sparkleen Laundry</title>
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="page-order">
    <!-- Background Decoration -->
    <div class="page-bg" aria-hidden="true">
        <div class="pg-cloud pg-cloud-1"></div>
        <div class="pg-cloud pg-cloud-2"></div>
        <div class="pg-dither"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">
                <span class="brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="3"></rect><circle cx="12" cy="13" r="5"></circle><circle cx="8" cy="6" r="1" fill="currentColor"></circle></svg>
                </span>
                Sparkleen <span class="brand-accent">Laundry</span>
            </a>
            <button class="navbar-toggle" onclick="document.getElementById('navMenu').classList.toggle('show')">☰</button>
            <ul class="navbar-nav" id="navMenu">
                <li><a href="dashboard.php">Beranda</a></li>
                <li><a href="dashboard.php#layanan">Layanan</a></li>
                <li><a href="dashboard.php#tentang">Tentang Kami</a></li>
                <li><a href="dashboard.php#kontak">Kontak</a></li>
                <li><a href="pesanan.php" class="active">Form Pemesanan</a></li>
                <li><a href="../admin/login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <main class="order-main">
        <!-- Hero Header Section -->
        <section class="order-hero">
            <div class="order-hero-badge">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                Layanan Penjemputan Cepat &amp; Higienis
            </div>
            <h1 class="order-hero-title">
                Form <span class="font-editorial accent-gradient">Pemesanan</span>
            </h1>
            <p class="order-hero-desc">
                Isi form di bawah ini untuk memesan penjemputan pakaian. Setelah terkirim, pesanan Anda langsung tercatat di sistem dan tim kami akan segera memprosesnya.
            </p>
        </section>

        <!-- Form Card Section -->
        <section class="order-form-section">
            <div class="order-card">
                <!-- Card Header -->
                <div class="order-card-header">
                    <div class="order-card-header-top">
                        <span class="order-label-badge">FORMULIR PESANAN</span>
                        <span class="order-step-label">Tahap 1 dari 1</span>
                    </div>
                    <h2 class="order-card-title">Form Pemesanan Cepat</h2>
                    <p class="order-card-desc">Ceritakan kebutuhan laundry Anda pada form ini. Tim Sparkleen Laundry akan mencatat pesanan, lalu menjadwalkan kurir untuk menjemput pakaian sesuai alamat Anda.</p>
                </div>

                <!-- Alert Messages -->
                <?php if (!empty($pesan)): ?>
                    <div class="order-alert <?= $pesan_type === 'success' ? 'order-alert-success' : 'order-alert-error' ?>">
                        <div class="order-alert-icon">
                            <?php if ($pesan_type === 'success'): ?>
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <?php else: ?>
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <?php endif; ?>
                        </div>
                        <span><?= htmlspecialchars($pesan) ?></span>
                    </div>
                <?php endif; ?>

                <!-- FORM -->
                <form action="pesanan.php" method="POST" id="orderForm" class="order-form">
                    <!-- Nama Lengkap -->
                    <div class="field-group">
                        <label class="field-label" for="nama">
                            Nama Lengkap <span class="field-required">*</span>
                        </label>
                        <div class="field-input-wrap">
                            <div class="field-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" class="field-input" name="nama" id="nama"
                                   placeholder="Masukkan nama lengkap Anda" required
                                   autocomplete="name"
                                   value="<?= htmlspecialchars($nama ?? '') ?>">
                        </div>
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div class="field-group">
                        <label class="field-label" for="telepon">
                            Nomor WhatsApp <span class="field-required">*</span>
                        </label>
                        <div class="field-input-wrap">
                            <div class="field-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="tel" class="field-input" name="telepon" id="telepon"
                                   placeholder="Contoh: 0812-3456-7890" required
                                   autocomplete="tel"
                                   value="<?= htmlspecialchars($telepon ?? '') ?>">
                        </div>
                    </div>

                    <!-- Jenis Layanan -->
                    <div class="field-group">
                        <label class="field-label" for="layanan">
                            Pilih Layanan <span class="field-required">*</span>
                        </label>
                        <div class="field-input-wrap field-select-wrap">
                            <div class="field-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <select class="field-input field-select" name="layanan" id="layanan" required>
                                <option value="">— Pilih layanan laundry —</option>
                                <option value="reguler" <?= (($layanan ?? '') === 'reguler') ? 'selected' : '' ?>>👔 Cuci Kering Setrika (Rp 6.000 / Kg)</option>
                                <option value="kilat" <?= (($layanan ?? '') === 'kilat') ? 'selected' : '' ?>>⚡ Laundry Kilat 6 Jam (Rp 12.000 / Kg)</option>
                                <option value="dryclean" <?= (($layanan ?? '') === 'dryclean') ? 'selected' : '' ?>>🧥 Dry Clean (Rp 15.000 / Kg)</option>
                                <option value="sepatu" <?= (($layanan ?? '') === 'sepatu') ? 'selected' : '' ?>>👟 Cuci Sepatu / Tas (Rp 25.000 / item)</option>
                                <option value="karpet" <?= (($layanan ?? '') === 'karpet') ? 'selected' : '' ?>>🛏️ Cuci Karpet / Bed Cover (Rp 30.000 / item)</option>
                                <option value="setrika" <?= (($layanan ?? '') === 'setrika') ? 'selected' : '' ?>>👕 Setrika Saja (Rp 4.000 / Kg)</option>
                            </select>
                            <div class="field-chevron">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Estimasi Berat -->
                    <div class="field-group">
                        <label class="field-label" for="berat">
                            Estimasi Berat (Kg) <span class="field-required">*</span>
                        </label>
                        <div class="field-input-wrap">
                            <div class="field-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l3 9a5.002 5.002 0 01-6.001 0M18 7l-3 9m-3-9l6-2m-6 2V4"/></svg>
                            </div>
                            <input type="number" step="0.1" min="0.1" class="field-input" name="berat" id="berat"
                                   placeholder="Contoh: 2.5" required
                                   value="<?= htmlspecialchars($berat ?? '1') ?>">
                        </div>
                        <p class="field-hint">Masukkan perkiraan berat pakaian (Kg). Untuk sepatu/karpet, masukkan jumlah item.</p>
                    </div>

                    <!-- Live Price Estimate Box -->
                    <div id="estimateCard" class="price-estimate-box" style="display: none;">
                        <div class="price-estimate-icon">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="price-estimate-content">
                            <div class="price-estimate-header">Estimasi Total Biaya</div>
                            <div class="price-estimate-amount" id="estimateTotal">Rp 0</div>
                            <div class="price-estimate-detail" id="estimateDetail">Pilih layanan dan berat untuk melihat perkiraan biaya</div>
                        </div>
                    </div>

                    <!-- Alamat Penjemputan -->
                    <div class="field-group">
                        <div class="field-label-row">
                            <label class="field-label" for="alamat">
                                Alamat Penjemputan <span class="field-required">*</span>
                            </label>
                            <span class="field-action-link" onclick="useCurrentLocation()">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Gunakan Lokasi Saya
                            </span>
                        </div>
                        <textarea class="field-input field-textarea" name="alamat" id="alamat" rows="3"
                                  placeholder="Tulis alamat lengkap penjemputan (nama jalan, nomor rumah, RT/RW, dan patokan)" required><?= htmlspecialchars($alamat ?? '') ?></textarea>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="field-group">
                        <label class="field-label" for="catatan">
                            Catatan Tambahan <span class="field-optional">(opsional)</span>
                        </label>
                        <textarea class="field-input field-textarea" name="catatan" id="catatan" rows="2"
                                  placeholder="Contoh: pakaian bayi mohon dipisah, parfum lavender, jemput setelah jam 1 siang"><?= htmlspecialchars($catatan ?? '') ?></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="order-buttons">
                        <button type="submit" name="submit" class="order-btn-submit">
                            <span>Kirim Pesanan</span>
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <button type="reset" class="order-btn-reset" onclick="resetFormUI()">
                            Bersihkan Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="order-quick-links">
                <a href="dashboard.php" class="order-quick-link">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Kembali ke Beranda
                </a>
                <a href="dashboard.php#kontak" class="order-quick-link">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Info Kontak &amp; Lokasi Outlet
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="order-footer">
        <div class="container">
            <div class="order-footer-brand">
                <div class="footer-brand-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="2" width="16" height="20" rx="3"></rect><circle cx="12" cy="13" r="5"></circle></svg>
                </div>
                Sparkleen<span class="font-editorial footer-accent">Laundry</span>
            </div>
            <p class="order-footer-tagline">Jasa laundry kilat, bersih, dan terpercaya untuk kebutuhan pakaian Anda sehari-hari.</p>
            <nav class="order-footer-nav">
                <a href="dashboard.php">Beranda</a>
                <a href="dashboard.php#layanan">Layanan</a>
                <a href="dashboard.php#tentang">Tentang Kami</a>
                <a href="dashboard.php#kontak">Kontak</a>
                <a href="pesanan.php" class="active">Form Pemesanan</a>
            </nav>
            <p class="order-footer-copy">&copy; <?= date('Y'); ?> Sparkleen Laundry — Tugas Web Programming | Kelompok Ganjil — TI C</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // === Live Price Calculation ===
        const tarifHarga = {
            "reguler": 6000,
            "kilat": 12000,
            "dryclean": 15000,
            "sepatu": 25000,
            "karpet": 30000,
            "setrika": 4000
        };

        function hitungEstimasi() {
            const layanan = document.getElementById('layanan').value;
            const berat = parseFloat(document.getElementById('berat').value) || 0;
            const card = document.getElementById('estimateCard');
            const totalEl = document.getElementById('estimateTotal');
            const detailEl = document.getElementById('estimateDetail');

            if (layanan && tarifHarga[layanan] && berat > 0) {
                const tarif = tarifHarga[layanan];
                const total = Math.round(tarif * berat);
                totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
                detailEl.textContent = 'Rincian: Rp ' + tarif.toLocaleString('id-ID') + ' × ' + berat + ' Kg';
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        }

        document.getElementById('layanan').addEventListener('change', hitungEstimasi);
        document.getElementById('berat').addEventListener('input', hitungEstimasi);
        window.addEventListener('DOMContentLoaded', hitungEstimasi);

        // === Geolocation ===
        function useCurrentLocation() {
            const alamatInput = document.getElementById('alamat');
            alamatInput.value = 'Mendeteksi lokasi...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        alamatInput.value = `Koordinat: ${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)} — Silakan lengkapi dengan detail alamat`;
                    },
                    () => {
                        alamatInput.value = '';
                        alamatInput.placeholder = 'Lokasi tidak terdeteksi. Silakan masukkan alamat manual.';
                    }
                );
            } else {
                alamatInput.value = '';
                alamatInput.placeholder = 'Geolocation tidak didukung browser Anda.';
            }
        }

        // === Reset Form UI ===
        function resetFormUI() {
            document.getElementById('estimateCard').style.display = 'none';
        }

        // === Mobile nav auto-close ===
        document.querySelectorAll('#navMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navMenu').classList.remove('show');
            });
        });
    </script>
</body>
</html>
