<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Portal Supplier - PT Krama Yudha Tiga Berlian</title>

  <link rel="icon" href="img/logo.png">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
    }

    /* Background sama dengan landing page */
    body {
      background: linear-gradient(135deg,
          rgba(180, 0, 0, 0.85),
          rgba(0, 0, 0, 0.9)),
        url('img/office-bg.jpg') center / cover no-repeat;
    }

    .login-logo {
      width: 140px;
      margin-bottom: 15px;
    }

    .password-toggle {
      cursor: pointer;
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

    .animate-fadeInUp {
      animation: fadeInUp 0.9s ease;
    }
  </style>
</head>

<body>

  <div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl px-8 py-9 text-center animate-fadeInUp">

      <!-- Logo KTB -->
      <img src="img/ktb_fuso.png" alt="PT Krama Yudha Tiga Berlian" class="login-logo mx-auto">

      <div class="font-bold text-xl mb-1 text-red-600">PORTAL SUPPLIER</div>
      <div class="text-sm text-gray-600 mb-6">
        PT Krama Yudha Tiga Berlian
      </div>

      <form action="#" method="post">
        @csrf

        <div class="mb-4">
          <div class="flex items-center border border-gray-300 rounded-full bg-gray-50 px-4">
            <i class="bi bi-person-fill text-gray-600"></i>
            <input type="text" name="username" class="flex-1 bg-transparent border-0 px-3 py-3 focus:outline-none" placeholder="NIP / username Supplier" required>
          </div>
        </div>

        <div class="mb-4">
          <div class="flex items-center border border-gray-300 rounded-full bg-gray-50 px-4">
            <i class="bi bi-lock-fill text-gray-600"></i>
            <input type="password" name="password" id="password" class="flex-1 bg-transparent border-0 px-3 py-3 focus:outline-none" placeholder="Password" required>
            <span class="password-toggle text-gray-600 cursor-pointer" onclick="togglePassword()">
              <i class="bi bi-eye-fill" id="toggle-icon"></i>
            </span>
          </div>
        </div>

        <a href="/dashboard" class="w-full bg-red-600 hover:bg-white text-white hover:text-red-600 border-2 border-red-600 rounded-full py-3 px-4 font-semibold text-sm inline-flex items-center justify-center transition duration-300 transform hover:-translate-y-0.5 mt-2">
          <i class="bi bi-box-arrow-in-right mr-2"></i> Login
        </a>
      </form>

      <div class="text-xs text-gray-600 mt-5">
        © 2026 PT Krama Yudha Tiga Berlian
      </div>
    </div>
  </div>

  <script src="https://cdn.tailwindcss.com"></script>

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