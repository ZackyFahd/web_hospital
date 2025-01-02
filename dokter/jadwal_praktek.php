<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

// Pastikan hanya dokter yang dapat mengakses
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter') {
    header('Location: login.php');
    exit;
}

// Ambil ID dokter dari sesi
$dokter_id = $_SESSION['dokter_id'];

// Proses tambah jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_jadwal'])) {
    $hari = htmlspecialchars($_POST['hari']);
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    if (!empty($hari) && !empty($jam_mulai) && !empty($jam_selesai)) {
        $query = "SELECT * FROM jadwal_periksa WHERE id_dokter = ? AND hari = ? 
                  AND ((jam_mulai <= ? AND jam_selesai > ?) OR (jam_mulai < ? AND jam_selesai >= ?))";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isssss", $dokter_id, $hari, $jam_mulai, $jam_mulai, $jam_selesai, $jam_selesai);
        $stmt->execute();
        $jadwal_conflict = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$jadwal_conflict) {
            $query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, jadwal_aktif) 
                      VALUES (?, ?, ?, ?, 0)";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("isss", $dokter_id, $hari, $jam_mulai, $jam_selesai);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Proses edit jadwal (mengaktifkan jadwal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_jadwal'])) {
    $jadwal_id = $_POST['jadwal_id'];

    // Nonaktifkan semua jadwal sebelumnya
    $query = "UPDATE jadwal_periksa SET jadwal_aktif = 0 WHERE id_dokter = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    $stmt->close();

    // Aktifkan jadwal yang dipilih
    $query = "UPDATE jadwal_periksa SET jadwal_aktif = 1 WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $jadwal_id);
    if ($stmt->execute()) {
        $success_message = "Jadwal berhasil diaktifkan!";
    } else {
        $error_message = "Gagal mengaktifkan jadwal.";
    }
    $stmt->close();
}

// Ambil data jadwal
$query = "SELECT * FROM jadwal_periksa WHERE id_dokter = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $dokter_id);
$stmt->execute();
$result = $stmt->get_result();
$jadwal = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Praktek</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Kelola Jadwal Praktek</h1>

    <!-- Pesan -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <!-- Form Tambah Jadwal -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Jadwal Baru</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="hari" class="form-label">Hari</label>
                    <select class="form-select" id="hari" name="hari" required>
                        <option value="">Pilih Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                    <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                </div>
                <div class="mb-3">
                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                    <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                </div>
                <button type="submit" name="add_jadwal" class="btn btn-primary">Tambah Jadwal</button>
            </form>
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Jadwal</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($jadwal)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada jadwal.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jadwal as $index => $j): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($j['hari']) ?></td>
                            <td><?= htmlspecialchars($j['jam_mulai']) ?></td>
                            <td><?= htmlspecialchars($j['jam_selesai']) ?></td>
                            <td><?= $j['jadwal_aktif'] ? "Aktif" : "Tidak Aktif" ?></td>
                            <td>
                                <?php if (!$j['jadwal_aktif']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="jadwal_id" value="<?= $j['id'] ?>">
                                        <button type="submit" name="activate_jadwal" class="btn btn-success btn-sm">Aktifkan</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
