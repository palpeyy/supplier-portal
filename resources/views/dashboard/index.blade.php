@extends('layout.main')

@section('page_title', 'Dashboard')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('isi')
<div class="container-fluid">

  <!-- =========================
       KPI BOX
  ========================== -->
  <div class="row">

    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3>4</h3>
          <p>PO Aktif</p>
        </div>
        <div class="icon">
          <i class="fas fa-file-contract"></i>
        </div>
        <a href="#" class="small-box-footer">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>12</h3>
          <p>PO Selesai</p>
        </div>
        <div class="icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <a href="#" class="small-box-footer">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>7</h3>
          <p>Surat Jalan</p>
        </div>
        <div class="icon">
          <i class="fas fa-truck"></i>
        </div>
        <a href="#" class="small-box-footer">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>3</h3>
          <p>Invoice Pending</p>
        </div>
        <div class="icon">
          <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <a href="#" class="small-box-footer">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

  </div>

  <!-- =========================
       ROW TABLE
  ========================== -->
  <div class="row">

    <!-- PO TERBARU -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-file-contract"></i> PO Terbaru
          </h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>No PO</th>
                <th>Tanggal</th>
                <th>Nilai</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>PO-2026-001</td>
                <td>05 Jan 2026</td>
                <td>Rp 150.000.000</td>
                <td><span class="badge badge-info">Aktif</span></td>
              </tr>
              <tr>
                <td>PO-2026-002</td>
                <td>08 Jan 2026</td>
                <td>Rp 75.000.000</td>
                <td><span class="badge badge-warning">Proses</span></td>
              </tr>
              <tr>
                <td>PO-2026-003</td>
                <td>10 Jan 2026</td>
                <td>Rp 210.000.000</td>
                <td><span class="badge badge-success">Selesai</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- STATUS PENGIRIMAN -->
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-truck"></i> Status Pengiriman
          </h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>No SJ</th>
                <th>No PO</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>SJ-001</td>
                <td>PO-2026-001</td>
                <td><span class="badge badge-warning">Dikirim</span></td>
              </tr>
              <tr>
                <td>SJ-002</td>
                <td>PO-2026-002</td>
                <td><span class="badge badge-info">Dalam Proses</span></td>
              </tr>
              <tr>
                <td>SJ-003</td>
                <td>PO-2026-003</td>
                <td><span class="badge badge-success">Diterima</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <!-- =========================
       INVOICE
  ========================== -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-file-invoice"></i> Invoice Terbaru
          </h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No Invoice</th>
                <th>No PO</th>
                <th>Tanggal</th>
                <th>Nilai</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>INV-001</td>
                <td>PO-2026-001</td>
                <td>12 Jan 2026</td>
                <td>Rp 150.000.000</td>
                <td><span class="badge badge-danger">Pending</span></td>
              </tr>
              <tr>
                <td>INV-002</td>
                <td>PO-2026-003</td>
                <td>11 Jan 2026</td>
                <td>Rp 210.000.000</td>
                <td><span class="badge badge-success">Paid</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
