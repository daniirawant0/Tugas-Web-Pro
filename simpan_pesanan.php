<?php
// simpan_pesanan.php - CREATE: Simpan pesanan baru dari form kontak
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Metode tidak diizinkan.";
    exit;
}

// Ambil dan bersihkan input
$nama     = trim($_POST["nama"] ?? "");
$telepon  = trim($_POST["telepon"] ?? "");
$layanan  = trim($_POST["layanan"] ?? "");
$alamat   = trim($_POST["alamat"] ?? "");
$catatan  = trim($_POST["catatan"] ?? "");

// Validasi sederhana
if ($nama === "" || $telepon === "" || $layanan === "" || $alamat === "") {
    http_response_code(400);
    echo "Semua kolom wajib diisi.";
    exit;
}

// Hitung estimasi harga (sederhana)
$hargaPerKg = [
    "reguler" => 6000,
    "kilat"   => 12000,
    "dryclean" => 15000,
    "sepatu"  => 25000,
    "karpet"  => 30000,
    "setrika" => 4000,
];
$berkat = 3; // asumsi 3 kg untuk estimasi
$totalHarga = ($hargaPerKg[$layanan] ?? 6000) * $berkat;

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO pesanan (nama, telepon, layanan, alamat, catatan, total_harga) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $nama, $telepon, $layanan, $alamat, $catatan, $totalHarga);

if ($stmt->execute()) {
    $idPesanan = $stmt->insert_id;
    http_response_code(201);
    echo json_encode([
        "sukses" => true,
        "pesan" => "Pesanan berhasil dicatat",
        "id" => $idPesanan,
        "total_harga" => $totalHarga,
        "data" => [
            "nama" => $nama,
            "telepon" => $telepon,
            "layanan" => $layanan,
            "alamat" => $alamat,
            "catatan" => $catatan,
            "status" => "baru"
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["sukses" => false, "pesan" => "Gagal menyimpan pesanan: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
