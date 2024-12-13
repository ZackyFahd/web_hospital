<?php
// Include koneksi database
include('../koneksi/koneksi.php');

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('location:login.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

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

// Tambah Data Pasien
if (isset($_POST['tambah'])) {
    $nama = format_nama(validate_input($_POST['nama']));
    $alamat = format_alamat(validate_input($_POST['alamat']));
    $no_ktp = validate_input($_POST['no_ktp']);
    $no_hp = validate_input($_POST['no_hp']);
    $tanggal_lahir = validate_input($_POST['tanggal_lahir']);
    $jenis_kelamin = validate_input($_POST['jenis_kelamin']);
    $username = validate_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    // Periksa jika ada data yang kosong
    if (empty($nama) || empty($alamat) || empty($no_ktp) || empty($no_hp) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($username) || empty($password)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } else {
        // Generate No Rekam Medis (No RM)
        $tahun_bulan = date('Ym');
        $query_count = "SELECT COUNT(*) AS total FROM pasien WHERE no_rm LIKE '$tahun_bulan%'";
        $result_count = mysqli_query($koneksi, $query_count);
        $count = mysqli_fetch_assoc($result_count)['total'] + 1;
        $no_rm = $tahun_bulan . '-' . $count;

        $query = "INSERT INTO pasien (no_rm, nama, alamat, no_ktp, no_hp, tanggal_lahir, jenis_kelamin, username, password) 
                  VALUES ('$no_rm', '$nama', '$alamat', '$no_ktp', '$no_hp', '$tanggal_lahir', '$jenis_kelamin', '$username', '$password')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data pasien berhasil ditambahkan');</script>";
            echo "<script>location='index.php?halaman=manage_pasien';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data pasien');</script>";
        }
    }
}

// Update Data Pasien
if (isset($_POST['ubah'])) {
    $id = $_POST['id'];
    $nama = format_nama(validate_input($_POST['nama']));
    $alamat = format_alamat(validate_input($_POST['alamat']));
    $no_ktp = validate_input($_POST['no_ktp']);
    $no_hp = validate_input($_POST['no_hp']);
    $tanggal_lahir = validate_input($_POST['tanggal_lahir']);
    $jenis_kelamin = validate_input($_POST['jenis_kelamin']);
    $username = validate_input($_POST['username']);

    // Cek jika password baru ada, jika ada maka validasi password lama
    $password_baru = $_POST['password_baru'];
    if (!empty($password_baru)) {
        $password_lama = $_POST['password_lama'];

        // Cek apakah password lama sesuai
        $query_password = "SELECT password FROM pasien WHERE id='$id'";
        $result_password = mysqli_query($koneksi, $query_password);
        $data_password = mysqli_fetch_assoc($result_password);

        if (!password_verify($password_lama, $data_password['password'])) {
            echo "<script>alert('Password lama tidak sesuai!');</script>";
        } else {
            // Enkripsi password baru
            $password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $query_password_update = ", password='$password_baru'";
        }
    } else {
        // Jika tidak mengubah password, tidak perlu update password
        $query_password_update = '';
    }

    // Update data pasien selain No RM dan Username
    $query = "UPDATE pasien SET nama='$nama', alamat='$alamat', no_ktp='$no_ktp', no_hp='$no_hp', tanggal_lahir='$tanggal_lahir', jenis_kelamin='$jenis_kelamin', username='$username' $query_password_update WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data pasien berhasil diubah');</script>";
        echo "<script>location='index.php?halaman=manage_pasien';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data pasien');</script>";
    }
}


// Hapus Data Pasien
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM pasien WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data pasien berhasil dihapus');</script>";
        echo "<script>location='index.php?halaman=manage_pasien';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data pasien');</script>";
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

<h2>Kelola Pasien</h2>
<hr>

<!-- Tombol Tambah Pasien -->
<button class="btn btn-success" data-toggle="modal" data-target="#modalTambah">Tambah Pasien</button>
<br><br>

<!-- Tabel Data Pasien -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>No. RM</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No. KTP</th>
            <th>No. HP</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Username</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM pasien";
        $result = mysqli_query($koneksi, $query);
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['no_rm'] . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>" . $row['alamat'] . "</td>";
            echo "<td>" . $row['no_ktp'] . "</td>";
            echo "<td>" . $row['no_hp'] . "</td>";
            echo "<td>" . $row['tanggal_lahir'] . "</td>";
            echo "<td>" . $row['jenis_kelamin'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>";
            echo "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalUbah" . $row['id'] . "'>Ubah</button> ";
            echo "<a href='index.php?halaman=manage_pasien&hapus=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn btn-danger btn-sm'>Hapus</a>";
            echo "</td>";
            echo "</tr>";

            ?>
            <!-- Modal Ubah Pasien -->
            <div class="modal fade" id="modalUbah<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Pasien</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" value="<?php echo $row['nama']; ?>" required oninput="toUpperCase(this)">
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="<?php echo $row['alamat']; ?>" required oninput="capitalizeWords(this)">
                                </div>
                                <div class="form-group">
                                    <label>No. KTP</label>
                                    <input type="text" name="no_ktp" class="form-control" value="<?php echo $row['no_ktp']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?php echo $row['no_hp']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $row['tanggal_lahir']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control" required>
                                        <option value="Laki-laki" <?php echo ($row['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php echo ($row['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Password Lama</label>
                                    <input type="password" name="password_lama" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Password Baru (Opsional)</label>
                                    <input type="password" name="password_baru" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" name="ubah" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </tbody>
</table>

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pasien</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required oninput="toUpperCase(this)">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" required oninput="capitalizeWords(this)">
                    </div>
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" name="no_ktp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Pasien</button>
                </div>
            </form>
        </div>
    </div>
</div>
