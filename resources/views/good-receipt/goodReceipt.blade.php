@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    // DataTable GR
    $('#grTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "Tidak ada Good Receipt"
      },
      lengthMenu: [10, 25, 50],
    });

    // Modal View GR
    $(document).on('click', '.btn-view', function() {
      const grNo = $(this).data('gr');
      $('#viewGrModal #grNumber').text(grNo);
      $('#viewGrModal').modal('show');
    });

    // Tambah baris dinamis di modal tambah GR
    $(document).on('click', '#addItemRow', function() {
      let row = `<tr>
        <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
        <td><input type="number" class="form-control form-control-sm" placeholder="Qty Diterima"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Satuan"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Keterangan"></td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
      $('#grItemTable tbody').append(row);
    });

    // Hapus baris item
    $(document).on('click', '.removeRow', function() {
      $(this).closest('tr').remove();
    });
  });
</script>
@endsection

@section('page_title', 'Good Receipt')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Good Receipt</li>
@endsection

@section('isi')
<div class="container-fluid">
  <!-- Header + Add Button -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h5>Daftar Good Receipt</h5>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-primary" data-toggle="modal" data-target="#addGrModal">
        <i class="fas fa-plus"></i> Tambah GR
      </button>
    </div>
  </div>

  <!-- GR Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <table id="grTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No GR</th>
                <th>Tanggal</th>
                <th>No SJ</th>
                <th>Supplier</th>
                <th>Penerima</th>
                <th>Total Item</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
  <tr>
    <td>GR-001</td>
    <td>2026-01-11</td>
    <td>SJ-001</td>
    <td>PT. ABC</td>
    <td>Gudang 1</td>
    <td>5</td>
    <td><span class="badge badge-success">Diterima</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-gr="GR-001" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>GR-002</td>
    <td>2026-01-12</td>
    <td>SJ-002</td>
    <td>PT. XYZ</td>
    <td>Gudang 2</td>
    <td>3</td>
    <td><span class="badge badge-warning">Menunggu Validasi</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-gr="GR-002" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
          <a class="dropdown-item" href="#"><i class="fas fa-trash text-danger"></i> Hapus</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>GR-003</td>
    <td>2026-01-13</td>
    <td>SJ-003</td>
    <td>PT. LMN</td>
    <td>Gudang 3</td>
    <td>7</td>
    <td><span class="badge badge-danger">Ditolak</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item btn-view" data-gr="GR-003" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
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

<!-- Modal Tambah GR -->
<div class="modal fade" id="addGrModal" tabindex="-1" aria-labelledby="addGrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Good Receipt</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Header GR -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Tanggal GR</label>
                <input type="date" class="form-control" name="tanggal">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>No Surat Jalan</label>
                <input type="text" class="form-control" name="no_sj" placeholder="SJ terkait">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Supplier / Pengirim</label>
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
              <h6><i class="fas fa-boxes"></i> Detail Item Barang</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm" id="grItemTable">
                  <thead>
                    <tr>
                      <th>Material</th>
                      <th>Qty Diterima</th>
                      <th>Satuan</th>
                      <th>Keterangan</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="text" class="form-control form-control-sm" placeholder="Material"></td>
                      <td><input type="number" class="form-control form-control-sm" placeholder="Qty Diterima"></td>
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
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan GR</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal View GR -->
<div class="modal fade" id="viewGrModal" tabindex="-1" aria-labelledby="viewGrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Good Receipt: <span id="grNumber"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Material</th>
              <th>Qty Diterima</th>
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
