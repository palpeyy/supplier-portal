@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable Approval PO
    $('#approvalPoTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada PO menunggu approval"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View PO
    $(document).on('click', '.btn-view', function() {
      const poNo = $(this).data('po');
      $('#viewPoModal #poNumber').text(poNo);
      $('#viewPoModal').modal('show');
    });

    // Approve / Reject dari modal
    $(document).on('click', '#btnApprove', function() {
      const poNo = $('#poNumber').text();
      if(confirm(`Setujui PO ${poNo}?`)) {
        alert(`PO ${poNo} disetujui!`);
        $('#viewPoModal').modal('hide');
        // Tambahkan ajax/post ke controller untuk approve
      }
    });

    $(document).on('click', '#btnReject', function() {
      const poNo = $('#poNumber').text();
      const reason = prompt(`Tolak PO ${poNo}, tulis alasan:`);
      if(reason) {
        alert(`PO ${poNo} ditolak dengan alasan: ${reason}`);
        $('#viewPoModal').modal('hide');
        // Tambahkan ajax/post ke controller untuk reject
      }
    });
  });
</script>
@endsection

@section('page_title', 'Approval Purchase Order')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Approval PO</li>
@endsection

@section('isi')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar PO Menunggu Approval</h5>
    </div>
  </div>

  <!-- Approval PO Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="approvalPoTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No PO</th>
                <th>Tanggal PO</th>
                <th>Supplier</th>
                <th>Total Item</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>PO-002</td>
                <td>2026-01-11</td>
                <td>PT. XYZ</td>
                <td>3</td>
                <td><span class="badge badge-warning">Menunggu</span></td>
                <td class="text-center">
                  <button class="btn btn-info btn-sm btn-view" data-po="PO-002">
                    <i class="fas fa-eye"></i> Detail
                  </button>
                </td>
              </tr>

              <tr>
                <td>PO-003</td>
                <td>2026-01-12</td>
                <td>PT. LMN</td>
                <td>7</td>
                <td><span class="badge badge-warning">Menunggu</span></td>
                <td class="text-center">
                  <button class="btn btn-info btn-sm btn-view" data-po="PO-003">
                    <i class="fas fa-eye"></i> Detail
                  </button>
                </td>
              </tr>

              <!-- Loop data PO menunggu approval dari database -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal View PO -->
<div class="modal fade" id="viewPoModal" tabindex="-1" aria-labelledby="viewPoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Purchase Order: <span id="poNumber"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Material</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Material X</td>
              <td>5</td>
              <td>pcs</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Material Y</td>
              <td>2</td>
              <td>kg</td>
              <td>Segera dikirim</td>
            </tr>
            <!-- Loop detail item PO dari database -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnApprove">
          <i class="fas fa-check"></i> Approve
        </button>
        <button type="button" class="btn btn-danger" id="btnReject">
          <i class="fas fa-times"></i> Reject
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
