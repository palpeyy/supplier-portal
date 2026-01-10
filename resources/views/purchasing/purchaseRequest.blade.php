@extends('layout.main')

@section('scripts')
<script>
  $(document).ready(function() {
    $('#prTable').DataTable({
      autoWidth: false,
      language: {
        emptyTable: "tidak ada data"
      },
      lengthMenu: [10, 25, 50],
    });
  });
</script>
@endsection

@section('page_title', 'Purchase Request')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Transaksi</li>
  <li class="breadcrumb-item active">Purchase Request</li>
@endsection

@section('isi')
<div class="container-fluid">

  <!-- HEADER -->
  <div class="row mb-2">
    <div class="col-sm-6"></div>
    <div class="col-sm-6 text-right">
      <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalPR">
        <i class="fas fa-plus"></i> Buat PR
      </a>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover text-nowrap" id="prTable">
        <thead>
          <tr>
            <th>No PR</th>
            <th>Tanggal</th>
            <th>Departemen</th>
            <th>Pemohon</th>
            <th>Total Item</th>
            <th>Status</th>
            <th width="100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PR-2025-001</td>
            <td>10 Jan 2026</td>
            <td>Purchasing</td>
            <td>Naufal</td>
            <td>3 Item</td>
            <td><span class="badge badge-secondary">Draft</span></td>
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
            <td>PR-2025-002</td>
            <td>09 Jan 2026</td>
            <td>Logistik</td>
            <td>Budi Santoso</td>
            <td>5 Item</td>
            <td><span class="badge badge-warning">Waiting Approval</span></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td>PR-2025-003</td>
            <td>08 Jan 2026</td>
            <td>Finance</td>
            <td>Siti Rahma</td>
            <td>2 Item</td>
            <td><span class="badge badge-success">Approved</span></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<div class="modal fade" id="modalPR" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-file-alt"></i> Buat Purchase Request
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form>
  <div class="modal-body">
    <div class="row">

      <!-- Header PR -->
      <div class="col-md-6">
        <div class="form-group">
          <label>Tanggal PR</label>
          <input type="date" class="form-control">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label>Departemen</label>
          <select class="form-control">
            <option>Purchasing</option>
            <option>Logistik</option>
            <option>Finance</option>
            <option>Admin</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label>Pemohon</label>
          <input type="text" class="form-control" placeholder="Nama Pemohon">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" class="form-control" placeholder="Keterangan PR">
        </div>
      </div>

      <!-- Detail Item -->
      <div class="col-md-12">
        <hr>
        <h6><i class="fas fa-boxes"></i> Detail Item Material</h6>

        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th style="width: 35%">Nama Material</th>
                <th style="width: 15%">Qty</th>
                <th style="width: 15%">Satuan</th>
                <th style="width: 25%">Keterangan</th>
                <th style="width: 10%">Aksi</th>
              </tr>
            </thead>
            <tbody id="itemTable">
              <tr>
                <td>
                  <input type="text" class="form-control form-control-sm" placeholder="Material">
                </td>
                <td>
                  <input type="number" class="form-control form-control-sm" placeholder="Qty">
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm" placeholder="Satuan">
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm" placeholder="Keterangan">
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <button type="button" class="btn btn-sm btn-success">
          <i class="fas fa-plus"></i> Tambah Item
        </button>
      </div>

    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save"></i> Simpan PR
    </button>
  </div>
</form>


    </div>
  </div>
</div>
@endsection
