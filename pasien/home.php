<?php
include('../koneksi/koneksi.php');

// Pastikan pasien sudah login
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'pasien') {
    header('Location: login.php');
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Ambil informasi pasien
$query = "SELECT id, nama, no_rm FROM pasien WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$pasien = $result->fetch_assoc();

// Ambil jadwal kontrol berikutnya
$query_jadwal = "
    SELECT jp.hari, jp.jam_mulai, p.nama_poli, d.nama AS nama_dokter
    FROM daftar_poli dp
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter d ON jp.id_dokter = d.id
    JOIN poli p ON d.id_poli = p.id
    WHERE dp.id_pasien = ? AND dp.no_antrian IS NOT NULL
    ORDER BY jp.hari ASC LIMIT 1
";
$stmt_jadwal = $koneksi->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $pasien['id']);
$stmt_jadwal->execute();
$jadwal = $stmt_jadwal->get_result()->fetch_assoc();


// Ambil total riwayat pemeriksaan
$query_riwayat = "SELECT COUNT(*) AS total_riwayat FROM periksa WHERE id_daftar_poli IN (SELECT id FROM daftar_poli WHERE id_pasien = ?)";
$stmt_riwayat = $koneksi->prepare($query_riwayat);
$stmt_riwayat->bind_param("i", $pasien['id']);
$stmt_riwayat->execute();
$result_riwayat = $stmt_riwayat->get_result();
$riwayat = $result_riwayat->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style-admin.css">
  <title>Dashboard User - Klinik Slamet Medika</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .info-box {
      margin: 20px 0;
      padding: 15px;
      border-radius: 10px;
      color: #fff;
    }

    .info-box.primary {
      background-color: #007bff;
    }

    .info-box.success {
      background-color: #28a745;
    }

    .info-box.warning {
      background-color: #ffc107;
    }

    h3 {
      margin-top: 0;
    }

    p {
      margin: 5px 0;
    }

    @media (max-width: 768px) {
      .info-box {
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <!-- Content Section -->
  <div class="container">
    <h2>Dashboard Pasien</h2>
    <div class="info-box primary">
      <h3>Informasi Pasien</h3>
      <p><strong>Nama:</strong> <?php echo htmlspecialchars($pasien['nama']); ?></p>
      <p><strong>No. Rekam Medis:</strong> <?php echo htmlspecialchars($pasien['no_rm']); ?></p>
    </div>
    <div class="info-box success">
      <h3>Jadwal Kontrol Berikutnya</h3>
      <?php if ($jadwal): ?>
        <p><strong>Poli:</strong> <?php echo htmlspecialchars($jadwal['nama_poli']); ?></p>
        <p><strong>Dokter:</strong> <?php echo htmlspecialchars($jadwal['nama_dokter']); ?></p>
        <p><strong>Hari:</strong> <?php echo htmlspecialchars($jadwal['hari']); ?></p>
        <p><strong>Jam:</strong> <?php echo htmlspecialchars($jadwal['jam_mulai']); ?></p>
      <?php else: ?>
        <p>Belum ada jadwal kontrol yang terdaftar.</p>
      <?php endif; ?>
    </div>
    <div class="info-box warning">
      <h3>Riwayat Pemeriksaan</h3>
      <p>Total pemeriksaan: <strong><?php echo (int) $riwayat['total_riwayat']; ?></strong></p>
    </div>
  </div>
</body>

</html>
