<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Portal Supplier - PT Krama Yudha Tiga Berlian</title>

  <link href="img/logo.png" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
    }

    .bg-image {
      background: url('img/office-bg.jpg') center / cover no-repeat;
      height: 100%;
      position: relative;
    }

    .overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(180,0,0,0.85), rgba(0,0,0,0.85));
      z-index: 1;
    }

    /* Wrapper konten */
    .content-wrapper {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      z-index: 2;
    }

    .content {
      max-width: 1200px;
      width: 100%;
      margin: auto;
      color: #fff;
      padding: 0 30px;
      animation: fadeInUp 1.2s ease-out;
    }

    h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    h2 {
      font-size: 1.4rem;
      font-weight: 400;
      margin-bottom: 20px;
      opacity: 0.9;
    }

    p {
      font-size: 1rem;
      opacity: 0.9;
    }

    .btn-login {
      padding: 12px 30px;
      font-size: 1.1rem;
      border-radius: 50px;
      background: #b40000;
      border: 2px solid #b40000;
      color: #fff;
      transition: .3s;
      box-shadow: 0 6px 15px rgba(0,0,0,.4);
    }

    .btn-login:hover {
      background: #fff;
      color: #b40000;
      transform: scale(1.05);
    }

    /* Logo */
    .logo-container {
      position: absolute;
      top: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 3;
      text-align: center;
    }

    .logo-container span {
      font-size: 3.2rem;
      font-weight: 700;
      color: #fff;
      letter-spacing: 2px;
    }

    .sub-logo {
      font-size: .85rem;
      letter-spacing: 2px;
      opacity: .85;
      color: #ffffff;
      letter-spacing: 0.3px;
    }

    /* Gambar kanan */
    .hero-image {
    max-width: 85%;
    height: auto;
    margin-left: auto;   /* PENTING */
    display: block;      /* PENTING */
    }

    .footer {
      position: absolute;
      bottom: 0;
      width: 100%;
      text-align: center;
      color: #fff;
      font-size: .85rem;
      padding: 10px 0;
      opacity: .85;
      z-index: 3;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
  <div class="bg-image">
    <div class="overlay"></div>

    <!-- Logo -->
    <div class="logo-container">
      <span>PORTAL SUPPLIER</span>
      <div class="sub-logo">PT KRAMA YUDHA TIGA BERLIAN</div>
    </div>

    <!-- Content -->
    <div class="content-wrapper">
      <div class="content">
        <div class="row align-items-center">

          <!-- KIRI: Teks -->
          <div class="col-md-6 text-center text-md-start">
            <h1>Selamat Datang</h1>
            <h2>Portal Supplier PT Krama Yudha Tiga Berlian</h2>
            <p class="mb-4">
              Sistem terintegrasi untuk pengelolaan
              <strong>Purchase Order (PO)</strong>,
              <strong>Surat Jalan</strong>,
              dan <strong>Proforma Invoice (PI)</strong>
              secara transparan, akurat, dan real-time.
            </p>
            <a href="login" class="btn btn-login">
              <i class="bi bi-box-arrow-in-right"></i> Masuk Portal
            </a>
          </div>

          <!-- KANAN: Gambar -->
            <div class="col-md-6 d-flex justify-content-end align-items-center mt-4 mt-md-0">
            <img src="img/supplier.png" class="hero-image" alt="Portal Supplier KTB">
            </div>

        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      &copy; 2026 PT Krama Yudha Tiga Berlian. All rights reserved.
    </div>
  </div>
</body>

</html>
