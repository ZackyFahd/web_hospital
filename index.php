<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            max-height: 150px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
        }

        .btn {
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .container h1 {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 0 3px 5px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <!-- Background Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1>Hospital Management System</h1>
            <p class="fs-5">Pilih login sebagai Admin, Dokter, atau Pasien untuk melanjutkan.</p>
            <div class="row justify-content-center mt-5">
                <!-- Admin Card -->
                <div class="col-md-3">
                    <div class="card shadow-lg">
                        <img src="assets/img/admin-icon.png" class="card-img-top" alt="Admin Icon">
                        <div class="card-body text-center">
                            <h5 class="card-title">Admin</h5>
                            <p class="card-text">Kelola dokter, pasien, poli, dan obat di sistem.</p>
                            <a href="admin/login.php" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login Admin</a>
                        </div>
                    </div>
                </div>
                <!-- Dokter Card -->
                <div class="col-md-3">
                    <div class="card shadow-lg">
                        <img src="assets/img/dokter-icon.png" class="card-img-top" alt="Dokter Icon">
                        <div class="card-body text-center">
                            <h5 class="card-title">Dokter</h5>
                            <p class="card-text">Kelola jadwal dan riwayat pasien.</p>
                            <a href="dokter/login.php" class="btn btn-success w-100"><i class="bi bi-person-lines-fill"></i> Login Dokter</a>
                        </div>
                    </div>
                </div>
                <!-- Pasien Card -->
                <div class="col-md-3">
                    <div class="card shadow-lg">
                        <img src="assets/img/pasien-icon.png" class="card-img-top" alt="Pasien Icon">
                        <div class="card-body text-center">
                            <h5 class="card-title">Pasien</h5>
                            <p class="card-text">Daftar atau login untuk konsultasi.</p>
                            <div class="d-grid gap-2">
                                <a href="pasien/register.php" class="btn btn-secondary"><i class="bi bi-pencil-square"></i> Daftar Baru</a>
                                <a href="pasien/login.php" class="btn btn-info"><i class="bi bi-box-arrow-in-right"></i> Login Pasien</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        &copy; 2024 Hospital Management System. All Rights Reserved.
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
