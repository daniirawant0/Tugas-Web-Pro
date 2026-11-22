/* ============================================================
   SPARKLEEN LAUNDRY — script.js (Tugas 2: Konten Dinamis)
   Kelompok Ganjil — TI C — Web Programming

   Integrasi HTML + CSS + JavaScript:
   01. Manajemen tema terang/gelap  (localStorage + prefers-color-scheme)
   02. Navbar pil mengambang + menu mobile
   03. Status buka/tutup live (zona waktu Asia/Jakarta)
   04. Sapaan & tanggal dinamis di hero
   05. Animasi reveal saat scroll (IntersectionObserver, gaya flow-in)
   06. Statistik count-up
   07. Countdown promo (akhir bulan berjalan, otomatis lanjut)
   08. Carousel testimoni (auto, swipe, dot, keyboard)
   09. Kalkulator estimasi harga + pesan WhatsApp otomatis
   10. Filter kartu layanan
   11. Akordeon FAQ
   12. Segmented control alur kerja (Tentang)
   13. Tips: render dinamis, cari, filter kategori, tips acak
   14. Form kontak: validasi inline + panel sukses + WhatsApp
   15. Kartu anggota kelompok dinamis + pencarian
   16. Tombol ke atas & tahun footer otomatis
   17. Motion andreas.vision: word-reveal judul hero (kata demi kata)
   18. Motion andreas.vision: tombol magnetik mengikuti kursor
   Semua fitur menghormati prefers-reduced-motion.
   ============================================================ */

(function () {
  "use strict";

  /* ============ UTIL ============ */
  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const rupiah = (n) =>
    "Rp" + Math.round(n).toLocaleString("id-ID");

  const tglPanjang = (d) =>
    new Intl.DateTimeFormat("id-ID", {
      weekday: "long", day: "numeric", month: "long", year: "numeric",
      timeZone: "Asia/Jakarta"
    }).format(d);

  const WA_ADMIN = "6281234567890"; // 0812-3456-7890 (Admin 1)

  /* Waktu Jakarta saat ini: { hari, jam, menit } */
  function jakartaSekarang() {
    const parts = {};
    new Intl.DateTimeFormat("en-US", {
      timeZone: "Asia/Jakarta", weekday: "short",
      hour: "numeric", minute: "numeric", hour12: false
    }).formatToParts(new Date()).forEach((p) => { parts[p.type] = p.value; });
    return {
      hari: parts.weekday,                                  // "Mon".."Sun"
      jam: Number(parts.hour) % 24,
      menit: Number(parts.minute)
    };
  }

  /* ============ 01. TEMA TERANG/GELAP ============ */
  function initTema() {
    const btn = $("#themeToggle");
    if (!btn) return;
    const root = document.documentElement;

    const sinkronkan = () => {
      const gelap = root.getAttribute("data-theme") === "dark";
      btn.setAttribute("aria-label", gelap ? "Aktifkan mode terang" : "Aktifkan mode gelap");
      btn.setAttribute("aria-pressed", gelap ? "true" : "false");
    };

    btn.addEventListener("click", () => {
      const baru = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
      root.classList.add("theme-fade");
      root.setAttribute("data-theme", baru);
      try { localStorage.setItem("sparkleen-theme", baru); } catch (e) { /* private mode */ }
      sinkronkan();
      setTimeout(() => root.classList.remove("theme-fade"), 500);
    });
    sinkronkan();
  }

  /* ============ 02. NAVBAR + MENU MOBILE ============ */
  function initNavbar() {
    const nav = $("#navbar");
    if (nav) {
      const onScroll = () => nav.classList.toggle("scrolled", window.scrollY > 8);
      onScroll();
      window.addEventListener("scroll", onScroll, { passive: true });
    }

    const toggle = $("#menuToggle");
    if (toggle) {
      const menu = $("#mobileMenu");
      const tutup = () => {
        document.body.classList.remove("menu-open");
        toggle.setAttribute("aria-expanded", "false");
        menu.setAttribute("aria-hidden", "true");
      };
      toggle.addEventListener("click", () => {
        const buka = !document.body.classList.contains("menu-open");
        document.body.classList.toggle("menu-open", buka);
        toggle.setAttribute("aria-expanded", String(buka));
        menu.setAttribute("aria-hidden", String(!buka));
      });
      $$("#mobileMenu a").forEach((a) => a.addEventListener("click", tutup));
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && document.body.classList.contains("menu-open")) {
          tutup();
          toggle.focus();
        }
      });
    }
  }

  /* ============ 03. STATUS BUKA/TUTUP ============ */
  function initStatusBuka() {
    const jadwal = {
      Mon: [7, 21], Tue: [7, 21], Wed: [7, 21], Thu: [7, 21], Fri: [7, 21], Sat: [7, 21],
      Sun: [8, 17]
    };
    const namaHari = { Mon: "Senin", Tue: "Selasa", Wed: "Rabu", Thu: "Kamis", Fri: "Jumat", Sat: "Sabtu", Sun: "Minggu" };

    const pills = $$("#statusPill, #statusPillMobile");
    const heroKicker = $("#heroKicker");

    function perbarui() {
      const { hari, jam, menit } = jakartaSekarang();
      const [bukaJam, tutupJam] = jadwal[hari];
      const sekarang = jam + menit / 60;
      const buka = sekarang >= bukaJam && sekarang < tutupJam;

      let status, detail;
      if (buka) {
        status = "Buka";
        detail = "tutup " + String(tutupJam).padStart(2, "0") + ".00 WIB";
      } else if (sekarang < bukaJam) {
        status = "Tutup";
        detail = "buka hari ini " + String(bukaJam).padStart(2, "0") + ".00 WIB";
      } else {
        const urut = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const besok = urut[(urut.indexOf(hari) + 1) % 7];
        status = "Tutup";
        detail = "buka " + namaHari[besok] + " " + String(jadwal[besok][0]).padStart(2, "0") + ".00 WIB";
      }

      if (pills.length) {
        pills.forEach((pill) => {
          // Hapus status lama & tambah status baru TANPA menimpa class lain
          // (class "nav-status" harus tetap ada agar disembunyikan di layar kecil)
          pill.classList.remove("buka", "tutup");
          pill.classList.add(buka ? "buka" : "tutup");
          pill.innerHTML =
            '<span class="status-dot" aria-hidden="true"></span>' +
            '<span class="status-text">' + status + " · " + detail + "</span>";
          pill.title = "Status operasional Sparkleen Laundry saat ini";
        });
      }
      if (heroKicker) {
        $("#heroKickerText").textContent = status + " sekarang · " + detail;
        const dot = $("#heroKickerDot");
        if (dot) dot.className = "status-dot " + (buka ? "" : "tutup");
      }
    }

    if (pills.length || heroKicker) {
      perbarui();
      setInterval(perbarui, 60000); // periksa ulang tiap menit
    }
  }

  /* ============ 04. SAPAAN & TANGGAL DINAMIS ============ */
  function initSapaan() {
    const el = $("#heroTanggal");
    if (!el) return;
    const { jam } = jakartaSekarang();
    let sapa;
    if (jam < 11) sapa = "Selamat pagi";
    else if (jam < 15) sapa = "Selamat siang";
    else if (jam < 18.5) sapa = "Selamat sore";
    else sapa = "Selamat malam";
    el.textContent = sapa + " · " + tglPanjang(new Date());
  }

  /* ============ 05. REVEAL ON SCROLL ============ */
  function initReveal() {
    const targets = $$(".reveal");
    if (!targets.length) return;
    if (reducedMotion || !("IntersectionObserver" in window)) {
      targets.forEach((el) => el.classList.add("in"));
      return;
    }
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) {
          en.target.classList.add("in");
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.14, rootMargin: "0px 0px -40px 0px" });
    targets.forEach((el) => io.observe(el));
  }

  /* ============ 06. STATISTIK COUNT-UP ============ */
  function initCounter() {
    const nums = $$("[data-count]");
    if (!nums.length) return;

    const format = (val, dec) =>
      dec > 0
        ? val.toLocaleString("id-ID", { minimumFractionDigits: dec, maximumFractionDigits: dec })
        : Math.round(val).toLocaleString("id-ID");

    const jalan = (el) => {
      const target = parseFloat(el.dataset.count);
      const dec = Number(el.dataset.decimals || 0);
      const suffix = el.dataset.suffix || "";
      if (reducedMotion) {
        el.textContent = format(target, dec) + suffix;
        return;
      }
      const durasi = 1600;
      const mulai = performance.now();
      const langkah = (t) => {
        const p = Math.min((t - mulai) / durasi, 1);
        const ease = 1 - Math.pow(1 - p, 3); // ease-out cubic
        el.textContent = format(target * ease, dec) + suffix;
        if (p < 1) requestAnimationFrame(langkah);
      };
      requestAnimationFrame(langkah);
    };

    if (!("IntersectionObserver" in window)) { nums.forEach(jalan); return; }
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) { jalan(en.target); io.unobserve(en.target); }
      });
    }, { threshold: 0.4 });
    nums.forEach((el) => io.observe(el));
  }

  /* ============ 07. COUNTDOWN PROMO ============ */
  function initCountdown() {
    const box = $("#promoCountdown");
    if (!box) return;
    const nums = {
      hari: $("#cdHari"), jam: $("#cdJam"),
      menit: $("#cdMenit"), detik: $("#cdDetik")
    };
    if (!nums.hari) return;

    let target = akhirBulan(new Date());

    function akhirBulan(d) {
      return new Date(d.getFullYear(), d.getMonth() + 1, 0, 23, 59, 59);
    }

    function render() {
      let beda = target - Date.now();
      if (beda <= 0) {
        const lanjut = new Date(target.getFullYear(), target.getMonth() + 2, 0, 23, 59, 59);
        target = lanjut;
        beda = target - Date.now();
      }
      const s = Math.floor(beda / 1000);
      const d = Math.floor(s / 86400);
      const h = Math.floor((s % 86400) / 3600);
      const m = Math.floor((s % 3600) / 60);
      const det = s % 60;
      nums.hari.textContent = String(d).padStart(2, "0");
      nums.jam.textContent = String(h).padStart(2, "0");
      nums.menit.textContent = String(m).padStart(2, "0");
      nums.detik.textContent = String(det).padStart(2, "0");
    }

    render();
    setInterval(render, 1000);
  }

  /* ============ 08. CAROUSEL TESTIMONI ============ */
  function initTestimoni() {
    const mount = $("#testiCarousel");
    if (!mount) return;

    const TESTI = [
      { nama: "Rina Sartika", peran: "Ibu rumah tangga · Bandung", bintang: 5, awa: "RS", warna: "tile-1",
        teks: "Antar-jemputnya benar-benar tepat waktu. Saya cuman chat jam 8 pagi, siangnya pakaian sudah dijemput, dan besoknya datang dalam keadaan rapi plus wangi. Anak saya yang bayi juga aman pakai deterjennya, tidak ada iritasi sama sekali." },
      { nama: "Budi Pratama", peran: "Karyawan swasta", bintang: 5, awa: "BP", warna: "tile-2",
        teks: "Layanan kilat 6 jam-nya penyelamat banget. Kemeja rapat penting kena kopi jam 7 pagi, saya kirim ke Sparkleen, jam 3 sore sudah wangi dan rapi. Harganya masuk akal untuk kebutuhan mendadak seperti itu." },
      { nama: "Maya Anggraini", peran: "Mahasiswa", bintang: 5, awa: "MA", warna: "tile-5",
        teks: "Sebagai mahasiswa kos, harga Rp6.000 per kilo benar-benar bersahabat dengan kantong bulanan. Paket mingguannya bikin lebih hemat lagi, dan hasilnya konsisten bersih setiap kali." },
      { nama: "Dimas Prasetyo", peran: "Pemilik kos-kosan", bintang: 5, awa: "DP", warna: "tile-3",
        teks: "Saya rutin cuci bed cover dan karpet 12 kamar kos di sini. Mesinnya besar, hasilnya benar-benar bersih dan bebas tungau, plus dikirim kembali rapi. Prosesnya juga transparan dari nota digital sampai pengiriman." },
      { nama: "Sari Wulandari", peran: "Pelanggan dry clean", bintang: 5, awa: "SW", warna: "tile-7",
        teks: "Kebaya pengantin hasil dry clean-nya keren, warna tetap cerah dan payetnya tidak ada yang lepas. Timnya juga memeriksa kondisi bahan sebelum proses. Recommended banget untuk pakaian formal." },
      { nama: "Agus Wijaya", peran: "Kolektor sneakers", bintang: 4, awa: "AW", warna: "tile-8",
        teks: "Sneakers putih langka saya dibilang aman untuk dibersihkan, dan hasilnya luar biasa: sol kembali putih, bentuk tidak berubah, tidak ada bekas sablon yang rusak. Komunikasi adminnya juga cepat dan sopan." }
    ];

    const track = $(".carousel-track", mount);
    const dotsBox = $(".carousel-dots", mount);
    const prev = $(".carousel-prev", mount);
    const next = $(".carousel-next", mount);
    let indeks = 0;
    let timer = null;

    const bintangStr = (n) => Array.from({ length: 5 }, (_, i) =>
      `<re-icon icon="star5"${i < n ? ' weight="filled"' : ''} size="15" color="${i < n ? "#f5a623" : "var(--text-3)"}" aria-hidden="true"></re-icon>`
    ).join("");

    track.innerHTML = TESTI.map((t) => `
      <div class="slide" role="group" aria-label="Testimoni dari ${t.nama}">
        <figure class="testi-card">
          <div class="testi-stars" aria-label="Rating ${t.bintang} dari 5">${bintangStr(t.bintang)}</div>
          <blockquote>“${t.teks}”</blockquote>
          <figcaption>
            <div class="testi-ava ${t.warna}" aria-hidden="true">${t.awa}</div>
            <div class="testi-nama">${t.nama}</div>
            <div class="testi-role">${t.peran}</div>
          </figcaption>
        </figure>
      </div>`).join("");

    dotsBox.innerHTML = TESTI.map((_, i) =>
      `<button type="button" aria-label="Ke testimoni ${i + 1}"></button>`).join("");
    const dots = $$("button", dotsBox);

    function tampil(i) {
      indeks = (i + TESTI.length) % TESTI.length;
      track.style.transform = "translateX(-" + indeks * 100 + "%)";
      dots.forEach((d, di) => {
        d.classList.toggle("on", di === indeks);
        d.setAttribute("aria-current", di === indeks ? "true" : "false");
      });
    }

    prev.addEventListener("click", () => { tampil(indeks - 1); mulai(); });
    next.addEventListener("click", () => { tampil(indeks + 1); mulai(); });
    dots.forEach((d, di) => d.addEventListener("click", () => { tampil(di); mulai(); }));

    // Keyboard: panah kiri/kanan saat fokus di dalam carousel
    mount.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") { tampil(indeks - 1); mulai(); }
      if (e.key === "ArrowRight") { tampil(indeks + 1); mulai(); }
    });

    // Swipe di layar sentuh
    let mulaiX = null;
    track.addEventListener("pointerdown", (e) => { mulaiX = e.clientX; });
    track.addEventListener("pointerup", (e) => {
      if (mulaiX === null) return;
      const beda = e.clientX - mulaiX;
      if (Math.abs(beda) > 42) { tampil(indeks + (beda < 0 ? 1 : -1)); mulai(); }
      mulaiX = null;
    });

    function mulai() {
      if (reducedMotion) return;
      clearInterval(timer);
      timer = setInterval(() => tampil(indeks + 1), 6000);
    }
    function jeda() { clearInterval(timer); }

    mount.addEventListener("mouseenter", jeda);
    mount.addEventListener("focusin", jeda);
    mount.addEventListener("mouseleave", mulai);
    mount.addEventListener("focusout", mulai);

    tampil(0);
    mulai();
  }

  /* ============ 09. KALKULATOR ESTIMASI HARGA ============ */
  function initKalkulator() {
    const box = $("#kalkulator");
    if (!box) return;

    const LAYANAN = {
      reguler:  { nama: "Cuci Kering Setrika Reguler", harga: 6000,  unit: "kg",    step: 0.5, min: 0.5, hari: 2, member: true,  kat: "Cuci reguler" },
      kilat:    { nama: "Laundry Kilat 6 Jam",         harga: 12000, unit: "kg",    step: 0.5, min: 0.5, hari: 0, member: false, kat: "Express" },
      setrika:  { nama: "Setrika Saja",                harga: 4000,  unit: "kg",    step: 0.5, min: 0.5, hari: 1, member: true,  kat: "Cuci reguler" },
      karpet:   { nama: "Cuci Karpet",                 harga: 15000, unit: "kg",    step: 0.5, min: 0.5, hari: 4, member: true,  kat: "Rumah tangga" },
      dryclean: { nama: "Dry Clean (Jas, Gaun, Kebaya)", harga: 25000, unit: "pcs",  step: 1,   min: 1,   hari: 3, member: false, kat: "Premium" },
      sepatu:   { nama: "Cuci Sepatu",                harga: 20000, unit: "pasang", step: 1,  min: 1,   hari: 2, member: false, kat: "Spesialis" },
      tas:      { nama: "Cuci Tas",                    harga: 25000, unit: "pcs",  step: 1,   min: 1,   hari: 3, member: false, kat: "Spesialis" },
      bedcover: { nama: "Cuci Bed Cover (King)",       harga: 45000, unit: "pcs",  step: 1,   min: 1,   hari: 3, member: false, kat: "Rumah tangga" }
    };

    const sel = $("#kalkLayanan");
    const inp = $("#kalkJumlah");
    const cekAntar = $("#kalkAntar");
    const cekMember = $("#kalkMember");
    const unitLabel = $("#kalkUnit");
    const hintAntar = $("#hintAntar");
    const hintMember = $("#hintMember");

    const ui = {
      layanan: $("#rLayanan"), jumlah: $("#rJumlah"), satuan: $("#rSatuan"),
      subtotal: $("#rSubtotal"), diskon: $("#rDiskon"), diskonBaris: $("#barisDiskon"),
      total: $("#rTotal"), est: $("#rEst"), antarBaris: $("#barisAntar"),
      antarVal: $("#rAntar"), wa: $("#kalkWa")
    };

    function isiPilihan() {
      sel.innerHTML = Object.entries(LAYANAN).map(([k, v]) =>
        `<option value="${k}">${v.nama} — ${rupiah(v.harga)}/${v.unit}</option>`).join("");
    }

    function estimasiSelesai(hari) {
      if (hari === 0) {
        const selesaiJam = new Date(Date.now() + 6 * 3600 * 1000);
        const jamStr = new Intl.DateTimeFormat("id-ID", {
          timeZone: "Asia/Jakarta", hour: "2-digit", minute: "2-digit", hour12: false
        }).format(selesaiJam);
        return "Kilat: selesai sekitar pukul " + jamStr.replace(".", ":") + " WIB hari ini";
      }
      const tgl = new Date(Date.now() + hari * 24 * 3600 * 1000);
      return "Perkiraan selesai: " + tglPanjang(tgl);
    }

    function hitung() {
      const data = LAYANAN[sel.value];
      let jumlah = parseFloat(inp.value);
      if (isNaN(jumlah) || jumlah < data.min) jumlah = data.min;
      inp.step = data.step;
      inp.min = data.min;
      if (data.step === 1) inp.setAttribute("inputmode", "numeric");
      else inp.removeAttribute("inputmode");

      unitLabel.textContent = data.unit;

      const subtotal = data.harga * jumlah;
      const memberAktif = cekMember.checked && data.member;
      const diskon = memberAktif ? subtotal * 0.1 : 0;
      const total = subtotal - diskon;

      // Petunjuk dinamis (feedback di antarmuka, bukan alert)
      hintMember.classList.toggle("show", cekMember.checked && !data.member);
      const antarTidakGratis = cekAntar.checked && subtotal < 30000;
      hintAntar.classList.toggle("show", antarTidakGratis);

      ui.layanan.textContent = data.nama;
      ui.jumlah.textContent = jumlah.toLocaleString("id-ID") + " " + data.unit;
      ui.satuan.textContent = rupiah(data.harga) + " / " + data.unit;
      ui.subtotal.textContent = rupiah(subtotal);
      ui.diskonBaris.style.display = diskon > 0 ? "" : "none";
      ui.diskon.textContent = "−" + rupiah(diskon);
      ui.total.textContent = rupiah(total);
      ui.est.textContent = estimasiSelesai(data.hari);
      ui.antarBaris.style.display = cekAntar.checked ? "" : "none";
      ui.antarVal.textContent = antarTidakGratis
        ? "Perlu konfirmasi admin"
        : "Gratis (radius 5 km)";

      // Pesan WhatsApp otomatis dari data kalkulator
      const pesan =
        "Halo Sparkleen Laundry! Saya ingin memesan layanan berikut:" +
        "\n• Layanan: " + data.nama +
        "\n• Jumlah: " + jumlah.toLocaleString("id-ID") + " " + data.unit +
        "\n• Harga satuan: " + rupiah(data.harga) + "/" + data.unit +
        "\n• Subtotal: " + rupiah(subtotal) +
        (diskon > 0 ? "\n• Diskon member (10%): −" + rupiah(diskon) : "") +
        "\n• Total estimasi: " + rupiah(total) +
        "\n• Antar-jemput: " + (cekAntar.checked ? (antarTidakGratis ? "perlu konfirmasi" : "gratis") : "tidak perlu") +
        "\n• " + estimasiSelesai(data.hari) +
        "\nTerima kasih!";
      ui.wa.href = "https://wa.me/" + WA_ADMIN + "?text=" + encodeURIComponent(pesan);
    }

    isiPilihan();
    [sel, inp, cekAntar, cekMember].forEach((el) => el.addEventListener("input", hitung));
    hitung();
  }

  /* ============ 10. FILTER KARTU LAYANAN ============ */
  function initFilterLayanan() {
    const chips = $$(".chips-layanan .chip");
    if (!chips.length) return;
    const kartu = $$("#gridLayanan .card");
    const hitungEl = $("#layananCount");

    chips.forEach((chip) => chip.addEventListener("click", () => {
      chips.forEach((c) => { c.classList.remove("on"); c.setAttribute("aria-pressed", "false"); });
      chip.classList.add("on");
      chip.setAttribute("aria-pressed", "true");
      const f = chip.dataset.filter;
      let n = 0;
      kartu.forEach((k) => {
        const cocok = f === "semua" || k.dataset.kategori === f;
        k.classList.toggle("hide", !cocok);
        if (cocok) n++;
        k.style.setProperty("--d", (n % 3) * 0.07 + "s");
      });
      const label = { semua: "semua layanan", kg: "layanan per kilogram", pcs: "layanan per potongan", gratis: "layanan gratis" }[f];
      hitungEl.textContent = "Menampilkan " + n + " dari " + kartu.length + " layanan — " + label + ".";
    }));
  }

  /* ============ 11. AKORDEON FAQ ============ */
  function initAkordeon() {
    $$(".acc").forEach((acc) => {
      const items = $$(".acc-item", acc);
      items.forEach((item) => {
        const btn = $(".acc-btn", item);
        btn.addEventListener("click", () => {
          const terbuka = item.classList.contains("open");
          items.forEach((i) => {
            i.classList.remove("open");
            $(".acc-btn", i).setAttribute("aria-expanded", "false");
          });
          if (!terbuka) {
            item.classList.add("open");
            btn.setAttribute("aria-expanded", "true");
          }
        });
      });
    });
  }

  /* ============ 12. SEGMENTED CONTROL ALUR KERJA ============ */
  function initAlur() {
    const wrap = $("#alurInteraktif");
    if (!wrap) return;

    const ALUR = [
      { ikon: "document", judul: "Penerimaan & Penimbangan",
        teks: "Pakaian diterima, dihitung, ditimbang, dan diperiksa kondisinya satu per satu. Seluruh data pencatatan ditulis ke nota digital yang langsung dikirim ke WhatsApp pelanggan, sehingga Anda bisa memeriksa kelengkapan kapan saja tanpa harus menyimpan kertas." },
      { ikon: "washer", judul: "Pemilahan & Pencucian",
        teks: "Pakaian dipisahkan berdasarkan warna, jenis kain, dan tingkat kekotorannya. Setiap kelompok dicuci dengan deterjen serta mode mesin yang paling sesuai, tidak pernah digabung antar pelanggan, dan pakaian bayi selalu mendapatkan jalur terpisah yang lebih higienis." },
      { ikon: "flame", judul: "Pengeringan & Penyetrikaan",
        teks: "Pakaian dikeringkan pada suhu yang aman untuk masing-masing bahan, lalu disetrika oleh tim berpengalaman dengan perhatian khusus pada kerah, manset, dan lipatan. Hasil akhirnya rapi, halus, dan siap langsung dipakai." },
      { ikon: "truck-fast", judul: "Pengemasan & Pengiriman",
        teks: "Pakaian yang sudah selesai dilipat dan dikemas rapi dengan pelindung higienis, diberi label sesuai nota, lalu diantar kembali oleh kurir sesuai jadwal yang telah disepakati. Anda cukup menerima pakaian bersih tanpa perlu keluar rumah." }
    ];

    const btns = $$(".segmented button", wrap);
    const detail = $("#alurDetail");
    const bar = $(".alur-progress .bar", wrap);
    let indeks = 0;
    let timer = null;

    function render(i) {
      indeks = (i + ALUR.length) % ALUR.length;
      const d = ALUR[indeks];
      detail.innerHTML =
        '<span class="ikon" aria-hidden="true"><re-icon icon="' + d.ikon + '" size="26" color="currentColor"></re-icon></span>' +
        "<div><h3>" + d.judul + "</h3><p>" + d.teks + "</p></div>";
      btns.forEach((b, bi) => {
        b.classList.toggle("on", bi === indeks);
        b.setAttribute("aria-pressed", bi === indeks ? "true" : "false");
      });
      if (bar) bar.style.transform = "scaleX(" + ((indeks + 1) / ALUR.length) + ")";
    }

    btns.forEach((b, bi) => b.addEventListener("click", () => { render(bi); berhenti(); }));

    function mulai() {
      if (reducedMotion) return;
      timer = setInterval(() => render(indeks + 1), 4500);
    }
    function berhenti() { clearInterval(timer); }
    wrap.addEventListener("mouseenter", berhenti);
    wrap.addEventListener("focusin", berhenti);
    wrap.addEventListener("mouseleave", mulai);

    render(0);
    mulai();
  }

  /* ============ 13. MODUL TIPS DINAMIS ============ */
  function initTips() {
    const grid = $("#gridTips");
    if (!grid) return;

    const TIPS = [
      { judul: "Pisahkan Pakaian Berdasarkan Warna dan Jenis Kain", kategori: "pemilahan", ikon: "color-swatch",
        ringkas: "Mencampur semua pakaian dalam satu mesin adalah kesalahan paling umum.",
        isi: "Pakaian putih yang dicuci bersama jeans baru berwarna gelap hampir pasti berubah keabu-abuan karena serat pewarna yang larut dalam air. Kebiasaan ini juga membuat noda dari satu pakaian menular ke pakaian lain. Minimal pisahkan tiga kelompok: pakaian putih dan warna terang, pakaian warna pekat, serta pakaian berbahan halus seperti sutra, sifon, atau renda. Pisahkan juga pakaian penghasil serat seperti handuk dan blazer wol agar pakaian lain tidak tampak berbulu. Dengan kebiasaan sederhana ini, warna pakaian akan tetap cerah jauh lebih lama." },
      { judul: "Baca Label Perawatan Sebelum Mencuci", kategori: "perawatan", ikon: "tag",
        ringkas: "Label kecil di bagian dalam pakaian menyimpan informasi paling penting.",
        isi: "Simbol bak mandi berarti boleh dicuci mesin, segitiga berarti boleh memakai pemutih, kotak dengan lingkaran berarti boleh masuk mesin pengering, dan setrika bertitik menunjukkan tingkat panas yang aman. Banyak orang mengabaikannya padahal isinya mencegah pakaian menyusut, melar, atau rusak. Jika ragu, gunakan cara paling lembut: air dingin dan siklus halus. Di Sparkleen Laundry, setiap pakaian selalu diperiksa labelnya oleh tim kami sebelum masuk tahap pencucian." },
      { judul: "Gunakan Suhu Air yang Tepat", kategori: "praktik", ikon: "thermometer",
        ringkas: "Air panas tidak selalu lebih baik untuk semua jenis pakaian.",
        isi: "Air panas memang efektif untuk handuk, sprei, dan pakaian kotor berat, tetapi membuat pakaian sehari-hari menyusut, meluntur, dan cepat kusam. Untuk pakaian berwarna dan bahan sintetis, gunakan air dingin atau suhu ruangan agar warna stabil sekaligus hemat listrik. Pakaian bayi dan pemilik kulit sensitif sebaiknya dicuci dengan air hangat bersuhu sedang memakai deterjen hipoalergenik tanpa pewangi kuat. Di outlet kami, setiap mesin punya pengaturan suhu yang disesuaikan operator berdasarkan jenis kain." },
      { judul: "Perlakukan Bahan Khusus dengan Cara Khusus", kategori: "perawatan", ikon: "layer",
        ringkas: "Wol, sutra, dan linen punya aturan main yang berbeda satu sama lain.",
        isi: "Wol akan menyusut dan menggumpal bila digosok keras; cukup rendam singkat air dingin dan keringkan di atas handuk. Sutra sebaiknya dicuci tangan dengan sabun lembut dan dijemur tanpa sinar matahari langsung agar kilau alaminya terjaga. Linen mudah kusut parah jika diperas berlebihan. Untuk jas, gaun pesta, atau kebaya bersulam, jangan risikokan mencucinya sendiri — layanan dry clean kami dirancang khusus untuk bahan sensitif tersebut." },
      { judul: "Simpan Pakaian dengan Benar Setelah Bersih", kategori: "penyimpanan", ikon: "closet",
        ringkas: "Perawatan tidak berhenti di mesin cuci; lemari ikut menentukan umur pakaian.",
        isi: "Pastikan pakaian benar-benar kering sebelum disimpan karena kelembapan memicu bau apek dan jamur. Gunakan hanger berbantalan untuk blazer agar bahu tidak turun, dan lipat pakaian rajut seperti kaos serta sweater alih-alih menggantungnya agar tidak melar. Jangan padatkan lemari terlalu penuh, sisipkan kapur barus secara rutin, dan bersihkan lemari minimal dua kali setahun. Pakaian bersih dari laundry pun akan tetap wangi sampai saat dipakai kembali." },
      { judul: "Jangan Terlalu Banyak Deterjen", kategori: "praktik", ikon: "bottle",
        ringkas: "Dosis berlebihan justru meninggalkan residu yang membuat pakaian kusam.",
        isi: "Sisa deterjen yang tidak terbilas sempurna menempel di serat kain, menarik debu, dan memicu iritasi pada kulit sensitif. Ikuti takaran pada kemasan dan ingat bahwa air Bandung yang tergolong lunak membutuhkan dosis lebih sedikit. Gejala pakaian berlendir atau berbau sepat saat setengah kering adalah tanda deterjen berlebih. Jika terjadi, cuci ulang tanpa deterjen sekali lagi untuk membilas residunya." },
      { judul: "Kosongkan Kantong dan Rapatkan Resleting", kategori: "pemilahan", ikon: "bag-cross",
        ringkas: "Tisu di kantong adalah musuh utama seluruh tumpukan cucian Anda.",
        isi: "Tisu, karcis, atau koin yang tertinggal di kantong akan hancur saat pencucian dan menyelimuti seluruh pakaian lain dengan serat putih. Periksalah setiap kantong sebelum mencuci, rapatkan resleting agar giginya tidak merusak kain lain, dan kaitkan bra agar tidak mengait baju di sekitarnya. Kebiasaan satu menit ini menyelamatkan Anda dari satu putaran pencucian ulang yang membuang waktu, air, dan listrik." },
      { judul: "Angkat Noda Secepat Mungkin", kategori: "perawatan", ikon: "sparkle",
        ringkas: "Noda berumur 10 menit jauh lebih mudah dihilangkan daripada noda berumur sehari.",
        isi: "Segera tepuk-tepuk noda dengan kain lembap (jangan digosok) dan beri sedikit air dingin dari sisi dalam kain agar noda terdorong keluar, bukan masuk lebih dalam. Hindari air panas untuk noda berprotein seperti darah karena panas justru mengunci pigmennya di serat. Untuk noda membandel seperti tinta, cat, atau saus tomat, informasikan ke tim kami saat menyerahkan pakaian agar bisa dipersiapkan treatment khusus sebelum pencucian." }
    ];

    const LABEL_KAT = { pemilahan: "Pemilahan", perawatan: "Perawatan", praktik: "Praktik Cuci", penyimpanan: "Penyimpanan" };

    const chips = $$(".tips-chips .chip");
    const input = $("#cariTips");
    const clear = $("#clearTips");
    const count = $("#tipsCount");
    const kosong = $("#tipsKosong");
    const btnAcak = $("#tipsAcak");
    let katAktif = "semua";

    function render(list) {
      grid.innerHTML = list.map((t, i) => `
        <article class="tip-card" data-kategori="${t.kategori}" data-id="${i}" style="--d:${(i % 2) * 0.08}s">
          <div class="tip-top">
            <span class="ikon" aria-hidden="true"><re-icon icon="${t.ikon}" size="20" color="currentColor"></re-icon></span>
            <span class="tip-kat">${LABEL_KAT[t.kategori]}</span>
          </div>
          <h3>${t.judul}</h3>
          <p class="ringkas">${t.ringkas}</p>
          <div class="tip-panel"><div><p>${t.isi}</p></div></div>
          <button type="button" class="tip-more" aria-expanded="false">Baca selengkapnya <span class="arw" aria-hidden="true">⌄</span></button>
        </article>`).join("");

      $$(".tip-card .tip-more", grid).forEach((b) =>
        b.addEventListener("click", function () {
          const card = b.closest(".tip-card");
          const buka = card.classList.toggle("open");
          b.setAttribute("aria-expanded", String(buka));
          b.firstChild.textContent = buka ? "Tutup " : "Baca selengkapnya ";
        }));
    }

    function saring() {
      const q = (input.value || "").toLowerCase().trim();
      const list = TIPS.map((t, i) => ({ ...t, idx: i }))
        .filter((t) => (katAktif === "semua" || t.kategori === katAktif))
        .filter((t) => !q || (t.judul + " " + t.ringkas + " " + t.isi).toLowerCase().includes(q));
      render(list);
      clear.classList.toggle("show", q.length > 0);
      count.textContent = list.length === 0
        ? "Tidak ada tips yang cocok."
        : "Ditemukan " + list.length + " dari " + TIPS.length + " tips.";
      kosong.classList.toggle("show", list.length === 0);
      grid.style.display = list.length === 0 ? "none" : "";
    }

    chips.forEach((chip) => chip.addEventListener("click", () => {
      chips.forEach((c) => { c.classList.remove("on"); c.setAttribute("aria-pressed", "false"); });
      chip.classList.add("on");
      chip.setAttribute("aria-pressed", "true");
      katAktif = chip.dataset.filter;
      saring();
    }));

    input.addEventListener("input", saring);
    clear.addEventListener("click", () => { input.value = ""; saring(); input.focus(); });

    const reset = $("#resetTips");
    if (reset) reset.addEventListener("click", () => {
      input.value = "";
      katAktif = "semua";
      chips.forEach((c) => {
        const aktif = c.dataset.filter === "semua";
        c.classList.toggle("on", aktif);
        c.setAttribute("aria-pressed", String(aktif));
      });
      saring();
      input.focus();
    });

    btnAcak.addEventListener("click", () => {
      const kartu = $$(".tip-card", grid);
      if (!kartu.length) return;
      let pilihan;
      do { pilihan = kartu[Math.floor(Math.random() * kartu.length)]; }
      while (kartu.length > 1 && pilihan.classList.contains("open"));
      kartu.forEach((k) => { k.classList.remove("open", "highlight"); const b = $(".tip-more", k); b.setAttribute("aria-expanded", "false"); b.firstChild.textContent = "Baca selengkapnya "; });
      pilihan.classList.add("open", "highlight");
      const b = $(".tip-more", pilihan);
      b.setAttribute("aria-expanded", "true");
      b.firstChild.textContent = "Tutup ";
      pilihan.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "center" });
      setTimeout(() => pilihan.classList.remove("highlight"), 2400);
    });

    saring();
  }

  /* ============ 14. FORM KONTAK + WHATSAPP ============ */
  function initFormKontak() {
    const form = $("#formPesan");
    if (!form) return;

    const fields = {
      nama:    { el: $("#nama"),    cek: (v) => v.trim().length >= 3,
                 pesan: "Nama minimal 3 karakter, misalnya nama lengkap Anda." },
      telepon: { el: $("#telepon"), cek: (v) => /^(?:\+62|62|0)8[1-9][0-9]{6,10}$/.test(v.replace(/[\s-]/g, "")),
                 pesan: "Gunakan format nomor Indonesia, contoh: 0812-3456-7890." },
      layanan: { el: $("#layanan"), cek: (v) => v !== "",
                 pesan: "Pilih salah satu layanan terlebih dahulu." },
      alamat:  { el: $("#alamat"),  cek: (v) => v.trim().length >= 10,
                 pesan: "Tulis alamat lengkap (minimal 10 karakter) agar kurir mudah menemukan lokasi." }
    };

    const panel = $("#suksesPanel");
    const ringkasan = $("#suksesRingkasan");

    function statusField(f, valid) {
      const box = f.el.closest(".field");
      box.classList.toggle("invalid", !valid);
      box.classList.toggle("valid", valid && f.el.value.trim() !== "");
      f.el.setAttribute("aria-invalid", String(!valid));
      const err = $(".field-error", box);
      if (!valid) err.textContent = f.pesan;
    }

    Object.values(fields).forEach((f) => {
      f.el.addEventListener("blur", () => {
        if (f.el.value.trim() !== "" || f.el.tagName === "SELECT") statusField(f, f.cek(f.el.value));
      });
      f.el.addEventListener("input", () => {
        if (f.el.closest(".field").classList.contains("invalid")) statusField(f, f.cek(f.el.value));
      });
    });

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      let pertamaInvalid = null;
      Object.values(fields).forEach((f) => {
        const ok = f.cek(f.el.value);
        statusField(f, ok);
        if (!ok && !pertamaInvalid) pertamaInvalid = f.el;
      });
      if (pertamaInvalid) {
        pertamaInvalid.focus();
        return;
      }

      const data = {
        nama: fields.nama.el.value.trim(),
        telepon: fields.telepon.el.value.trim(),
        layanan: fields.layanan.el.options[fields.layanan.el.selectedIndex].text,
        alamat: fields.alamat.el.value.trim(),
        catatan: ($("#catatan").value || "").trim()
      };

      ringkasan.innerHTML =
        "<span><b>Nama:</b> " + data.nama + "</span>" +
        "<span><b>WhatsApp:</b> " + data.telepon + "</span>" +
        "<span><b>Layanan:</b> " + data.layanan + "</span>" +
        "<span><b>Alamat penjemputan:</b> " + data.alamat + "</span>" +
        (data.catatan ? "<span><b>Catatan:</b> " + data.catatan + "</span>" : "");

      const pesanWA =
        "Halo Sparkleen Laundry! Saya ingin memesan penjemputan pakaian." +
        "\n• Nama: " + data.nama +
        "\n• No. WhatsApp: " + data.telepon +
        "\n• Layanan: " + data.layanan +
        "\n• Alamat penjemputan: " + data.alamat +
        (data.catatan ? "\n• Catatan: " + data.catatan : "") +
        "\nTerima kasih!";
      $("#waLanjut").href = "https://wa.me/" + WA_ADMIN + "?text=" + encodeURIComponent(pesanWA);

      /* ============ PHP CRUD: Kirim pesanan ke server ============ */
      const formData = new FormData();
      formData.append("nama", data.nama);
      formData.append("telepon", data.telepon);
      formData.append("layanan", fields.layanan.el.value);
      formData.append("alamat", data.alamat);
      formData.append("catatan", data.catatan);

      fetch("simpan_pesanan.php", {
        method: "POST",
        body: formData
      })
        .then((res) => res.json())
        .then((json) => {
          if (json.sukses) {
            console.log("Pesanan tersimpan ke database (ID:", json.id, ")");
          } else {
            console.warn("Gagal simpan ke database:", json.pesan);
          }
        })
        .catch((err) => {
          console.warn("Tidak dapat terhubung ke server PHP:", err);
        });

      form.style.display = "none";
      panel.classList.add("show");
      panel.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "center" });
      $("#hapusFokus").focus();
    });

    form.addEventListener("reset", () => {
      setTimeout(() => $$(".field", form).forEach((b) => b.classList.remove("valid", "invalid")), 0);
    });

    $("#pesanLagi").addEventListener("click", () => {
      form.reset();
      $$(".field", form).forEach((b) => b.classList.remove("valid", "invalid"));
      panel.classList.remove("show");
      form.style.display = "";
      fields.nama.el.focus();
    });
  }

  /* ============ 15. ANGGOTA KELOMPOK DINAMIS ============ */
  function initAnggota() {
    const grid = $("#gridAnggota");
    if (!grid) return;

    const ANGGOTA = [
      { nama: "Dani Irawanto", nim: "25104410057", peran: "Ketua",
        tugas: "Koordinasi tim, halaman beranda (index.html), motion & animasi, dan laporan" },
      { nama: "Fidaus Bintang Maulana Jesa", nim: "25104410034", peran: "Anggota",
        tugas: "Halaman layanan & kalkulator harga, halaman tips (filter dan pencarian)" },
      { nama: "Muhamad Krisna Yodi Pratama", nim: "25104410083", peran: "Anggota",
        tugas: "Halaman tentang kami, halaman kontak & validasi form, dan desain CSS" }
    ];

    const TILE = ["tile-1", "tile-2", "tile-5", "tile-3", "tile-8"];
    const input = $("#cariAnggota");
    const count = $("#anggotaCount");

    const inisial = (nama) => nama.split(" ").map((k) => k[0]).join("").slice(0, 2).toUpperCase();

    function render(list) {
      grid.innerHTML = list.map((a, i) => `
        <article class="kartu-anggota">
          <div class="ava ${TILE[i % TILE.length]}" aria-hidden="true">${inisial(a.nama)}</div>
          <h3>${a.nama}</h3>
          <div class="nim">NIM ${a.nim}</div>
          <span class="peran-pill ${a.peran === "Ketua" ? "ketua" : "anggota"}">${a.peran}</span>
          <p>${a.tugas}</p>
        </article>`).join("");
      count.textContent = "Menampilkan " + list.length + " dari " + ANGGOTA.length + " anggota kelompok.";
    }

    input.addEventListener("input", () => {
      const q = input.value.toLowerCase().trim();
      render(ANGGOTA.filter((a) =>
        !q || (a.nama + " " + a.nim + " " + a.tugas + " " + a.peran).toLowerCase().includes(q)));
    });

    render(ANGGOTA);
  }

  /* ============ 16. TOMBOL KE ATAS + TAHUN FOOTER ============ */
  function initLainnya() {
    const toTop = $("#toTop");
    if (toTop) {
      const onScroll = () => toTop.classList.toggle("show", window.scrollY > 640);
      onScroll();
      window.addEventListener("scroll", onScroll, { passive: true });
      toTop.addEventListener("click", () =>
        window.scrollTo({ top: 0, behavior: reducedMotion ? "auto" : "smooth" }));
    }
    const y = $("#tahunFooter");
    if (y) y.textContent = String(new Date().getFullYear());
  }

  /* ============ 17. WORD-REveal JUDUL HERO (inspirasi andreas.vision) ============
     Judul dipecah kata demi kata, lalu tiap kata mengalir masuk (naik + blur
     memudar) dengan jeda berjenjang — meniru keyframe "word-mark/word-text"
     pada andreas.vision. Elemen <span class="grad"> dipertahankan gayanya. */
  function initKataReveal() {
    const h1 = $(".hero h1");
    if (!h1 || reducedMotion) return;

    let urut = 0;
    const buatKata = () => {
      const w = document.createElement("span");
      const isi = document.createElement("span");
      w.className = "wr";
      isi.className = "wi";
      isi.style.setProperty("--wd", (0.15 + urut * 0.07).toFixed(2) + "s");
      urut++;
      w.appendChild(isi);
      return { w: w, isi: isi };
    };

    const frag = document.createDocumentFragment();

    Array.from(h1.childNodes).forEach((node) => {
      if (node.nodeType === 3) {
        // simpul teks biasa → pecah per kata
        node.textContent.split(/(\s+)/).forEach((bagian) => {
          if (!bagian) return;
          if (/^\s+$/.test(bagian)) { frag.appendChild(document.createTextNode(" ")); return; }
          const { w, isi } = buatKata();
          isi.textContent = bagian;
          frag.appendChild(w);
        });
      } else if (node.nodeType === 1 && node.tagName === "BR") {
        frag.appendChild(node); // pertahankan pemenggalan baris
      } else if (node.nodeType === 1) {
        // mis. <span class="grad">…</span> → pecah kata di dalamnya, kelas disalin
        const kelas = node.className;
        node.textContent.split(/(\s+)/).forEach((bagian) => {
          if (!bagian) return;
          if (/^\s+$/.test(bagian)) { frag.appendChild(document.createTextNode(" ")); return; }
          const { w, isi } = buatKata();
          isi.textContent = bagian;
          if (kelas) isi.className = "wi " + kelas;
          frag.appendChild(w);
        });
      }
    });

    h1.textContent = "";
    h1.appendChild(frag);
  }

  /* ============ 18. TOMBOL MAGNETIK (inspirasi andreas.vision) ============
     Tombol utama sedikit "tertarik" mengikuti posisi kursor lalu kembali
     mulus ke posisi semula — hanya aktif untuk pointer presisi (mouse). */
  function initMagnet() {
    if (reducedMotion || !window.matchMedia("(pointer: fine)").matches) return;
    $$(".btn-primary, .btn-wa, .to-top").forEach((btn) => {
      btn.addEventListener("pointermove", (e) => {
        const r = btn.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width / 2) / r.width;
        const y = (e.clientY - r.top - r.height / 2) / r.height;
        btn.style.transform = "translate(" + (x * 7).toFixed(1) + "px," + (y * 6).toFixed(1) + "px)";
      });
      btn.addEventListener("pointerleave", () => { btn.style.transform = ""; });
    });
  }

  /* ============ JALANKAN SEMUA ============ */
  document.addEventListener("DOMContentLoaded", () => {
    initTema();
    initNavbar();
    initStatusBuka();
    initSapaan();
    initKataReveal();
    initReveal();
    initCounter();
    initCountdown();
    initTestimoni();
    initKalkulator();
    initFilterLayanan();
    initAkordeon();
    initAlur();
    initTips();
    initFormKontak();
    initAnggota();
    initLainnya();
    initMagnet();
  });
})();

