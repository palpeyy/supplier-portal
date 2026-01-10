@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    $('#materialTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "tidak ada data"
      },
      lengthMenu: [10, 25, 50],
    });
  });
</script>
@endsection

@section('page_title', 'Master Data Material')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Master Data</li>
  <li class="breadcrumb-item active">Material</li>
@endsection

@section('isi')
<div class="container-fluid">

  <!-- HEADER -->
  <div class="row mb-2">
    <div class="col-sm-6"></div>
    <div class="col-sm-6 text-right">
      <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalMaterial">
        <i class="fas fa-plus"></i> Tambah Material
      </a>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover text-nowrap" id="materialTable">
        <thead>
          <tr>
            <th>Kode Material</th>
            <th>Nama Material</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>Status</th>
            <th width="100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>MAT-001</td>
            <td>Baut M10</td>
            <td>Sparepart</td>
            <td>PCS</td>
            <td>Rp 2.500</td>
            <td><span class="badge badge-success">Aktif</span></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                  <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td>MAT-002</td>
            <td>Oli Mesin SAE 40</td>
            <td>Consumable</td>
            <td>Liter</td>
            <td>Rp 45.000</td>
            <td><span class="badge badge-success">Aktif</span></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                  <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td>MAT-003</td>
            <td>Ban Truk 900R20</td>
            <td>Sparepart</td>
            <td>Unit</td>
            <td>Rp 3.500.000</td>
            <td><span class="badge badge-secondary">Non Aktif</span></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                  <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
<div class="modal fade" id="modalMaterial" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-box"></i> Tambah Material
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form>
        <div class="modal-body">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Material</label>
                <input type="text" class="form-control" placeholder="MAT-001">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Material</label>
                <input type="text" class="form-control" placeholder="Nama Material">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Kategori</label>
                <select class="form-control">
                  <option>Sparepart</option>
                  <option>Consumable</option>
                  <option>Asset</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Satuan</label>
                <select class="form-control">
                  <option>PCS</option>
                  <option>Unit</option>
                  <option>Liter</option>
                  <option>Kg</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Harga</label>
                <input type="number" class="form-control" placeholder="Harga">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control">
                  <option>Aktif</option>
                  <option>Non Aktif</option>
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
