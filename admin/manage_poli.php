<?php
// Include koneksi database
include('../koneksi/koneksi.php');

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('location:login.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Include koneksi database
include('../koneksi/koneksi.php');

// Tambah Poli Baru
if (isset($_POST['tambah'])) {
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO poli (nama_poli, keterangan) VALUES ('$nama_poli', '$keterangan')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data poli berhasil ditambahkan');</script>";
        echo "<script>location='index.php?halaman=manage_poli';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data poli');</script>";
    }
}

// Update Data Poli
if (isset($_POST['ubah'])) {
    $id = $_POST['id'];
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    $query = "UPDATE poli SET nama_poli='$nama_poli', keterangan='$keterangan' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data poli berhasil diubah');</script>";
        echo "<script>location='index.php?halaman=manage_poli';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data poli');</script>";
    }
}

// Hapus Data Poli
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $query = "DELETE FROM poli WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data poli berhasil dihapus');</script>";
        echo "<script>location='index.php?halaman=manage_poli';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data poli');</script>";
    }
}
?>

<h2>Kelola Poli</h2>
<hr>

<!-- Tombol Tambah Poli -->
<button class="btn btn-success" data-toggle="modal" data-target="#modalTambah">Tambah Poli</button>
<br><br>

<!-- Tabel Data Poli -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Poli</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM poli";
        $result = mysqli_query($koneksi, $query);
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['nama_poli'] . "</td>";
            echo "<td>" . $row['keterangan'] . "</td>";
            echo "<td>";
            echo "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalUbah" . $row['id'] . "'>Ubah</button> ";
            echo "<a href='index.php?halaman=manage_poli&hapus=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn btn-danger btn-sm'>Hapus</a>";
            echo "</td>";
            echo "</tr>";

            // Modal Ubah Poli
            ?>
            <div class="modal fade" id="modalUbah<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Poli</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group">
                                    <label>Nama Poli</label>
                                    <input type="text" name="nama_poli" class="form-control" value="<?php echo $row['nama_poli']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>keterangan</label>
                                    <input type="text" name="keterangan" class="form-control" value="<?php echo $row['keterangan']; ?>" required>
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

<!-- Modal Tambah Poli -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Poli Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Poli</label>
                        <input type="text" name="nama_poli" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
