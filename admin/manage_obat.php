<?php
// Include koneksi database
include('../koneksi/koneksi.php');

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('location:login.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Query untuk menampilkan semua obat
$query = "SELECT * FROM obat";
$result = mysqli_query($koneksi, $query);

// Fungsi untuk memvalidasi input dan mencegah serangan XSS dan SQL Injection
function validate_input($data) {
    $data = trim($data); // Menghapus spasi yang tidak perlu
    $data = stripslashes($data); // Menghapus backslashes (\)
    $data = htmlspecialchars($data); // Mengkonversi karakter khusus menjadi entitas HTML
    return $data;
}

// Menangani form tambah obat
if (isset($_POST['tambah_obat'])) {
    $nama_obat = validate_input($_POST['nama_obat']);
    $jenis_obat = validate_input($_POST['jenis_obat']);
    $harga = $_POST['harga'];

    // Validasi jika ada data kosong
    if (empty($nama_obat) || empty($jenis_obat) || empty($harga)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } else {
        // Query untuk menambah data obat
        $query = "INSERT INTO obat (nama_obat, jenis_obat, harga) 
                  VALUES ('$nama_obat', '$jenis_obat', '$harga')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Obat berhasil ditambahkan');</script>";
            echo "<script>location='index.php?halaman=manage_obat';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan obat');</script>";
        }
    }
}

// Ubah Obat
if (isset($_POST['ubah_obat'])) {
    $id_obat = $_POST['id_obat'];
    $nama_obat = validate_input($_POST['nama_obat']);
    $jenis_obat = validate_input($_POST['jenis_obat']);
    $harga = $_POST['harga'];

    // Query untuk mengupdate obat
    $query = "UPDATE obat SET nama_obat='$nama_obat', jenis_obat='$jenis_obat', harga='$harga' WHERE id_obat='$id_obat'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Obat berhasil diubah');</script>";
        echo "<script>location='index.php?halaman=manage_obat';</script>";
    } else {
        echo "<script>alert('Gagal mengubah obat');</script>";
    }
}

// Hapus Obat
if (isset($_GET['hapus'])) {
    $id_obat = $_GET['hapus'];
    $query = "DELETE FROM obat WHERE id_obat='$id_obat'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Obat berhasil dihapus');</script>";
        echo "<script>location='index.php?halaman=manage_obat';</script>";
    } else {
        echo "<script>alert('Gagal menghapus obat');</script>";
    }
}
?>

<h2>Daftar Obat</h2>
<hr>

<!-- Tombol Tambah Obat -->
<button class="btn btn-success" data-toggle="modal" data-target="#modalTambahObat">Tambah Obat</button>
<br><br>

<!-- Tabel Daftar Obat -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Jenis Obat</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['nama_obat'] . "</td>";
            echo "<td>" . $row['jenis_obat'] . "</td>";
            echo "<td>" . $row['harga'] . "</td>";
            echo "<td>";
            echo "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalUbahObat" . $row['id_obat'] . "'>Ubah</button> ";
            echo "<a href='index.php?halaman=manage_obat&hapus=" . $row['id_obat'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn btn-danger btn-sm'>Hapus</a>";
            echo "</td>";
            echo "</tr>";

            // Modal Ubah Obat
            ?>
            <div class="modal fade" id="modalUbahObat<?php echo $row['id_obat']; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Obat</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_obat" value="<?php echo $row['id_obat']; ?>">
                                <div class="form-group">
                                    <label>Nama Obat</label>
                                    <input type="text" name="nama_obat" class="form-control" value="<?php echo $row['nama_obat']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Obat</label>
                                    <select name="jenis_obat" class="form-control" required>
                                        <option value="Tablet" <?php echo ($row['jenis_obat'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                                        <option value="Sirup" <?php echo ($row['jenis_obat'] == 'Sirup') ? 'selected' : ''; ?>>Sirup</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Harga</label>
                                    <input type="number" name="harga" class="form-control" value="<?php echo $row['harga']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" name="ubah_obat" class="btn btn-primary">Simpan Perubahan</button>
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

<!-- Modal Tambah Obat -->
<div class="modal fade" id="modalTambahObat" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Obat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Obat</label>
                        <select name="jenis_obat" class="form-control" required>
                            <option value="Tablet">Tablet</option>
                            <option value="Sirup">Sirup</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_obat" class="btn btn-primary">Tambah Obat</button>
                </div>
            </form>
        </div>
    </div>
</div>
