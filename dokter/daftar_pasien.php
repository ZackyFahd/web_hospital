<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter') {
    header('Location: login.php');
    exit;
}

$dokter_id = $_SESSION['dokter_id'];

// Query untuk mendapatkan daftar pasien yang pernah mendaftar di poli dokter tersebut
$query_pasien = "
    SELECT DISTINCT
        p.id AS id_pasien,
        p.nama AS nama_pasien,
        p.no_rm,
        p.jenis_kelamin,
        p.no_hp,
        p.alamat
    FROM 
        daftar_poli dp
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN 
        dokter d ON jp.id_dokter = d.id
    JOIN 
        pasien p ON dp.id_pasien = p.id
    WHERE 
        d.id = ?
";

$stmt_pasien = $koneksi->prepare($query_pasien);
$stmt_pasien->bind_param("i", $dokter_id);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pasien</title>
    <style>
        /* Tambahkan gaya sederhana */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        async function showDetails(idPasien) {
            try {
                const response = await fetch(`detail_pasien.php?id_pasien=${idPasien}`);
                if (!response.ok) {
                    throw new Error('Gagal mengambil data detail pasien.');
                }
                const data = await response.text();
                document.getElementById('modalContent').innerHTML = data;
                document.getElementById('detailModal').style.display = 'block';
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h1>Daftar Pasien</h1>
    <table>
        <thead>
            <tr>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Jenis Kelamin</th>
                <th>No. HP</th>
                <th>Alamat</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_pasien->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['no_rm']) ?></td>
                    <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td>
                        <button onclick="showDetails(<?= $row['id_pasien'] ?>)">Lihat Detail</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal untuk Detail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>
</body>
</html>
