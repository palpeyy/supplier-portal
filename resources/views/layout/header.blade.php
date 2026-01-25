<!-- Navbar with Tailwind -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg px-6 py-4">
  <div class="flex items-center justify-between">
    <!-- Left Side -->
    <div class="flex items-center space-x-4">
      <button class="p-2 hover:bg-blue-700 rounded-lg transition duration-200" data-widget="pushmenu">
        <i class="fas fa-bars"></i>
      </button>
      <span class="font-semibold text-lg hidden sm:block">
        Hai, {{ Auth::user()->name ?? 'Guest' }}! 👋 Selamat datang
      </span>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-6">
      <span class="text-sm opacity-90" id="datetime"></span>

      <!-- User Dropdown -->
      <div class="relative group">
        <button class="p-2 hover:bg-blue-700 rounded-lg transition duration-200">
          <i class="fas fa-user-circle text-2xl"></i>
        </button>
        <div class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50">
          <a href="#" class="block px-4 py-2 hover:bg-blue-50 rounded-t-lg">
            <i class="fas fa-user mr-2 text-blue-600"></i> Profil
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 rounded-b-lg border-t">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function updateDateTime() {
    const now = new Date();
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const dayName = days[now.getDay()];
    const date = now.getDate().toString().padStart(2, '0'); // 01, 02, dst
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();

    // Jam, menit, detik
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    // Format akhir
    const formatted = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}:${seconds}`;

    document.getElementById('datetime').innerText = formatted;
  }

  // Update setiap 1 detik
  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>