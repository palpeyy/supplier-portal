@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable PO
    $('#poTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada data"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View PO
    $(document).on('click', '.btn-view', function() {
      const poNo = $(this).data('po');
      $('#viewPoModal #poNumber').text(poNo);
      $('#viewPoModal').modal('show');
    });

    // Tambah baris dinamis di modal tambah PO
    $(document).on('click', '#addItemRow', function() {
      let row = `<tr>
        <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Qty"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Satuan"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Keterangan"></td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
      $('#poItemTable tbody').append(row);
    });

    // Hapus baris item
    $(document).on('click', '.removeRow', function() {
      $(this).closest('tr').remove();
    });
  });
</script>
@endsection

@section('page_title', 'Purchase Order')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Purchase Order</li>
@endsection

@section('isi')
<div class="container-fluid">
  <!-- Header + Add Button -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar Purchase Order</h5>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-primary" data-toggle="modal" data-target="#addPoModal">
        <i class="fas fa-plus"></i> Tambah PO
      </button>
    </div>
  </div>

  <!-- PO Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="poTable" class="table table-bordered table-striped">
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
    <td>PO-001</td>
    <td>2026-01-10</td>
    <td>PT. ABC</td>
    <td>5</td>
    <td><span class="badge badge-success">Approved</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-001" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-002</td>
    <td>2026-01-11</td>
    <td>PT. XYZ</td>
    <td>8</td>
    <td><span class="badge badge-warning">Pending</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-002" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-003</td>
    <td>2026-01-12</td>
    <td>PT. LMN</td>
    <td>12</td>
    <td><span class="badge badge-danger">Rejected</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-003" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-004</td>
    <td>2026-01-13</td>
    <td>PT. DEF</td>
    <td>7</td>
    <td><span class="badge badge-success">Approved</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-004" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-005</td>
    <td>2026-01-14</td>
    <td>PT. GHI</td>
    <td>10</td>
    <td><span class="badge badge-warning">Pending</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-005" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-006</td>
    <td>2026-01-15</td>
    <td>PT. JKL</td>
    <td>6</td>
    <td><span class="badge badge-success">Approved</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-006" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>PO-007</td>
    <td>2026-01-16</td>
    <td>PT. MNO</td>
    <td>9</td>
    <td><span class="badge badge-warning">Pending</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-po="PO-007" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
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

<!-- Modal Tambah PO -->
<div class="modal fade" id="addPoModal" tabindex="-1" aria-labelledby="addPoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Purchase Order</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Header PO -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal PO</label>
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
                <label>Keterangan</label>
                <input type="text" class="form-control" name="keterangan" placeholder="Keterangan PO">
              </div>
            </div>

            <!-- Detail Item -->
            <div class="col-md-12 mt-3">
              <h6><i class="fas fa-boxes"></i> Detail Item Material</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm" id="poItemTable">
                  <thead>
                    <tr>
                      <th>Material</th>
                      <th>Qty</th>
                      <th>Satuan</th>
                      <th>Keterangan</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Qty"></td>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Satuan"></td>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Keterangan"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-success btn-sm" id="addItemRow">
                <i class="fas fa-plus"></i> Tambah Item
              </button>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan PO</button>
        </div>
      </form>
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
              <td>Material A</td>
              <td>10</td>
              <td>pcs</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Material B</td>
              <td>5</td>
              <td>kg</td>
              <td>Segera dikirim</td>
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
