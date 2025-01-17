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

// Ambil daftar pasien untuk dokter ini
$query_pasien = "
    SELECT 
        dp.id AS id_daftar_poli,
        dp.no_antrian,
        dp.keluhan,
        p.nama AS nama_pasien,
        p.no_rm,
        p.alamat,
        p.no_hp
    FROM 
        daftar_poli dp
    JOIN 
        pasien p ON dp.id_pasien = p.id
    JOIN 
        jadwal_periksa jp ON dp.id_jadwal = jp.id
    WHERE 
        jp.id_dokter = ? AND 
        dp.id NOT IN (SELECT id_daftar_poli FROM periksa)
";

$stmt_pasien = $koneksi->prepare($query_pasien);
$stmt_pasien->bind_param("i", $dokter_id);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();

// Ambil daftar obat
$query_obat = "SELECT id_obat, nama_obat, harga FROM obat";
$result_obat = $koneksi->query($query_obat);

// Proses form jika ada data yang dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $tanggal_periksa = $_POST['tanggal_periksa'];
    $biaya_dokter = 150000; // Biaya dokter tetap
    $obat_ids = $_POST['obat'] ?? [];
    $catatan = $_POST['catatan'];

    $total_biaya = $biaya_dokter;

    if (!empty($obat_ids)) {
        foreach ($obat_ids as $id_obat) {
            $query_obat = "SELECT harga FROM obat WHERE id_obat = ?";
            $stmt_obat = $koneksi->prepare($query_obat);
            $stmt_obat->bind_param("i", $id_obat);
            $stmt_obat->execute();
            $result_obat = $stmt_obat->get_result();
            $obat = $result_obat->fetch_assoc();
            $total_biaya += (int) $obat['harga'];
        }
    }

    // Simpan data ke tabel periksa
    $query_periksa = "
        INSERT INTO periksa (id_daftar_poli, tanggal_periksa, catatan, biaya_periksa) 
        VALUES (?, ?, ?, ?)
    ";
    $stmt_periksa = $koneksi->prepare($query_periksa);
    $stmt_periksa->bind_param("issi", $id_daftar_poli, $tanggal_periksa, $catatan, $total_biaya);
    $stmt_periksa->execute();

    // Simpan data obat ke tabel detail_periksa
    $id_periksa = $koneksi->insert_id;
    foreach ($obat_ids as $id_obat) {
        $query_obat_periksa = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
        $stmt_obat_periksa = $koneksi->prepare($query_obat_periksa);
        $stmt_obat_periksa->bind_param("ii", $id_periksa, $id_obat);
        $stmt_obat_periksa->execute();
    }

    // Perbarui status di tabel daftar_poli
    $update_status = "UPDATE daftar_poli SET status_periksa = 1 WHERE id = ?";
    $stmt = $koneksi->prepare($update_status);
    $stmt->bind_param("i", $id_daftar_poli);
    $stmt->execute();

    // Redirect kembali ke halaman antrian pasien
    header('Location: index.php?halaman=antrian_pasien');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Dokter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            margin: 20px auto;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
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
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], input[type="date"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
    <script>
        function hitungTotal() {
            const biayaDokter = 150000; // Biaya dokter tetap
            const obat = document.querySelectorAll('input[name="obat[]"]:checked');
            let total = biayaDokter;

            obat.forEach(el => {
                total += parseInt(el.dataset.harga);
            });

            document.getElementById('total_biaya').textContent = total.toLocaleString('id-ID');
        }

        document.addEventListener("DOMContentLoaded", function () {
            hitungTotal();
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Daftar Pasien Belum Diperiksa</h1>
        <table>
            <thead>
                <tr>
                    <th>No. Antrian</th>
                    <th>Nama Pasien</th>
                    <th>No. RM</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th>Keluhan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_pasien->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['no_antrian'] ?></td>
                        <td><?= $row['nama_pasien'] ?></td>
                        <td><?= $row['no_rm'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['no_hp'] ?></td>
                        <td><?= $row['keluhan'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h1>Periksa Pasien</h1>
        <form action="" method="POST">
            <label for="id_daftar_poli">Pilih Pasien:</label>
            <select name="id_daftar_poli" id="id_daftar_poli" required>
                <option value="">-- Pilih Pasien --</option>
                <?php
                $stmt_pasien->execute();
                $result_pasien = $stmt_pasien->get_result();
                while ($row = $result_pasien->fetch_assoc()): ?>
                    <option value="<?= $row['id_daftar_poli'] ?>"><?= $row['nama_pasien'] ?> - <?= $row['keluhan'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="tanggal_periksa">Tanggal Periksa:</label>
            <input type="date" name="tanggal_periksa" id="tanggal_periksa" required>

            <label>Biaya Dokter:</label>
            <p><strong>Rp150.000</strong></p> <!-- Biaya dokter ditampilkan sebagai teks -->

            <label>Obat:</label>
            <?php while ($row = $result_obat->fetch_assoc()): ?>
                <div>
                    <input type="checkbox" name="obat[]" value="<?= $row['id_obat'] ?>" data-harga="<?= $row['harga'] ?>" onchange="hitungTotal()">
                    <?= $row['nama_obat'] ?> - Rp<?= number_format($row['harga'], 0, ',', '.') ?>
                </div>
            <?php endwhile; ?>

            <label for="catatan">Catatan:</label>
            <textarea name="catatan" id="catatan"></textarea>

            <h3 class="total">Total Biaya: Rp<span id="total_biaya">0</span></h3>

            <button type="submit">Selesaikan Pemeriksaan</button>
        </form>
    </div>
</body>
</html>
