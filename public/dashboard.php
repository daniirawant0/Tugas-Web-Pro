<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sparkleen Laundry — Bersih, Wangi, Kilat</title>
    <link rel="stylesheet" href="../asset/style.css">
    <style>
        .price-tag {
            display: inline-block;
            background: var(--surface-ice-tint);
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
            padding: 4px 12px;
            border-radius: var(--rounded-full);
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">
                <span class="brand-icon">🧺</span>
                Sparkleen <span class="brand-accent">Laundry</span>
            </a>
            <button class="navbar-toggle" onclick="document.getElementById('navMenu').classList.toggle('show')">☰</button>
            <ul class="navbar-nav" id="navMenu">
                <li><a href="dashboard.php" class="active">Beranda</a></li>
                <li><a href="#layanan">Layanan &amp; Tarif</a></li>
                <li><a href="#tentang">Tentang Kami</a></li>
                <li><a href="#kontak">Kontak</a></li>
                <li><a href="pesanan.php">Form Pemesanan</a></li>
                <li><a href="../admin/login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="status-pill">
                <span class="dot"></span>
                Buka sekarang · tutup 17.00 WIB
            </div>
            <h1 class="hero-title" style="margin-top: 40px;">
                Jasa Laundry <em class="accent">Kilat</em>,<br>
                Bersih &amp; Wangi Tanpa Ribet
            </h1>
            <p class="hero-subtitle">
                Sparkleen Laundry menghadirkan layanan pencucian pakaian profesional dengan 
                proses kilat 6 jam, antar-jemput gratis, dan hasil yang terjamin higienis untuk keluarga Anda.
            </p>
            <div class="hero-buttons">
                <a href="#layanan" class="btn btn-primary">
                    Lihat Layanan &amp; Tarif <span class="btn-icon">↗</span>
                </a>
                <a href="pesanan.php" class="btn btn-secondary">
                    Pesan Sekarang <span class="btn-icon">↗</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="section" id="layanan">
        <div class="container">
            <h2 class="section-title text-center">Layanan &amp; Tarif Kami</h2>
            <p class="section-subtitle text-center">Harga transparan dan terjangkau sesuai kebutuhan Anda</p>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-card-icon">👔</div>
                    <div class="service-card-body">
                        <h3>Cuci Kering Setrika</h3>
                        <p>Layanan reguler dengan hasil bersih, wangi, dan rapi. Selesai dalam 2-3 hari kerja.</p>
                        <div class="price-tag">Rp 6.000 / Kg</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-icon">⚡</div>
                    <div class="service-card-body">
                        <h3>Laundry Kilat 6 Jam</h3>
                        <p>Butuh cepat? Pakaian Anda selesai dicuci, kering, dan disetrika hanya dalam 6 jam.</p>
                        <div class="price-tag">Rp 12.000 / Kg</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-icon">🧥</div>
                    <div class="service-card-body">
                        <h3>Dry Clean</h3>
                        <p>Perawatan khusus untuk jas, gaun, dan pakaian berbahan halus dengan teknik dry cleaning.</p>
                        <div class="price-tag">Rp 15.000 / Kg</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-icon">👟</div>
                    <div class="service-card-body">
                        <h3>Cuci Sepatu / Tas</h3>
                        <p>Cuci sepatu dan tas kesayangan Anda agar kembali bersih dan segar seperti baru.</p>
                        <div class="price-tag">Rp 25.000 / item</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-icon">🛏️</div>
                    <div class="service-card-body">
                        <h3>Cuci Karpet / Bed Cover</h3>
                        <p>Pembersihan mendalam untuk karpet, bed cover, dan item berukuran besar lainnya.</p>
                        <div class="price-tag">Rp 30.000 / item</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-icon">👕</div>
                    <div class="service-card-body">
                        <h3>Setrika Saja</h3>
                        <p>Hanya butuh setrika? Serahkan pada kami, hasilnya dijamin rapi dan wangi.</p>
                        <div class="price-tag">Rp 4.000 / Kg</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section>
        <div class="container">
            <div class="stats-banner">
                <div class="stat-item">
                    <div class="stat-number">547+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">324+</div>
                    <div class="stat-label">Pakaian Dicuci</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">145+</div>
                    <div class="stat-label">Pesanan Harian</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9+</div>
                    <div class="stat-label">Rating ★</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="section" id="tentang">
        <div class="container">
            <div style="max-width: 720px; margin: 0 auto; text-align: center;">
                <h2 class="section-title">Tentang Sparkleen Laundry</h2>
                <p style="font-size: 16px; color: var(--text-muted); line-height: 1.8; margin-top: var(--space-lg);">
                    Sparkleen Laundry adalah layanan laundry terpercaya yang mengutamakan kebersihan, 
                    kecepatan, dan kepuasan pelanggan. Dengan peralatan modern dan deterjen ramah lingkungan, 
                    kami memastikan setiap helai pakaian Anda ditangani dengan penuh perhatian. 
                    Layanan antar-jemput gratis tersedia untuk kemudahan Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- Cara Pesan Section -->
    <section class="section" style="background: var(--surface-ice-light);">
        <div class="container">
            <h2 class="section-title text-center">Cara Pesan</h2>
            <p class="section-subtitle text-center">Proses mudah dan cepat hanya dalam 4 langkah</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-lg); margin-top: var(--space-xl);">
                <div style="text-align: center;">
                    <div style="width: 56px; height: 56px; background: var(--tertiary); color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: var(--space-md);">1</div>
                    <h3 style="font-size: 18px; font-weight: 600;">Isi Form Pesanan</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin-top: var(--space-sm);">Pilih layanan dan estimasi berat pakaian Anda.</p>
                </div>
                <div style="text-align: center;">
                    <div style="width: 56px; height: 56px; background: var(--tertiary); color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: var(--space-md);">2</div>
                    <h3 style="font-size: 18px; font-weight: 600;">Penjemputan</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin-top: var(--space-sm);">Tim kami akan menjemput pakaian Anda di lokasi.</p>
                </div>
                <div style="text-align: center;">
                    <div style="width: 56px; height: 56px; background: var(--tertiary); color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: var(--space-md);">3</div>
                    <h3 style="font-size: 18px; font-weight: 600;">Proses Pencucian</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin-top: var(--space-sm);">Pakaian dicuci dengan peralatan modern dan deterjen premium.</p>
                </div>
                <div style="text-align: center;">
                    <div style="width: 56px; height: 56px; background: var(--tertiary); color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: var(--space-md);">4</div>
                    <h3 style="font-size: 18px; font-weight: 600;">Pengantaran</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin-top: var(--space-sm);">Pakaian bersih dan wangi diantar kembali ke rumah Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section class="section" id="kontak">
        <div class="container text-center">
            <h2 class="section-title">Hubungi Kami</h2>
            <p style="font-size: 16px; color: var(--text-muted); margin-top: var(--space-md);">
                📍 Jl. Contoh Alamat No. 123, Kota Anda<br>
                📞 0812-3456-7890<br>
                📧 info@sparkleenlaundry.com
            </p>
            <div style="margin-top: var(--space-xl);">
                <a href="pesanan.php" class="btn btn-blue">
                    Pesan Sekarang <span class="btn-icon">↗</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> <strong>Sparkleen Laundry</strong> — Bersih, Wangi, Kilat. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('#navMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navMenu').classList.remove('show');
            });
        });
    </script>
</body>
</html>
