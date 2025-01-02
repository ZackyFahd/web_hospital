<?php
session_start();

include('../koneksi/koneksi.php');

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'pasien' || !isset($_SESSION['pasien_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT nama FROM pasien WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $nama_lengkap = $row['nama'];
    $nama_depan = explode(' ', $nama_lengkap)[0];
} else {
    $nama_depan = "pasien";
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
    <title>Dashboard Pasien</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Pasien</a>
        </div>
        <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;">
            <span>Halo, <strong><?php echo htmlspecialchars($nama_depan); ?></strong>!</span>
            <a href="logout.php" class="btn btn-danger square-btn-adjust">Logout</a>
        </div>
    </nav>

    <!-- NAV SIDE -->
    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li>
                    <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <a href="index.php?halaman=daftar_periksa"><i class="fa fa-folder"></i> Daftar Periksa</a>
                </li>
                <li>
                    <a href="index.php?halaman=rekam_medis"><i class="fa fa-folder"></i> Rekam Medis</a>
                </li>
                <li>
                    <a href="index.php?halaman=logout"><i class="fa fa-sign-out"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END NAV SIDE -->

    <!-- PAGE WRAPPER -->
    <div id="page-wrapper">
        <div id="page-inner">
            <?php
            if (isset($_GET["halaman"])) {
                if ($_GET["halaman"] == "daftar_periksa") {
                    include 'daftar_periksa.php';
                } elseif ($_GET["halaman"] == "rekam_medis") {
                    include 'rekam_medis.php';
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
    <!-- END PAGE WRAPPER -->

</div>
<!-- END WRAPPER -->

<!-- SCRIPTS -->
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.metisMenu.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>
