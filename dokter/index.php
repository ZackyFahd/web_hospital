<?php
session_start();

// Koneksi ke database
include('../koneksi/koneksi.php');

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dokter') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Dokter</title>
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
            <a class="navbar-brand" href="index.php">Dokter</a>
        </div>
        <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;">
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
                    <a href="index.php?halaman=jadwal_praktek"><i class="fa fa-calendar"></i> Jadwal Praktek</a>
                </li>
                <li>
                    <a href="index.php?halaman=daftar_pasien"><i class="fa fa-users"></i> Daftar Pasien</a>
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
                if ($_GET["halaman"] == "jadwal_praktek") {
                    include 'jadwal_praktek.php';
                } elseif ($_GET["halaman"] == "daftar_pasien") {
                    include 'daftar_pasien.php';
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
