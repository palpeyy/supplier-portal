<!-- Reusable Card Component -->
<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 px-6 py-4 flex items-center">
        <i class="fas fa-info-circle mr-3 text-blue-600"></i>
        <h5 class="font-semibold text-lg text-gray-800">{{ $title ?? 'Card Title' }}</h5>
    </div>
    <div class="p-6">
        {{ $slot }}
    </div>
</div>