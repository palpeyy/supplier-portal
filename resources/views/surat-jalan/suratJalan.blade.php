@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable SJ
    $('#sjTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada Surat Jalan"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View SJ
    $(document).on('click', '.btn-view', function() {
      const sjNo = $(this).data('sj');
      $('#viewSjModal #sjNumber').text(sjNo);
      $('#viewSjModal').modal('show');
    });

    // Tambah baris dinamis di modal tambah SJ
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
      $('#sjItemTable tbody').append(row);
    });

    // Hapus baris item
    $(document).on('click', '.removeRow', function() {
      $(this).closest('tr').remove();
    });
  });
</script>
@endsection

@section('page_title', 'Surat Jalan')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Surat Jalan</li>
@endsection

@section('isi')
<div class="container-fluid">
  <!-- Header + Add Button -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar Surat Jalan</h5>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-primary" data-toggle="modal" data-target="#addSjModal">
        <i class="fas fa-plus"></i> Tambah Surat Jalan
      </button>
    </div>
  </div>

  <!-- SJ Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="sjTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No SJ</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Penerima</th>
                <th>Total Item</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>SJ-001</td>
                <td>2026-01-10</td>
                <td>PT. ABC</td>
                <td>Gudang 1</td>
                <td>5</td>
                <td><span class="badge badge-info">Dikirim</span></td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-toggle="dropdown">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item btn-view" data-sj="SJ-001" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                      <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
                    </div>
                  </div>
                </td>
              </tr>
              <!-- Loop data SJ dari database -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah SJ -->
<div class="modal fade" id="addSjModal" tabindex="-1" aria-labelledby="addSjModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Surat Jalan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Header SJ -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" class="form-control" name="tanggal">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Supplier / Pengirim</label>
                <input type="text" class="form-control" name="supplier" placeholder="Nama Supplier">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Penerima</label>
                <input type="text" class="form-control" name="penerima" placeholder="Nama Penerima">
              </div>
            </div>

            <!-- Detail Item -->
            <div class="col-md-12 mt-3">
              <h6><i class="fas fa-boxes"></i> Detail Item Barang</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm" id="sjItemTable">
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
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan SJ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal View SJ -->
<div class="modal fade" id="viewSjModal" tabindex="-1" aria-labelledby="viewSjModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Surat Jalan: <span id="sjNumber"></span></h5>
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
            <!-- Loop detail item dari database -->
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
