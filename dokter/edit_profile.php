<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../koneksi/koneksi.php';

// Pastikan hanya dokter yang dapat mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter') {
    header('Location: login.php');
    exit;
}

// Ambil ID dokter dari sesi
$dokter_id = $_SESSION['dokter_id'];
$success_message = '';
$error_message = '';

// Ambil data dokter untuk ditampilkan di form
$query = "SELECT nama, alamat, no_hp FROM dokter WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $dokter_id);
$stmt->execute();
$result = $stmt->get_result();
$dokter = $result->fetch_assoc();
$stmt->close();

if (!$dokter) {
    $error_message = "Data dokter tidak ditemukan.";
}

// Jika form disubmit, proses perubahan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = strtoupper(htmlspecialchars($_POST['nama'])); // Nama otomatis kapital semua
    $alamat = htmlspecialchars($_POST['alamat']);
    $alamat = ucwords(strtolower($alamat)); // Alamat otomatis kapital huruf pertama tiap kata
    $no_hp = htmlspecialchars($_POST['no_hp']);

    // Validasi data
    if (empty($nama) || empty($no_hp)) {
        $error_message = "Nama dan No. HP wajib diisi.";
    } else {
        // Update data dokter tanpa mengganti password dan username
        $query = "UPDATE dokter SET nama = ?, alamat = ?, no_hp = ? WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssi", $nama, $alamat, $no_hp, $dokter_id);

        // Eksekusi query
        if ($stmt->execute()) {
            $success_message = "Profil berhasil diperbarui.";
            
            // Perbarui data sesi
            $_SESSION['dokter_nama'] = $nama;

            // Refresh halaman agar data terbaru langsung muncul
            header("Location: index.php?halaman=edit_profile");
            exit;
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui profil.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Dokter</title>
    <script>
        // Otomatis kapitalisasi pada input nama
        function capitalizeNama(input) {
            input.value = input.value.toUpperCase(); // Semua huruf kapital
        }

        // Otomatis kapitalisasi pada input alamat (huruf awal tiap kata kapital)
        function capitalizeAlamat(input) {
            input.value = input.value
                .toLowerCase() // Semua huruf kecil dulu
                .replace(/\b\w/g, (char) => char.toUpperCase()); // Kapital huruf pertama tiap kata
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Edit Profil Dokter</h1>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php elseif (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <!-- Bagian Informasi Profil -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Profil Dokter</h4>
        </div>
        <div class="card-body">
            <p><strong>Nama:</strong> <?= htmlspecialchars($dokter['nama']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($dokter['alamat']) ?></p>
            <p><strong>Nomor HP:</strong> <?= htmlspecialchars($dokter['no_hp']) ?></p>
        </div>
    </div>

    <!-- Form Edit Profil -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($dokter['nama']) ?>" required oninput="capitalizeNama(this)">
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" oninput="capitalizeAlamat(this)"><?= htmlspecialchars($dokter['alamat']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($dokter['no_hp']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
