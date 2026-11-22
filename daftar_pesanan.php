<?php
// daftar_pesanan.php - READ: Tampilkan semua pesanan (halaman manajemen admin)
// Serasi dengan desain Sparkleen Laundry (style.css yang sama)
require_once "koneksi.php";

$pesanan = [];
$result = $conn->query("SELECT * FROM pesanan ORDER BY tanggal_pesan DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pesanan[] = $row;
    }
}

$statusLabel = [
    "baru"         => "Baru",
    "diproses"     => "Diproses",
    "selesai"      => "Selesai",
    "dikirim"      => "Dikirim",
    "dibatalkan"   => "Dibatalkan",
];
$statusColor = [
    "baru"         => "#0a67d6",
    "diproses"     => "#7c3aed",
    "selesai"      => "#10b981",
    "dikirim"      => "#3b82f6",
    "dibatalkan"   => "#ef4444",
];
$statusIcon = [
    "baru"         => "inbox",
    "diproses"     => "timer",
    "selesai"      => "check-circle",
    "dikirim"      => "truck-fast",
    "dibatalkan"   => "x-circle",
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Pesanan - Sparkleen Laundry | Jasa Laundry Kilat</title>
  <meta name="description" content="Kelola pesanan laundry pelanggan: lihat daftar, perbarui status, dan hapus pesanan melalui sistem CRUD Sparkleen Laundry.">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%230a67d6'/%3E%3Cg fill='none' stroke='white' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='13' y='13' width='38' height='38' rx='6'/%3E%3Ccircle cx='32' cy='37' r='9'/%3E%3Ccircle cx='21' cy='21' r='2' fill='white' stroke='none'/%3E%3C/g%3E%3C/svg%3E">
  <script>
    (function () {
      var t = null;
      try { t = localStorage.getItem("sparkleen-theme"); } catch (e) {}
      if (!t) t = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
      document.documentElement.setAttribute("data-theme", t);
      document.documentElement.classList.add("js");
    })();
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="https://unpkg.com/reicon/cdn/reicon.js"></script>
  <style>
    /* Tabel pesanan serasi dengan card Sparkleen */
    .tabel-card {
      background: var(--card);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-md);
    }
    .tabel-card table {
      width: 100%;
      border-collapse: collapse;
      font-family: "Geist", sans-serif;
      font-size: 0.92rem;
    }
    .tabel-card th {
      background: var(--chip-bg);
      color: var(--text-2);
      text-align: left;
      padding: 14px 18px;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      border-bottom: 1px solid var(--card-border);
    }
    .tabel-card td {
      padding: 16px 18px;
      border-bottom: 1px solid var(--hairline);
      color: var(--text);
      vertical-align: middle;
    }
    .tabel-card tr:last-child td {
      border-bottom: none;
    }
    .tabel-card tr:hover td {
      background: var(--chip-bg);
      transition: background 0.2s ease;
    }
    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: var(--radius-full);
      font-size: 0.82rem;
      font-weight: 600;
      background: rgba(0,0,0,0.06);
      color: var(--text);
      border: 1px solid var(--hairline);
    }
    .status-chip strong {
      color: var(--warna, var(--accent));
    }
    .tabel-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .tabel-card table { min-width: 880px; }
    .kosong-placeholder {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-2);
    }
    .kosong-placeholder re-icon {
      display: flex;
      justify-content: center;
      width: 48px; height: 48px;
      margin: 0 auto 16px;
      color: var(--text-3);
    }
    .aksi-group {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    .btn-status-sm {
      padding: 6px 14px;
      font-size: 0.82rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--hairline);
      background: var(--card);
      color: var(--text);
      cursor: pointer;
      vertical-align: middle;
      transition: all 0.2s ease;
      font-family: inherit;
    }
    .btn-status-sm:hover {
      border-color: var(--accent);
      color: var(--accent);
    }
    .btn-hapus {
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 6px;
      border-radius: var(--radius-md);
      color: var(--text-2);
      transition: all 0.2s ease;
    }
    .btn-hapus:hover {
      background: rgba(239, 68, 68, 0.10);
      color: #ef4444;
    }
    .ringkasan-stat {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .stat-box {
      flex: 1;
      min-width: 140px;
      background: var(--card);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-lg);
      padding: 20px 16px;
      text-align: center;
      box-shadow: var(--shadow-sm);
    }
    .stat-box .nilai {
      display: block;
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 6px;
      font-family: "Instrument Serif", serif;
    }
    .stat-box .label {
      font-size: 0.85rem;
      color: var(--text-2);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
  </style>
</head>
<body>

  <a class="skip-link" href="#konten">Langsung ke konten utama</a>

  <!-- ============ LATAR DITHER PIKSEL ============ -->
  <div class="page-bg" aria-hidden="true">
    <div class="pg-cloud pg-cloud-1"></div>
    <div class="pg-cloud pg-cloud-2"></div>
    <div class="pg-dither pg-dither-halus"></div>
    <div class="pg-dither pg-dither-kasar"></div>
  </div>

  <!-- ============ NAVBAR ============ -->
  <nav class="navbar" id="navbar" aria-label="Navigasi utama">
    <div class="nav-inner">
      <a href="index.html" class="logo" aria-label="Sparkleen Laundry — kembali ke beranda">
        <span class="logo-mark" aria-hidden="true"><re-icon icon="washer" size="20" color="currentColor"></re-icon></span>
        Sparkleen<span class="logo-accent">Laundry</span>
      </a>
      <ul class="nav-links">
        <li><a href="index.html">Beranda</a></li>
        <li><a href="layanan.html">Layanan</a></li>
        <li><a href="tentang.html">Tentang Kami</a></li>
        <li><a href="tips.html">Tips &amp; Perawatan</a></li>
        <li><a href="kontak.html">Kontak</a></li>
        <li><a href="pemesanan.html">Form Pemesanan</a></li>
        <li><a href="daftar_pesanan.php" class="aktif" aria-current="page">Daftar Pesanan</a></li>
      </ul>
      <div class="nav-actions">
        <span class="status-pill nav-status" id="statusPill" aria-live="polite"></span>
        <button type="button" class="theme-toggle" id="themeToggle" aria-label="Ganti mode terang/gelap">
          <re-icon class="ico-moon" icon="moon" size="16" color="currentColor" aria-hidden="true"></re-icon>
          <re-icon class="ico-sun" icon="sun" size="16" color="currentColor" aria-hidden="true"></re-icon>
        </button>
        <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobileMenu">
          <span></span><span></span>
        </button>
      </div>
    </div>
  </nav>

  <!-- ============ MENU MOBILE ============ -->
  <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <nav aria-label="Menu navigasi mobile">
      <ul>
        <li><a href="index.html"><span class="no">01</span> Beranda</a></li>
        <li><a href="layanan.html"><span class="no">02</span> Layanan</a></li>
        <li><a href="tentang.html"><span class="no">03</span> Tentang Kami</a></li>
        <li><a href="tips.html"><span class="no">04</span> Tips &amp; Perawatan</a></li>
        <li><a href="kontak.html"><span class="no">05</span> Kontak</a></li>
        <li><a href="pemesanan.html"><span class="no">06</span> Form Pemesanan</a></li>
        <li><a href="daftar_pesanan.php"><span class="no">07</span> Daftar Pesanan</a></li>
      </ul>
    </nav>
    <div class="mm-footer">
      <span class="status-pill" id="statusPillMobile" aria-live="polite"></span>
      <p>Sistem manajemen pesanan CRUD — Sparkleen Laundry.</p>
    </div>
  </div>

  <main id="konten">

    <!-- ============ HERO ============ -->
    <header class="hero alt">
      <div class="hero-inner">
        <h1>Manajemen <span class="grad">Pesanan</span></h1>
        <p class="subjudul">Kelola pesanan laundry pelanggan: lihat daftar, perbarui status, dan hapus data pesanan melalui sistem CRUD.</p>
      </div>
    </header>

    <!-- ============ RINGKASAN STAT ============ -->
    <section class="section">
      <div class="container">
        <div class="ringkasan-stat reveal" style="--d:0s">
          <div class="stat-box">
            <span class="nilai"><?= count($pesanan) ?></span>
            <span class="label">Total Pesanan</span>
          </div>
          <div class="stat-box">
            <span class="nilai" style="color:#f59e0b;">
              <?= array_reduce($pesanan, function($c, $p){ return $c + ($p["status"]==="baru" ? 1:0); }, 0) ?>
            </span>
            <span class="label">Baru</span>
          </div>
          <div class="stat-box">
            <span class="nilai" style="color:#10b981;">
              <?= array_reduce($pesanan, function($c, $p){ return $c + ($p["status"]==="selesai" ? 1:0); }, 0) ?>
            </span>
            <span class="label">Selesai</span>
          </div>
          <div class="stat-box">
            <span class="nilai" style="color:#ef4444;">
              <?= array_reduce($pesanan, function($c, $p){ return $c + ($p["status"]==="dibatalkan" ? 1:0); }, 0) ?>
            </span>
            <span class="label">Dibatalkan</span>
          </div>
        </div>

        <!-- ============ DAFTAR PESANAN ============ -->
        <div class="section-head reveal" style="--d:0.1s">
          <span class="badge">CRUD Pesanan</span>
          <h2>Daftar Semua Pesanan</h2>
          <p class="deskripsi">Kelola setiap pesanan dari pelanggan Sparkleen Laundry. Ubah status dengan dropdown, atau hapus data yang tidak diperlukan.</p>
        </div>

        <div class="tabel-card reveal" style="--d:0.2s">
          <?php if (empty($pesanan)): ?>
            <div class="kosong-placeholder">
              <re-icon icon="inbox" size="48" color="currentColor" aria-hidden="true"></re-icon>
              <h3>Belum Ada Pesanan</h3>
              <p>Data pesanan masih kosong. Ayo terima pesanan pertama melalui halaman <a href="kontak.html" style="color:var(--accent); text-decoration:underline;">Kontak</a>.</p>
            </div>
          <?php else: ?>
            <div class="tabel-scroll">
            <table>
              <thead>
                <tr>
                  <th style="width:6%;">ID</th>
                  <th style="width:18%;">Nama Pelanggan</th>
                  <th style="width:16%;">Telepon</th>
                  <th style="width:16%;">Layanan</th>
                  <th style="width:22%;">Alamat Penjemputan</th>
                  <th style="width:10%;">Tanggal</th>
                  <th style="width:12%;">Status</th>
                  <th style="width:10%;">Total</th>
                  <th style="width:10%;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pesanan as $p): ?>
                  <tr>
                    <td>#<?= htmlspecialchars($p["id"]) ?></td>
                    <td><?= htmlspecialchars($p["nama"]) ?></td>
                    <td><?= htmlspecialchars($p["telepon"]) ?></td>
                    <td><?= htmlspecialchars($p["layanan"]) ?></td>
                    <td><?= htmlspecialchars($p["alamat"]) ?></td>
                    <td><?= htmlspecialchars($p["tanggal_pesan"]) ?></td>
                    <td>
                      <span class="status-chip" style="--warna:<?= $statusColor[$p["status"]] ?? "#0a67d6"; ?>">
                        <re-icon icon="<?= $statusIcon[$p["status"]] ?? "inbox"; ?>" size="13" color="<?= $statusColor[$p["status"]] ?? "#0a67d6"; ?>" aria-hidden="true"></re-icon>
                        <strong><?= htmlspecialchars($statusLabel[$p["status"]] ?? $p["status"]) ?></strong>
                      </span>
                    </td>
                    <td>Rp <?= number_format($p["total_harga"], 0, ",", ".") ?></td>
                    <td>
                      <div class="aksi-group">
                        <form action="update_status.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Ubah status pesanan #<?= $p["id"] ?>?')">
                          <input type="hidden" name="id" value="<?= $p["id"] ?>">
                          <select name="status" onchange="this.form.submit()" class="btn-status-sm">
                            <?php foreach ($statusLabel as $k => $v): ?>
                              <option value="<?= $k ?>" <?= $p["status"] === $k ? "selected" : "" ?>><?= $v ?></option>
                            <?php endforeach; ?>
                          </select>
                        </form>
                        <form action="hapus_pesanan.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Yakin ingin menghapus pesanan #<?= $p["id"] ?>? Tindakan ini tidak dapat dibatalkan.')">
                          <input type="hidden" name="id" value="<?= $p["id"] ?>">
                          <button type="submit" class="btn-hapus" title="Hapus pesanan" aria-label="Hapus pesanan #<?= $p["id"] ?>">
                            <re-icon icon="trash" size="15" color="#ef4444" aria-hidden="true"></re-icon>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            </div>
          <?php endif; ?>
        </div>

        <div class="form-actions reveal" style="--d:0.3s; justify-content: center; margin-top: 30px;">
          <a href="kontak.html" class="btn btn-primary">
            <re-icon icon="washer" size="16" color="currentColor" aria-hidden="true"></re-icon>
            Buat Pesanan Baru
          </a>
          <a href="index.html" class="btn btn-ghost btn-sm">
            <re-icon icon="arrow-left" size="16" color="currentColor" aria-hidden="true"></re-icon>
            Kembali ke Beranda
          </a>
        </div>
      </div>
    </section>
  </main>

  <!-- ============ FOOTER ============ -->
  <footer>
    <div class="container">
      <div class="footer-logo"><span class="logo-mark" aria-hidden="true"><re-icon icon="washer" size="20" color="currentColor"></re-icon></span> Sparkleen<span class="logo-accent">Laundry</span></div>
      <p>Sistem manajemen pesanan CRUD — PHP + MySQL | Tugas 2 Web Programming</p>
      <nav class="footer-nav" aria-label="Navigasi footer">
        <a href="index.html">Beranda</a>
        <a href="layanan.html">Layanan</a>
        <a href="tentang.html">Tentang Kami</a>
        <a href="tips.html">Tips &amp; Perawatan</a>
        <a href="kontak.html">Kontak</a>
        <a href="pemesanan.html">Form Pemesanan</a>
        <a href="daftar_pesanan.php">Daftar Pesanan</a>
      </nav>
      <p class="copyright">&copy; <span id="tahunFooter">2026</span> Sparkleen Laundry — Kelompok Ganjil — TI C</p>
    </div>
  </footer>

  <button type="button" class="to-top" id="toTop" aria-label="Kembali ke atas halaman">
    <re-icon icon="arrow-up" size="17" color="currentColor" aria-hidden="true"></re-icon>
  </button>

  <script src="script.js"></script>
  <script>
    // Notifikasi flash setelah update/hapus status
    const params = new URLSearchParams(window.location.search);
    if (params.has("updated") || params.has("deleted")) {
      const notif = document.createElement("div");
      notif.style.cssText = "position:fixed; top:20px; right:20px; background:var(--accent); color:#fff; padding:14px 22px; border-radius:var(--radius-lg); z-index:9999; box-shadow:var(--shadow-lg); font-family:'Geist',sans-serif; font-size:0.95rem; border:1px solid rgba(255,255,255,0.2);";
      notif.textContent = params.has("updated") ? "Status pesanan berhasil diperbarui!" : "Pesanan berhasil dihapus!";
      document.body.appendChild(notif);
      setTimeout(() => { notif.style.opacity="0"; notif.style.transition="opacity 0.3s ease"; setTimeout(()=>notif.remove(), 300); }, 2500);
      window.history.replaceState({}, "", window.location.pathname);
    }
  </script>
</body>
</html>
