<?php
require '../koneksi/koneksi.php'; // Koneksi ke database
session_start(); // Memulai sesi

// Validasi data sebelum diproses
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Format Nama menjadi huruf kapital semua
function format_nama($nama) {
    return strtoupper($nama); // Mengubah semua huruf menjadi kapital
}

// Format Alamat: Huruf depan setiap kata menjadi kapital
function format_alamat($alamat) {
    return ucwords(strtolower($alamat)); // Huruf kecil semua, lalu huruf depan kapital
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = format_nama(validate_input($_POST['nama']));
    $alamat = format_alamat(validate_input($_POST['alamat']));
    $no_ktp = validate_input($_POST['no_ktp']);
    $no_hp = validate_input($_POST['no_hp']);
    $tanggal_lahir = validate_input($_POST['tanggal_lahir']);
    $jenis_kelamin = validate_input($_POST['jenis_kelamin']);
    $username = validate_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    // Validasi input
    if (empty($jenis_kelamin) || empty($tanggal_lahir)) {
        $error = "Semua field harus diisi!";
    } else {
        // Periksa apakah pasien sudah ada berdasarkan no KTP
        $query_check = "SELECT * FROM pasien WHERE no_ktp = '$no_ktp'";
        $result_check = mysqli_query($koneksi, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Pasien dengan No. KTP ini sudah terdaftar!";
        } else {
            // Buat No RM baru
            $year_month = date('Ym');
            $query_count = "SELECT COUNT(*) as total FROM pasien WHERE no_rm LIKE '$year_month%'";
            $result_count = mysqli_query($koneksi, $query_count);
            $row_count = mysqli_fetch_assoc($result_count);
            $new_id = $row_count['total'] + 1;
            $no_rm = $year_month . '-' . str_pad($new_id, 3, '0', STR_PAD_LEFT);

            // Masukkan data pasien baru
            $query_insert = "INSERT INTO pasien (no_rm, nama, alamat, no_ktp, no_hp, username, password, jenis_kelamin, tanggal_lahir)
                             VALUES ('$no_rm', '$nama', '$alamat', '$no_ktp', '$no_hp', '$username', '$password', '$jenis_kelamin', '$tanggal_lahir')";
            if (mysqli_query($koneksi, $query_insert)) {
                // Simpan data pengguna ke dalam sesi
                $_SESSION['username'] = $username;
                $_SESSION['nama'] = $nama;
                $_SESSION['no_rm'] = $no_rm;

                // Pindahkan ke dashboard
                header("Location: index.php");
                exit();
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
        }
    }
}
?>

<script>
    // Fungsi untuk mengubah teks menjadi huruf kapital
    function toUpperCase(element) {
        element.value = element.value.toUpperCase();
    }

    // Fungsi untuk kapitalisasi setiap kata
    function capitalizeWords(element) {
        element.value = element.value
            .toLowerCase()
            .replace(/\b\w/g, char => char.toUpperCase());
    }
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pasien</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register_pasien.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/img/pasien-icon.png" alt="Patient Icon">
        </div>
        <h2>Registrasi Pasien Baru</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required oninput="toUpperCase(this)">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" placeholder="Masukkan alamat" required oninput="capitalizeWords(this)">
            </div>
            <div class="form-group">
                <label for="no_ktp">No. KTP</label>
                <input type="text" id="no_ktp" name="no_ktp" placeholder="Masukkan No. KTP" maxlength="16" required>
            </div>
            <div class="form-group">
                <label for="no_hp">No. HP</label>
                <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan No. HP" required>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="" disabled selected>Pilih jenis kelamin</option>
                    <option value="Laki-Laki">Laki-Laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-primary">Daftar</button>
            <?php if (isset($error)): ?>
                <div class="alert-danger"> <?= $error ?> </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function toUpperCase(element) {
            element.value = element.value.toUpperCase();
        }

        function capitalizeWords(element) {
            element.value = element.value
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase());
        }
    </script>
</body>
</html>
