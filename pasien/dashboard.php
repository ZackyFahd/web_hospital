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
$query = "SELECT nama, no_rm FROM pasien WHERE username = ?";
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
    JOIN poli p ON jp.id_dokter = d.id
    JOIN dokter d ON d.id_poli = p.id
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
<div class="row">
    <div class="col-md-12">
        <h2>Selamat datang, <?php echo htmlspecialchars(explode(' ', $pasien['nama'])[0]); ?>!</h2>
        <h5>Nomor Rekam Medis Anda: <?php echo htmlspecialchars($pasien['no_rm']); ?></h5>
    </div>
</div>
<hr />
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Jadwal Kontrol Berikutnya
            </div>
            <div class="panel-body">
                <?php if ($jadwal): ?>
                    <p><strong>Poli:</strong> <?php echo htmlspecialchars($jadwal['nama_poli']); ?></p>
                    <p><strong>Dokter:</strong> <?php echo htmlspecialchars($jadwal['nama_dokter']); ?></p>
                    <p><strong>Hari:</strong> <?php echo htmlspecialchars($jadwal['hari']); ?></p>
                    <p><strong>Jam:</strong> <?php echo htmlspecialchars($jadwal['jam_mulai']); ?></p>
                <?php else: ?>
                    <p>Belum ada jadwal kontrol.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                Total Riwayat Pemeriksaan
            </div>
            <div class="panel-body">
                <h3><?php echo (int) $riwayat['total_riwayat']; ?> kali</h3>
            </div>
        </div>
    </div>
</div>
