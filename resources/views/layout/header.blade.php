<!-- Navbar with Tailwind -->
<div class="bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg px-0 py-3 w-full">
  <div class="flex items-center justify-between px-6">
    <!-- Left Side -->
    <div class="flex items-center space-x-3">
      <button class="p-1 hover:bg-red-700 rounded-lg transition duration-200" data-widget="pushmenu">
        <i class="fas fa-bars text-lg"></i>
      </button>
      <div class="hidden sm:block">
        <h1 class="font-bold text-sm mb-0">
          Hai, {{ Auth::user()->name ?? 'Guest' }}! 👋
        </h1>
        <p class="text-red-100 text-xs">Selamat datang di Portal Supplier</p>
      </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-4">
      <span class="text-xs font-semibold bg-red-500 px-2 py-1 rounded-md" id="datetime"></span>

      <!-- User Dropdown -->
      <div class="relative inline-block text-left" id="userDropdown">
  <button class="p-1 hover:bg-red-700 rounded-lg transition duration-200 focus:outline-none" id="userDropdownBtn" type="button">
    <i class="fas fa-user-circle text-lg"></i>
  </button>

  <div class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-xl opacity-0 invisible transition-all duration-200 z-50 border border-gray-100" 
       id="userDropdownMenu" 
       style="display: none;">
    
    <div class="py-1">
      <a href="#" class="block px-4 py-2 hover:bg-red-50 transition duration-150">
        <i class="fas fa-user mr-2 text-red-600"></i> Profil
      </a>
      
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 border-t border-gray-100 transition duration-150">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </button>
      </form>
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

  // User Dropdown Toggle
  document.addEventListener('DOMContentLoaded', function() {
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    const userDropdown = document.getElementById('userDropdown');

    // Toggle dropdown saat button diklik
    userDropdownBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      userDropdownMenu.style.display = userDropdownMenu.style.display === 'none' ? 'block' : 'none';
      userDropdownMenu.classList.toggle('opacity-0');
      userDropdownMenu.classList.toggle('invisible');
      userDropdownMenu.classList.toggle('opacity-100');
      userDropdownMenu.classList.toggle('visible');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
      if (!userDropdown.contains(e.target)) {
        userDropdownMenu.style.display = 'none';
        userDropdownMenu.classList.add('opacity-0', 'invisible');
        userDropdownMenu.classList.remove('opacity-100', 'visible');
      }
    });
  });
</script>