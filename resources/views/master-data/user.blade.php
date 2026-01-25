@extends('layout.main')

@section('scripts')

<script>
  document.querySelectorAll('.fa-eye').forEach(icon => {
    icon.addEventListener('click', function () {
      const input = this.closest('.input-group').querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
      this.classList.toggle('fa-eye-slash');
    });
  });
</script>

<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            autoWidth: false,
            "language": {
                "emptyTable": "tidak ada data"
            },
            "lengthMenu": [10, 25, 50],
        });
    });

</script>

@endsection

@section('page_title', 'Staff PT KTB')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Master Data</li>
  <li class="breadcrumb-item active">User PT</li>
@endsection

@section('isi')
<div class="container-fluid">

  <!-- HEADER -->
  <div class="row mb-2">
    <div class="col-sm-6"></div>
    <div class="col-sm-6 text-right">
      <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200" data-toggle="modal" data-target="#modalUserPT">
        <i class="fas fa-plus mr-2"></i> Tambah User PT
      </a>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover text-nowrap" id="userTable">
        <thead>
          <tr>
            <th>NIP</th>
            <th>Nama User</th>
            <th>Email</th>
            <th>Departemen</th>
            <th>Status</th>
            <th width="100">Aksi</th>
          </tr>
        </thead>
        <tbody>
  <tr>
    <td>EMP-001</td>
    <td>Naufal</td>
    <td>andi@company.co.id</td>
    <td>Purchasing</td>
    <td><span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>EMP-002</td>
    <td>Siti Rahma</td>
    <td>siti@company.co.id</td>
    <td>Finance</td>
    <td><span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded">Non Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>EMP-003</td>
    <td>Budi Santoso</td>
    <td>budi@company.co.id</td>
    <td>Logistik</td>
    <td><span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>EMP-004</td>
    <td>Rina Oktaviani</td>
    <td>rina@company.co.id</td>
    <td>Admin</td>
    <td><span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>EMP-005</td>
    <td>Doni Prasetyo</td>
    <td>doni@company.co.id</td>
    <td>Purchasing</td>
    <td><span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded">Non Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td>EMP-006</td>
    <td>Maya Putri</td>
    <td>maya@company.co.id</td>
    <td>Finance</td>
    <td><span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Aktif</span></td>
    <td class="text-center">
      <div class="relative inline-block text-left">
        <button class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm" data-toggle="dropdown">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue-600 mr-2"></i> Detail</a>
          <a class="dropdown-item" href="#"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit</a>
        </div>
      </div>
    </td>
  </tr>
</tbody>

      </table>
    </div>
  </div>

</div>

<!-- MODAL TAMBAH USER PT -->
<div class="modal fade" id="modalUserPT" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-user"></i> Tambah User PT
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
                <label>NIP</label>
                <input type="text" class="form-control" placeholder="EMP-001">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Nama User</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap">
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
                <label>Email</label>
                <input type="email" class="form-control" placeholder="user@company.co.id">
              </div>
            </div>

            <!-- PASSWORD -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" placeholder="Password">
                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="fas fa-eye"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" class="form-control" placeholder="Ulangi Password">
              </div>
            </div>

            <div class="col-md-12">
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
          <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
          <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
