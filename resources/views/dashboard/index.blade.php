@extends('layout.main')

@section('page_title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">

  <!-- =========================
       HEADER
  ========================== -->

  <!-- =========================
       ROW 0 : STATISTIK RINGKAS
  ========================== -->
  <div class="grid grid-cols-1 md:grid-cols-2 {{ $isSupplier ? 'lg:grid-cols-4' : 'lg:grid-cols-5' }} gap-4 mb-6">
    <!-- Card Total PO -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-white text-sm font-semibold">Total Purchase Order</p>
            <h3 class="text-white text-3xl font-bold mt-2">{{ $totalPO }}</h3>
          </div>
          <i class="fas fa-file-invoice text-white opacity-20" style="font-size: 3rem;"></i>
        </div>
      </div>
      <div class="px-6 py-3 border-t border-gray-200">
        <small class="text-gray-600">{{ $isSupplier ? 'Purchase Order Anda' : 'Semua Purchase Order' }}</small>
      </div>
    </div>

    @unless($isSupplier)
    <!-- Card Total Supplier (hanya Admin/internal) -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-white text-sm font-semibold">Total Supplier</p>
            <h3 class="text-white text-3xl font-bold mt-2">{{ $totalSupplier }}</h3>
          </div>
          <i class="fas fa-building text-white opacity-20" style="font-size: 3rem;"></i>
        </div>
      </div>
      <div class="px-6 py-3 border-t border-gray-200">
        <small class="text-gray-600">Vendor aktif</small>
      </div>
    </div>
    @endunless

    <!-- Card Total Nilai PO -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-white text-sm font-semibold">Total Nilai</p>
            <h3 class="text-white text-2xl font-bold mt-2">Rp {{ number_format($totalValue / 1000000000, 2) }}M</h3>
          </div>
          <i class="fas fa-money-bill-wave text-white opacity-20" style="font-size: 3rem;"></i>
        </div>
      </div>
      <div class="px-6 py-3 border-t border-gray-200">
        <small class="text-gray-600">{{ $isSupplier ? 'Nilai item PO Anda' : 'Total nilai item' }}</small>
      </div>
    </div>

    <!-- Card PO Completed -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-white text-sm font-semibold">PO Selesai</p>
            <h3 class="text-white text-3xl font-bold mt-2">{{ $poCompleted }}</h3>
          </div>
          <i class="fas fa-check-circle text-white opacity-20" style="font-size: 3rem;"></i>
        </div>
      </div>
      <div class="px-6 py-3 border-t border-gray-200">
        <small class="text-gray-600">Barang diterima</small>
      </div>
    </div>

    <!-- Card 5 -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-white text-sm font-semibold">Invoice Selesai</p>
            <h3 class="text-white text-3xl font-bold mt-2">{{ $invoiceCompleted }}</h3>
          </div>
          <i class="fas fa-check-circle text-white opacity-20" style="font-size: 3rem;"></i>
        </div>
      </div>
      <div class="px-6 py-3 border-t border-gray-200">
        <small class="text-gray-600">Sampai validasi Admin</small>
      </div>
    </div>
  </div>

  <!-- =========================
       ROW 1 : BAR CHART
  ========================== -->
  <div class="mb-6">
    <div class="w-full">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex items-center bg-gray-50 border-b border-gray-200 px-6 py-4">
          <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
          <strong class="text-gray-800">Total Transaksi per Vendor (Invoice Tervalidasi)</strong>
        </div>
        <div class="p-6">
          <canvas id="barVendorChart" height="120"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================
       ROW 2 : LINE CHART
  ========================== -->
  <div class="mb-6">
    <div class="w-full">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex items-center bg-gray-50 border-b border-gray-200 px-6 py-4">
          <i class="fas fa-chart-line mr-2 text-yellow-600"></i>
          <strong class="text-gray-800">Tren PO Diterima Bulanan (6 Bulan Terakhir)</strong>
        </div>
        <div class="p-6">
          <canvas id="lineMonthlyChart" height="90"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 3 dihapus: modul pembayaran -->

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  /* =========================
   REAL DATA FROM SERVER
========================= */
  const vendorLabels = {{ \Illuminate\Support\Js::from($vendorLabels) }};
  const vendorTotals = {{ \Illuminate\Support\Js::from($vendorValues) }};

  const monthlyLabels = {{ \Illuminate\Support\Js::from($monthlyLabels) }};
  const monthlyTotals = {{ \Illuminate\Support\Js::from($monthlyValues) }};

  function formatRupiahShort(value) {
    const amount = Number(value) || 0;
    if (amount >= 1_000_000_000) {
      return (amount / 1_000_000_000).toFixed(2) + ' M';
    }
    if (amount >= 1_000_000) {
      return (amount / 1_000_000).toFixed(2) + ' Jt';
    }
    if (amount >= 1_000) {
      return (amount / 1_000).toFixed(2) + ' Rb';
    }
    return amount.toLocaleString('id-ID');
  }

  function formatRupiahFull(value) {
    return 'Rp ' + (Number(value) || 0).toLocaleString('id-ID');
  }

  /* =========================
     BAR CHART
  ========================= */
  new Chart(barVendorChart, {
    type: 'bar',
    data: {
      labels: vendorLabels.length > 0 ? vendorLabels : ['Tidak ada data'],
      datasets: [{
        label: 'Total Transaksi (Invoice Tervalidasi)',
        data: vendorTotals.length > 0 ? vendorTotals : [0],
        backgroundColor: [
          '#3b82f6',
          '#10b981',
          '#f59e0b',
          '#ef4444',
          '#8b5cf6',
          '#ec4899',
          '#14b8a6',
          '#f97316',
          '#6366f1',
          '#06b6d4'
        ],
        borderWidth: 1,
        borderColor: '#e5e7eb'
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: (context) => formatRupiahFull(context.parsed.y)
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => formatRupiahShort(value)
          }
        }
      }
    }
  });

  /* =========================
     LINE CHART
  ========================= */
  new Chart(lineMonthlyChart, {
    type: 'line',
    data: {
      labels: monthlyLabels.length > 0 ? monthlyLabels : ['Tidak ada data'],
      datasets: [{
        label: 'Jumlah PO Diterima',
        data: monthlyTotals.length > 0 ? monthlyTotals : [0],
        tension: 0.35,
        fill: true,
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderColor: '#3b82f6',
        borderWidth: 3,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => value + ' PO'
          }
        }
      }
    }
  });
</script>
@endpush