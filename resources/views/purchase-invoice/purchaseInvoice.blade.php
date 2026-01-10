@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable PI
    $('#piTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada Purchase Invoice"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View PI
    $(document).on('click', '.btn-view', function() {
      const piNo = $(this).data('pi');
      $('#viewPiModal #piNumber').text(piNo);
      $('#viewPiModal').modal('show');
    });

    // Tambah baris dinamis di modal tambah PI
    $(document).on('click', '#addItemRow', function() {
      let row = `<tr>
        <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Qty"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Satuan"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Harga Satuan"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Total"></td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
      $('#piItemTable tbody').append(row);
    });

    // Hapus baris item
    $(document).on('click', '.removeRow', function() {
      $(this).closest('tr').remove();
    });
  });
</script>
@endsection

@section('page_title', 'Purchase Invoice')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Purchase Invoice</li>
@endsection

@section('isi')
<div class="container-fluid">
  <!-- Header + Add Button -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar Purchase Invoice</h5>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-primary" data-toggle="modal" data-target="#addPiModal">
        <i class="fas fa-plus"></i> Tambah PI
      </button>
    </div>
  </div>

  <!-- PI Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="piTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No PI</th>
                <th>Tanggal</th>
                <th>No PO</th>
                <th>Supplier</th>
                <th>Total Item</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>PI-001</td>
                <td>2026-01-11</td>
                <td>PO-001</td>
                <td>PT. ABC</td>
                <td>5</td>
                <td>Rp 10,000,000</td>
                <td><span class="badge badge-success">Paid</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-pi="PI-001" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>

              <tr>
                <td>PI-002</td>
                <td>2026-01-12</td>
                <td>PO-002</td>
                <td>PT. XYZ</td>
                <td>3</td>
                <td>Rp 7,500,000</td>
                <td><span class="badge badge-warning">Pending</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-pi="PI-002" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>

              <tr>
                <td>PI-003</td>
                <td>2026-01-13</td>
                <td>PO-003</td>
                <td>PT. LMN</td>
                <td>4</td>
                <td>Rp 12,000,000</td>
                <td><span class="badge badge-danger">Overdue</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-pi="PI-003" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>

              <tr>
                <td>PI-004</td>
                <td>2026-01-14</td>
                <td>PO-004</td>
                <td>PT. DEF</td>
                <td>6</td>
                <td>Rp 15,000,000</td>
                <td><span class="badge badge-success">Paid</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-pi="PI-004" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>

              <tr>
                <td>PI-005</td>
                <td>2026-01-15</td>
                <td>PO-005</td>
                <td>PT. GHI</td>
                <td>2</td>
                <td>Rp 5,000,000</td>
                <td><span class="badge badge-warning">Pending</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-pi="PI-005" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah PI -->
<div class="modal fade" id="addPiModal" tabindex="-1" aria-labelledby="addPiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Purchase Invoice</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Tanggal PI</label>
                <input type="date" class="form-control" name="tanggal">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>No PO</label>
                <input type="text" class="form-control" name="no_po" placeholder="PO terkait">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Supplier</label>
                <input type="text" class="form-control" name="supplier" placeholder="Nama Supplier">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Penerima / Gudang</label>
                <input type="text" class="form-control" name="penerima" placeholder="Nama Penerima">
              </div>
            </div>

            <!-- Detail Item -->
            <div class="col-md-12 mt-3">
              <h6><i class="fas fa-boxes"></i> Detail Item Material</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm" id="piItemTable">
                  <thead>
                    <tr>
                      <th>Material</th>
                      <th>Qty</th>
                      <th>Satuan</th>
                      <th>Harga Satuan</th>
                      <th>Total</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Qty"></td>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Satuan"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Harga Satuan"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Total"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-success btn-sm" id="addItemRow"><i class="fas fa-plus"></i> Tambah Item</button>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan PI</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal View PI -->
<div class="modal fade" id="viewPiModal" tabindex="-1" aria-labelledby="viewPiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Purchase Invoice: <span id="piNumber"></span></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Material</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Harga Satuan</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Material A</td>
              <td>10</td>
              <td>pcs</td>
              <td>2,000,000</td>
              <td>Rp 20,000,000</td>
            </tr>
            <tr>
              <td>Material B</td>
              <td>5</td>
              <td>kg</td>
              <td>1,000,000</td>
              <td>Rp 5,000,000</td>
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
