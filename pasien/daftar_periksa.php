<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'pasien') {
    header('Location: login.php');
    exit;
}

$pasien_id = $_SESSION['pasien_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Ambil daftar poli
    if ($action === 'get_poli') {
        $query = "SELECT id, nama_poli FROM poli";
        $result = $koneksi->query($query);

        $poli = [];
        while ($row = $result->fetch_assoc()) {
            $poli[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($poli);
        exit;
    }

    // Ambil jadwal dokter berdasarkan poli
    if ($action === 'get_jadwal') {
        $id_poli = (int)$_POST['id_poli'] ?? 0;

        $query = "
            SELECT 
                jp.id AS id_jadwal,
                jp.hari,
                jp.jam_mulai,
                jp.jam_selesai,
                d.nama AS nama_dokter
            FROM 
                jadwal_periksa jp
            JOIN 
                dokter d ON jp.id_dokter = d.id
            WHERE 
                d.id_poli = ? AND jp.jadwal_aktif = 1
        ";

        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_poli);
        $stmt->execute();
        $result = $stmt->get_result();

        $jadwal = [];
        while ($row = $result->fetch_assoc()) {
            $jadwal[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($jadwal);
        exit;
    }

    // Daftar periksa
    if ($action === 'daftar_periksa') {
        $id_pasien = (int)$_POST['id_pasien'] ?? 0;
        $id_jadwal = (int)$_POST['id_jadwal'] ?? 0;
        $keluhan = $_POST['keluhan'] ?? '';

        // Ambil nomor antrian terakhir di jadwal tersebut
        $query_antrian = "
            SELECT MAX(no_antrian) AS no_antrian_terakhir 
            FROM daftar_poli 
            WHERE id_jadwal = ? AND status_periksa = 0
        ";

        $stmt = $koneksi->prepare($query_antrian);
        $stmt->bind_param("i", $id_jadwal);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $no_antrian_terakhir = $row['no_antrian_terakhir'] ?? 0;

        // Jika tidak ada pasien yang belum diperiksa, mulai ulang dari 1
        $no_antrian_baru = ($no_antrian_terakhir > 0) ? $no_antrian_terakhir + 1 : 1;

        // Tambahkan ke daftar periksa
        $query_daftar = "
            INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status_periksa) 
            VALUES (?, ?, ?, ?, 0)
        ";
        $stmt = $koneksi->prepare($query_daftar);
        $stmt->bind_param("iisi", $id_pasien, $id_jadwal, $keluhan, $no_antrian_baru);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'no_antrian' => $no_antrian_baru]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar']);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Periksa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fc;
        }
        header {
            background-color: #17a2b8;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 1.5em;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #555;
        }
        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        select {
            background-color: #f9f9f9;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color: #007bff;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        async function loadPoli() {
            const response = await fetch('daftar_periksa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_poli'
            });
            const data = await response.json();

            const poliSelect = document.getElementById('poli');
            poliSelect.innerHTML = '<option value="" disabled selected>Pilih Poli</option>';
            data.forEach(poli => {
                const option = document.createElement('option');
                option.value = poli.id;
                option.textContent = poli.nama_poli;
                poliSelect.appendChild(option);
            });
        }

        async function loadJadwal() {
            const idPoli = document.getElementById('poli').value;
            const response = await fetch('daftar_periksa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=get_jadwal&id_poli=${idPoli}`
            });
            const data = await response.json();

            const jadwalSelect = document.getElementById('jadwal');
            jadwalSelect.innerHTML = '<option value="" disabled selected>Pilih Jadwal</option>';
            data.forEach(jadwal => {
                const option = document.createElement('option');
                option.value = jadwal.id_jadwal;
                option.textContent = `${jadwal.hari} (${jadwal.jam_mulai} - ${jadwal.jam_selesai}) - Dokter: ${jadwal.nama_dokter}`;
                jadwalSelect.appendChild(option);
            });
        }

        async function daftarPeriksa() {
            const idPasien = document.getElementById('id_pasien').value;
            const idJadwal = document.getElementById('jadwal').value;
            const keluhan = document.getElementById('keluhan').value;

            const response = await fetch('daftar_periksa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=daftar_periksa&id_pasien=${idPasien}&id_jadwal=${idJadwal}&keluhan=${keluhan}`
            });
            const data = await response.json();

            if (data.status === 'success') {
                alert(`Pendaftaran berhasil! Nomor Antrian Anda: ${data.no_antrian}`);
                window.location.href = 'index.php?halaman=daftar_periksa'; // Redirect ke halaman daftar periksa
            } else {
                alert('Pendaftaran gagal: ' + data.message);
            }
        }
        const idPoli = document.getElementById('poli').value;
    </script>
</head>
<body onload="loadPoli()">
    <header>Daftar Periksa</header>
    <div class="container">
        <h1>Formulir Pendaftaran</h1>
        <form onsubmit="event.preventDefault(); daftarPeriksa();">
            <input type="hidden" id="id_pasien" value="<?= htmlspecialchars($pasien_id) ?>">

            <label for="poli">Poli:</label>
            <select id="poli" onchange="loadJadwal()" required></select>

            <label for="jadwal">Jadwal:</label>
            <select id="jadwal" required></select>

            <label for="keluhan">Keluhan:</label>
            <textarea id="keluhan" required></textarea>

            <button type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>
