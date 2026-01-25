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
  <li class="flex items-center"><span class="text-gray-500">/</span></li>
  <li class="flex items-center"><a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 ml-2">Master Data</a></li>
  <li class="flex items-center"><span class="text-gray-500 mx-2">/</span></li>
  <li class="flex items-center"><span class="text-gray-700 ml-2">Material</span></li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">

  <!-- HEADER -->
  <div class="flex flex-wrap -mx-2 mb-4">
    <div class="flex-1 px-2 w-full sm:w-1/2"></div>
    <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
      <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalMaterial">
        <i class="fas fa-plus mr-2"></i> Tambah Material
      </button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full border-collapse" id="materialTable">
        <thead class="bg-gray-100 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kode Material</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Material</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kategori</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Satuan</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Harga</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 w-24">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">MAT-001</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">Baut M10</td>
            <td class="px-6 py-3 text-sm text-gray-600">Sparepart</td>
            <td class="px-6 py-3 text-sm text-gray-600">PCS</td>
            <td class="px-6 py-3 text-sm text-gray-600">Rp 2.500</td>
            <td class="px-6 py-3 text-sm"><span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Aktif</span></td>
            <td class="px-6 py-3 text-sm flex gap-1">
              <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Detail">
                <i class="fas fa-eye"></i>
              </a>
              <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>

          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">MAT-002</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">Oli Mesin SAE 40</td>
            <td class="px-6 py-3 text-sm text-gray-600">Consumable</td>
            <td class="px-6 py-3 text-sm text-gray-600">Liter</td>
            <td class="px-6 py-3 text-sm text-gray-600">Rp 45.000</td>
            <td class="px-6 py-3 text-sm"><span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Aktif</span></td>
            <td class="px-6 py-3 text-sm flex gap-1">
              <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Detail">
                <i class="fas fa-eye"></i>
              </a>
              <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>

          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">MAT-003</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">Ban Truk 900R20</td>
            <td class="px-6 py-3 text-sm text-gray-600">Sparepart</td>
            <td class="px-6 py-3 text-sm text-gray-600">Unit</td>
            <td class="px-6 py-3 text-sm text-gray-600">Rp 3.500.000</td>
            <td class="px-6 py-3 text-sm"><span class="inline-block bg-yellow-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Non Aktif</span></td>
            <td class="px-6 py-3 text-sm flex gap-1">
              <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Detail">
                <i class="fas fa-eye"></i>
              </a>
              <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200" href="#" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
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
