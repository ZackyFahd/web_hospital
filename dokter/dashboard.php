<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Selamat datang, <?= $_SESSION['username']; ?>!</h1>
        <p>Ini adalah dashboard Admin.</p>
        <a href="../../index.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
