@extends('layout.main')

@section('page_title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">

  <!-- =========================
       HEADER
  ========================== -->
  <div class="mb-6">
    <div class="w-full">
      <h4 class="mb-0 font-bold text-2xl text-gray-800">Ringkasan Transaksi</h4>
      <small class="text-sm text-gray-500">
        Visualisasi data transaksi perusahaan (dummy)
      </small>
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
          <strong class="text-gray-800">Total Transaksi per Vendor</strong>
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
          <strong class="text-gray-800">Tren Transaksi Bulanan</strong>
        </div>
        <div class="p-6">
          <canvas id="lineMonthlyChart" height="90"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  /* =========================
   DUMMY DATA (VIEW ONLY)
========================= */
  const vendorLabels = ['Vendor A', 'Vendor B', 'Vendor C', 'Vendor D'];
  const vendorTotals = [120000000, 85000000, 45000000, 30000000];

  const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
  const monthlyTotals = [50, 70, 65, 90, 80, 110];

  /* =========================
     BAR CHART
  ========================= */
  new Chart(barVendorChart, {
    type: 'bar',
    data: {
      labels: vendorLabels,
      datasets: [{
        label: 'Total (Juta Rupiah)',
        data: vendorTotals.map(v => v / 1000000),
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => value + ' jt'
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
      labels: monthlyLabels,
      datasets: [{
        label: 'Total (Juta Rupiah)',
        data: monthlyTotals,
        tension: 0.35,
        fill: false
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        y: {
          ticks: {
            callback: value => value + ' jt'
          }
        }
      }
    }
  });
</script>
@endpush