<?php
// index.php
session_start();

include('../koneksi/koneksi.php');

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter' || !isset($_SESSION['dokter_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT nama FROM dokter WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $nama_lengkap = $row['nama'];
    $nama_depan = explode(' ', $nama_lengkap)[0];
} else {
    $nama_depan = "Dokter";
}
$stmt->close();

$halaman = isset($_GET['halaman']) ? htmlspecialchars($_GET['halaman']) : 'home';
$allowed_pages = ['jadwal_praktek', 'antrian_pasien', 'daftar_pasien', 'home', 'logout'];

$base_dir = __DIR__ . '/pages/';
$page_file = $base_dir . $halaman . '.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Dokter</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
    <script src="assets/js/jquery-1.10.2.js"></script>
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default navbar-cls-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Dokter</a>
        </div>
        <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;">
            Halo Dokter <strong><?php echo htmlspecialchars($nama_depan); ?></strong>!
            <a href="logout.php" class="btn btn-danger square-btn-adjust">Logout</a>
        </div>
    </nav>

    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li>
                    <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <a href="index.php?halaman=jadwal_praktek"><i class="fa fa-calendar"></i> Jadwal Praktek</a>
                </li>
                <li>
                    <a href="index.php?halaman=antrian_pasien"><i class="fa fa-users"></i> Antrian Pasien</a>
                </li>
                <li>
                    <a href="index.php?halaman=daftar_pasien"><i class="fa fa-user-md"></i> Daftar Pasien</a>
                </li>
                <li>
                    <a href="index.php?halaman=edit_profile"><i class="fa fa-users"></i> Edit Profile</a>
                </li>
                <li>
                    <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper">
        <div id="page-inner">
        <?php
            if (isset($_GET["halaman"])) {
                if ($_GET["halaman"] == "jadwal_praktek") {
                    include 'jadwal_praktek.php';
                } elseif ($_GET["halaman"] == "antrian_pasien") {
                    include 'antrian_pasien.php';
                } elseif ($_GET["halaman"] == "daftar_pasien") {
                    include 'daftar_pasien.php';
                }elseif ($_GET["halaman"] == "edit_profile") {
                    include 'edit_profile.php';
                } elseif ($_GET["halaman"] == "logout") {
                    include 'logout.php';
                } else {
                    echo "<h1>Halaman tidak ditemukan</h1>";
                }
            } else {
                include 'home.php';
            }
            ?>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.metisMenu.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>