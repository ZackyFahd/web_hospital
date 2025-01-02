<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter') {
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

if (!isset($_GET['id_pasien'])) {
    http_response_code(400);
    echo "ID Pasien tidak diberikan.";
    exit;
}

$id_pasien = intval($_GET['id_pasien']);
$id_dokter = $_SESSION['dokter_id']; // Ambil ID dokter dari sesi

// Query untuk mendapatkan detail pemeriksaan pasien hanya untuk dokter yang sedang login
$query_detail = "
    SELECT 
        dp.no_antrian,
        dp.keluhan,
        p.tanggal_periksa,
        p.catatan,
        p.biaya_periksa,
        GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS obat
    FROM 
        daftar_poli dp
    JOIN 
        periksa p ON dp.id = p.id_daftar_poli
    LEFT JOIN 
        detail_periksa dp_obat ON p.id = dp_obat.id_periksa
    LEFT JOIN 
        obat o ON dp_obat.id_obat = o.id_obat
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    WHERE 
        dp.id_pasien = ? 
        AND jp.id_dokter = ? -- Filter berdasarkan dokter yang sedang login
    GROUP BY 
        p.id
";

$stmt_detail = $koneksi->prepare($query_detail);
$stmt_detail->bind_param("ii", $id_pasien, $id_dokter);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();

if ($result_detail->num_rows > 0): ?>
    <h2>Detail Pemeriksaan Pasien</h2>
    <table>
        <thead>
            <tr>
                <th>No. Antrian</th>
                <th>Keluhan</th>
                <th>Tanggal Periksa</th>
                <th>Catatan</th>
                <th>Obat</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_detail->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['no_antrian']) ?></td>
                    <td><?= htmlspecialchars($row['keluhan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_periksa']) ?></td>
                    <td><?= htmlspecialchars($row['catatan']) ?></td>
                    <td><?= htmlspecialchars($row['obat']) ?></td>
                    <td>Rp<?= number_format($row['biaya_periksa'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data pemeriksaan untuk pasien ini oleh Anda.</p>
<?php endif; ?>
