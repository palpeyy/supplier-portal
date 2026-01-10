<!-- Navbar -->
<!-- Kiri -->
  <ul class="navbar-nav">
          <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    <li class="nav-item">
<span class="nav-link font-weight-bold">
        Hai, {{ Auth::user()->name ?? 'Guest' }}! Selamat datang di portal supplier.
      </span>
    </li>
  </ul>

  <!-- Kanan -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <span class="nav-link" id="datetime"></span>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" title="User">
        <i class="fas fa-user-circle"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="#" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> Profil
        </a>
        <div class="dropdown-divider"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </form>
      </div>
    </li>
  </ul>

<script>
  function updateDateTime() {
    const now = new Date();

    // Hari dan bulan dalam bahasa Indonesia
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Ambil bagian-bagian tanggal
    const dayName = days[now.getDay()];
    const date = now.getDate().toString().padStart(2, '0'); // 01, 02, dst
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();

    // Jam, menit, detik
    const hours = now.getHours().toString().padStart(2,'0');
    const minutes = now.getMinutes().toString().padStart(2,'0');
    const seconds = now.getSeconds().toString().padStart(2,'0');

    // Format akhir
    const formatted = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}:${seconds}`;

    document.getElementById('datetime').innerText = formatted;
  }

  // Update setiap 1 detik
  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>

