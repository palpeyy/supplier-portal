@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable Payment
    $('#paymentTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada pembayaran"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View Payment
    $(document).on('click', '.btn-view', function() {
      const paymentNo = $(this).data('payment');
      $('#viewPaymentModal #paymentNumber').text(paymentNo);
      $('#viewPaymentModal').modal('show');
    });

    // Tambah baris dinamis di modal tambah Payment (misal untuk beberapa invoice)
    $(document).on('click', '#addInvoiceRow', function() {
      let row = `<tr>
        <td><input type="text" class="form-control form-control-sm" placeholder="No PI"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Amount"></td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
      $('#paymentInvoiceTable tbody').append(row);
    });

    // Hapus baris item
    $(document).on('click', '.removeRow', function() {
      $(this).closest('tr').remove();
    });
  });
</script>
@endsection

@section('page_title', 'Daftar Pembayaran')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Pembayaran</li>
@endsection

@section('isi')
<div class="container-fluid">
  <!-- Header + Add Button -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar Pembayaran</h5>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-primary" data-toggle="modal" data-target="#addPaymentModal">
        <i class="fas fa-plus"></i> Tambah Pembayaran
      </button>
    </div>
  </div>

  <!-- Payment Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="paymentTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No Payment</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
  <tr>
    <td>PAY-001</td>
    <td>2026-01-11</td>
    <td>PT. ABC</td>
    <td>Rp 10,000,000</td>
    <td><span class="badge badge-success">Lunas</span></td>
    <td class="text-center">
      <a href="#" class="btn btn-sm btn-info btn-view" data-payment="PAY-001">Detail</a>
    </td>
  </tr>

  <tr>
    <td>PAY-002</td>
    <td>2026-01-12</td>
    <td>PT. XYZ</td>
    <td>Rp 7,500,000</td>
    <td><span class="badge badge-warning">Belum Lunas</span></td>
    <td class="text-center">
      <a href="#" class="btn btn-sm btn-info btn-view" data-payment="PAY-002">Detail</a>
    </td>
  </tr>

  <tr>
    <td>PAY-003</td>
    <td>2026-01-13</td>
    <td>PT. LMN</td>
    <td>Rp 12,000,000</td>
    <td><span class="badge badge-danger">Belum Bayar</span></td>
    <td class="text-center">
      <a href="#" class="btn btn-sm btn-info btn-view" data-payment="PAY-003">Detail</a>
    </td>
  </tr>

  <tr>
    <td>PAY-004</td>
    <td>2026-01-14</td>
    <td>PT. DEF</td>
    <td>Rp 15,000,000</td>
    <td><span class="badge badge-success">Lunas</span></td>
    <td class="text-center">
      <a href="#" class="btn btn-sm btn-info btn-view" data-payment="PAY-004">Detail</a>
    </td>
  </tr>

  <tr>
    <td>PAY-005</td>
    <td>2026-01-15</td>
    <td>PT. GHI</td>
    <td>Rp 5,000,000</td>
    <td><span class="badge badge-warning">Belum Lunas</span></td>
    <td class="text-center">
      <a href="#" class="btn btn-sm btn-info btn-view" data-payment="PAY-005">Detail</a>
    </td>
  </tr>
</tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Payment -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pembayaran</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal Pembayaran</label>
                <input type="date" class="form-control" name="tanggal">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Supplier</label>
                <input type="text" class="form-control" name="supplier" placeholder="Nama Supplier">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Metode Pembayaran</label>
                <select class="form-control" name="metode">
                  <option value="">-- Pilih Metode --</option>
                  <option value="Transfer">Transfer</option>
                  <option value="Cash">Cash</option>
                  <option value="Cek">Cek</option>
                </select>
              </div>
            </div>

            <!-- Detail Invoice -->
            <div class="col-md-12 mt-3">
              <h6><i class="fas fa-file-invoice-dollar"></i> Invoice Terkait</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm" id="paymentInvoiceTable">
                  <thead>
                    <tr>
                      <th>No PI</th>
                      <th>Amount</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="text" class="form-control form-control-sm" placeholder="No PI"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Amount"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-success btn-sm" id="addInvoiceRow"><i class="fas fa-plus"></i> Tambah Invoice</button>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pembayaran</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal View Payment -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pembayaran: <span id="paymentNumber"></span></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>No PI</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>PI-001</td>
              <td>Rp 10,000,000</td>
              <td>Lunas</td>
            </tr>
            <tr>
              <td>PI-002</td>
              <td>Rp 7,500,000</td>
              <td>Partial</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
