<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Portal Supplier - PT Krama Yudha Tiga Berlian</title>

  <link rel="icon" href="img/logo.png">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
    }

    /* Background sama dengan landing page */
    body {
      background: linear-gradient(
        135deg,
        rgba(180, 0, 0, 0.85),
        rgba(0, 0, 0, 0.9)
      ),
      url('img/office-bg.jpg') center / cover no-repeat;
    }

    .login-container {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background: #ffffff;
      width: 100%;
      max-width: 420px;
      padding: 35px 30px;
      border-radius: 16px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
      text-align: center;
      animation: fadeInUp 0.9s ease;
    }

    .login-logo {
      width: 140px;
      margin-bottom: 15px;
    }

    .login-title {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 5px;
      color: #b40000;
    }

    .login-subtitle {
      font-size: 0.85rem;
      color: #666;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 30px;
      padding: 10px 15px;
    }

    .input-group-text {
      border-radius: 30px;
      background: #f5f5f5;
    }

    .btn-login {
      width: 100%;
      border-radius: 30px;
      background: #b40000;
      border: 2px solid #b40000;
      color: #fff;
      padding: 12px;
      font-size: 15px;
      font-weight: 600;
      transition: 0.3s;
      margin-top: 10px;
    }

    .btn-login:hover {
      background: #ffffff;
      color: #b40000;
      transform: translateY(-2px);
    }

    .password-toggle {
      cursor: pointer;
    }

    .footer-text {
      margin-top: 20px;
      font-size: 0.75rem;
      color: #999;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(25px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <div class="login-container">
    <div class="login-card">

      <!-- Logo KTB -->
      <img src="img/ktb_fuso.png" alt="PT Krama Yudha Tiga Berlian" class="login-logo">

      <div class="login-title">PORTAL SUPPLIER</div>
      <div class="login-subtitle">
        PT Krama Yudha Tiga Berlian
      </div>

      <!-- Laravel Session Error (optional) -->
      {{-- @if(session('loginError'))
      <div class="alert alert-danger text-start">
        {{ session('loginError') }}
      </div>
      @endif --}}

      <form action="#" method="post">
        @csrf

        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-person-fill"></i>
            </span>
            <input type="text" name="username" class="form-control" placeholder="NIP / username Supplier" required>
          </div>
        </div>

        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            <span class="input-group-text password-toggle" onclick="togglePassword()">
              <i class="bi bi-eye-fill" id="toggle-icon"></i>
            </span>
          </div>
        </div>

        <a href="/dashboard" class="btn btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Login
        </a>
      </form>

      <div class="footer-text">
        © 2026 PT Krama Yudha Tiga Berlian
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon = document.getElementById('toggle-icon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
      }
    }
  </script>

</body>
</html>
