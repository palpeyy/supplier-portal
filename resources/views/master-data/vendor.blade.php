@extends('layout.main')

@section('page_title', 'Master Vendor')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Master Data</li>
  <li class="breadcrumb-item active">Vendor</li>
@endsection

@section('isi')
<div class="container-fluid">

  <!-- HEADER -->
  <div class="row mb-2">
    <div class="col-sm-6">
      {{-- <h1>Master Vendor</h1> --}}
    </div>
    <div class="col-sm-6 text-right">
<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalVendor">
  <i class="fas fa-plus"></i> Tambah Vendor
</a>

    </div>
  </div>

  <!-- TABLE -->
  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-hover text-nowrap">
        <thead>
          <tr>
            <th>Kode Vendor</th>
            <th>Nama Vendor</th>
            <th>Alamat</th>
            <th>Kontak</th>
            <th>Status</th>
            <th width="120">Aksi</th>
          </tr>
        </thead>
        <tbody>
  <!-- Baris Vendor lama -->
  <tr>
    <td>VND-001</td>
    <td>PT Braja Mukti Cakra</td>
    <td>Bekasi</td>
    <td>021-8899-xxxx</td>
    <td><span class="badge badge-success">Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  {{-- <tr>
    <td>VND-002</td>
    <td>PT Krama Yudha Tiga Berlian Motors</td>
    <td>Jakarta</td>
    <td>021-4602-xxx</td>
    <td><span class="badge badge-secondary">Non Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-info"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-warning"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr> --}}

  <!-- Baris Vendor tambahan sesuai PO -->
  <tr>
    <td>VND-003</td>
    <td>PT. ABC</td>
    <td>Jakarta</td>
    <td>021-8899-0001</td>
    <td><span class="badge badge-success">Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
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
    <td>VND-004</td>
    <td>PT. XYZ</td>
    <td>Jakarta</td>
    <td>021-8899-0002</td>
    <td><span class="badge badge-success">Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
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
    <td>VND-005</td>
    <td>PT. LMN</td>
    <td>Bandung</td>
    <td>022-8899-0003</td>
    <td><span class="badge badge-warning">Non Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
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
    <td>VND-006</td>
    <td>PT. DEF</td>
    <td>Surabaya</td>
    <td>031-8899-0004</td>
    <td><span class="badge badge-success">Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
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
    <td>VND-007</td>
    <td>PT. GHI</td>
    <td>Medan</td>
    <td>061-8899-0005</td>
    <td><span class="badge badge-warning">Non Aktif</span></td>
    <td class="text-center">
      <div class="dropdown">
        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
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


<!-- MODAL TAMBAH VENDOR -->
<div class="modal fade" id="modalVendor" tabindex="-1" role="dialog" aria-labelledby="modalVendorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalVendorLabel">
          <i class="fas fa-building"></i> Tambah Vendor
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post">
        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Vendor</label>
                <input type="text" class="form-control" placeholder="VND-001">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Vendor</label>
                <input type="text" class="form-control" placeholder="Nama Vendor">
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Kota</label>
                <input type="text" class="form-control" placeholder="Jakarta">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Telepon</label>
                <input type="text" class="form-control" placeholder="021-xxxxxxx">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" placeholder="vendor@email.com">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Person</label>
                <input type="text" class="form-control" placeholder="Nama PIC">
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
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection
