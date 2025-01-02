<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style-admin.css">
  <title>Dashboard Dokter - Klinik Slamet Medika Kota Semarang</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: 'Arial', sans-serif;
    }

    .head {
      position: relative;
      height: 100vh;
      background: url('../images/bg_admin.jpg') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: #fff;
    }

    .header-opacity {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }

    .header-jumbotron {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .header-jumbotron h4 {
      font-size: 3rem;
      font-weight: 300;
      margin: 0;
      line-height: 1.6;
      padding: 20px;
      background-color: rgba(0, 0, 0, 0.5);
      border-radius: 10px;
    }

    @media (max-width: 768px) {
      .header-jumbotron h4 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <!-- Header Jumbotron -->
  <section class="head">
    <div class="header-opacity"></div>
    <div class="header-jumbotron">
      <h4>Selamat Datang Di Dashboard Dokter<br/> Klinik Slamet Medika<br/>Kota Semarang</h4>
    </div>
  </section>
</body>

</html>
