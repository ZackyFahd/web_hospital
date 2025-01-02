<?php
require '../koneksi/koneksi.php';

// Pastikan ada parameter id_daftar_poli
if (!isset($_GET['id_daftar_poli'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID Daftar Poli tidak diberikan']);
    exit;
}

$id_daftar_poli = intval($_GET['id_daftar_poli']);

// Query untuk mendapatkan detail rekam medis
$query_detail = "
    SELECT 
        dp.no_antrian,
        dp.keluhan,
        p.tanggal_periksa,
        p.catatan,
        p.biaya_periksa,
        d.nama AS nama_dokter,
        GROUP_CONCAT(o.nama SEPARATOR ', ') AS obat
    FROM 
        periksa p
    JOIN 
        daftar_poli dp ON p.id_daftar_poli = dp.id
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN 
        dokter d ON jp.id_dokter = d.id
    LEFT JOIN 
        resep_obat ro ON p.id = ro.id_periksa
    LEFT JOIN 
        obat o ON ro.id_obat = o.id
    WHERE 
        dp.id = ?
    GROUP BY 
        p.id
";

$stmt_detail = $koneksi->prepare($query_detail);
$stmt_detail->bind_param("i", $id_daftar_poli);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();

if ($result_detail->num_rows > 0) {
    $data = $result_detail->fetch_assoc();
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
?>
