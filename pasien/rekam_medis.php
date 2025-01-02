<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

// Pastikan pasien sudah login
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'pasien') {
    header('Location: login.php');
    exit;
}

$pasien_id = $_SESSION['pasien_id'];

// Query untuk mendapatkan jadwal pasien yang belum diperiksa
$query_jadwal = "
    SELECT 
        dp.no_antrian,
        dp.keluhan,
        jp.hari,
        jp.jam_mulai,
        jp.jam_selesai,
        d.nama AS nama_dokter
    FROM 
        daftar_poli dp
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN 
        dokter d ON jp.id_dokter = d.id
    WHERE 
        dp.id_pasien = ? AND 
        dp.id NOT IN (SELECT id_daftar_poli FROM periksa)
";

$stmt_jadwal = $koneksi->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $pasien_id);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();

// Query untuk mendapatkan rekap medis pasien
$query_rekap = "
    SELECT 
        dp.no_antrian,
        dp.keluhan,
        p.tanggal_periksa,
        p.catatan,
        p.biaya_periksa,
        d.nama AS nama_dokter,
        GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS obat
    FROM 
        periksa p
    JOIN 
        daftar_poli dp ON p.id_daftar_poli = dp.id
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN 
        dokter d ON jp.id_dokter = d.id
    LEFT JOIN 
        detail_periksa dp_ob ON p.id = dp_ob.id_periksa
    LEFT JOIN 
        obat o ON dp_ob.id_obat = o.id_obat
    WHERE 
        dp.id_pasien = ?
    GROUP BY 
        p.id
";

$stmt_rekap = $koneksi->prepare($query_rekap);
$stmt_rekap->bind_param("i", $pasien_id);
$stmt_rekap->execute();
$result_rekap = $stmt_rekap->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekam Medis Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Jadwal Pasien yang Belum Diperiksa</h1>
    <table>
        <thead>
            <tr>
                <th>No. Antrian</th>
                <th>Keluhan</th>
                <th>Hari</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Nama Dokter</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_jadwal->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['no_antrian']) ?></td>
                    <td><?= htmlspecialchars($row['keluhan']) ?></td>
                    <td><?= htmlspecialchars($row['hari']) ?></td>
                    <td><?= htmlspecialchars($row['jam_mulai']) ?></td>
                    <td><?= htmlspecialchars($row['jam_selesai']) ?></td>
                    <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h1>Rekap Medis Pasien</h1>
    <table>
        <thead>
            <tr>
                <th>No. Antrian</th>
                <th>Keluhan</th>
                <th>Tanggal Periksa</th>
                <th>Catatan</th>
                <th>Biaya Periksa</th>
                <th>Nama Dokter</th>
                <th>Obat</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_rekap->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['no_antrian']) ?></td>
                    <td><?= htmlspecialchars($row['keluhan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_periksa']) ?></td>
                    <td><?= htmlspecialchars($row['catatan']) ?></td>
                    <td>Rp<?= number_format($row['biaya_periksa'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
                    <td><?= htmlspecialchars($row['obat']) ?: 'Tidak ada' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
