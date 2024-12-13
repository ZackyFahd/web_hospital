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

// Tambah Data Dokter
if (isset($_POST['tambah'])) {
    $nama = format_nama(validate_input($_POST['nama']));
    $alamat = format_alamat(validate_input($_POST['alamat']));
    $no_hp = validate_input($_POST['no_hp']);
    $id_poli = validate_input($_POST['id_poli']);
    $username = validate_input($_POST['username']);
    $password = password_hash(validate_input($_POST['password']), PASSWORD_BCRYPT);

    // Periksa jika ada data yang kosong
    if (empty($nama) || empty($alamat) || empty($no_hp) || empty($id_poli) || empty($username) || empty($password)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } else {
        $query = "INSERT INTO dokter (nama, alamat, no_hp, id_poli, username, password) 
                  VALUES ('$nama', '$alamat', '$no_hp', '$id_poli', '$username', '$password')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data dokter berhasil ditambahkan');</script>";
            echo "<script>location='index.php?halaman=manage_dokter';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data dokter');</script>";
        }
    }
}

// Update Data Dokter
if (isset($_POST['ubah'])) {
    $id = $_POST['id'];
    $nama = format_nama(validate_input($_POST['nama']));
    $alamat = format_alamat(validate_input($_POST['alamat']));
    $no_hp = validate_input($_POST['no_hp']);
    $id_poli = validate_input($_POST['id_poli']);
    $username = validate_input($_POST['username']);
    $password_lama = validate_input($_POST['password_lama']);
    $password_baru = !empty($_POST['password_baru']) ? password_hash(validate_input($_POST['password_baru']), PASSWORD_BCRYPT) : null;

    // Periksa jika ada data yang kosong
    if (empty($nama) || empty($alamat) || empty($no_hp) || empty($id_poli) || empty($username)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } else {
        // Verifikasi password lama
        $query = "SELECT password FROM dokter WHERE id='$id'";
        $result = mysqli_query($koneksi, $query);
        $dokter = mysqli_fetch_assoc($result);
        
        if (password_verify($password_lama, $dokter['password'])) {
            // Password lama valid, lanjutkan untuk mengubah data
            if ($password_baru) {
                $query = "UPDATE dokter SET nama='$nama', alamat='$alamat', no_hp='$no_hp', id_poli='$id_poli', username='$username', password='$password_baru' WHERE id='$id'";
            } else {
                $query = "UPDATE dokter SET nama='$nama', alamat='$alamat', no_hp='$no_hp', id_poli='$id_poli', username='$username' WHERE id='$id'";
            }
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Data dokter berhasil diubah');</script>";
                echo "<script>location='index.php?halaman=manage_dokter';</script>";
            } else {
                echo "<script>alert('Gagal mengubah data dokter');</script>";
            }
        } else {
            echo "<script>alert('Password lama salah!');</script>";
        }
    }
}

// Hapus Data Dokter
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM dokter WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data dokter berhasil dihapus');</script>";
        echo "<script>location='index.php?halaman=manage_dokter';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data dokter');</script>";
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


<h2>Kelola Dokter</h2>
<hr>

<!-- Tombol Tambah Dokter -->
<button class="btn btn-success" data-toggle="modal" data-target="#modalTambah">Tambah Dokter</button>
<br><br>

<!-- Tabel Data Dokter -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No. HP</th>
            <th>Poli</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT dokter.*, poli.nama_poli FROM dokter JOIN poli ON dokter.id_poli = poli.id";
        $result = mysqli_query($koneksi, $query);
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>" . $row['alamat'] . "</td>";
            echo "<td>" . $row['no_hp'] . "</td>";
            echo "<td>" . $row['nama_poli'] . "</td>";
            echo "<td>";
            echo "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalUbah" . $row['id'] . "'>Ubah</button> ";
            echo "<a href='index.php?halaman=manage_dokter&hapus=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn btn-danger btn-sm'>Hapus</a>";
            echo "</td>";
            echo "</tr>";

            ?>
            <!-- Modal Ubah Dokter -->
            <div class="modal fade" id="modalUbah<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Dokter</h5>
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
                                    <label>No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?php echo $row['no_hp']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Poli</label>
                                    <select name="id_poli" class="form-control" required>
                                        <?php
                                        $query_poli = "SELECT * FROM poli";
                                        $result_poli = mysqli_query($koneksi, $query_poli);
                                        while ($poli = mysqli_fetch_assoc($result_poli)) {
                                            $selected = $poli['id'] == $row['id_poli'] ? 'selected' : '';
                                            echo "<option value='" . $poli['id'] . "' $selected>" . $poli['nama_poli'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Password Lama</label>
                                    <input type="password" name="password_lama" class="form-control" required>
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

<!-- Modal Tambah Dokter -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dokter</h5>
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
                        <label>No. HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Poli</label>
                        <select name="id_poli" class="form-control" required>
                            <?php
                            $query_poli = "SELECT * FROM poli";
                            $result_poli = mysqli_query($koneksi, $query_poli);
                            while ($poli = mysqli_fetch_assoc($result_poli)) {
                                echo "<option value='" . $poli['id'] . "'>" . $poli['nama_poli'] . "</option>";
                            }
                            ?>
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
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Dokter</button>
                </div>
            </form>
        </div>
    </div>
</div>
