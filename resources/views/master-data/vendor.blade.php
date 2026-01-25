@extends('layout.main')

@section('page_title', 'Master Vendor')

@section('breadcrumb')
<li class="flex items-center"><span class="text-gray-500">/</span></li>
<li class="flex items-center"><a href="#" class="text-blue-600 hover:text-blue-800 ml-2">Master Data</a></li>
<li class="flex items-center"><span class="text-gray-500 mx-2">/</span></li>
<li class="flex items-center"><span class="text-gray-700 ml-2">Vendor</span></li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
  <!-- HEADER -->
  <div class="flex flex-wrap -mx-2 mb-4">
    <div class="flex-1 px-2 w-full sm:w-1/2"></div>
    <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
      <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalVendor">
        <i class="fas fa-plus mr-2"></i> Tambah Vendor
      </button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kode Vendor</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Vendor</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Alamat</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kontak</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 w-24">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Baris Vendor lama -->
          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">VND-001</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT Braja Mukti Cakra</td>
            <td class="px-6 py-3 text-sm text-gray-600">Bekasi</td>
            <td class="px-6 py-3 text-sm text-gray-600">021-8899-xxxx</td>
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

          <!-- Baris Vendor tambahan sesuai PO -->
          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">VND-003</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT. ABC</td>
            <td class="px-6 py-3 text-sm text-gray-600">Jakarta</td>
            <td class="px-6 py-3 text-sm text-gray-600">021-8899-0001</td>
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
            <td class="px-6 py-3 text-sm text-gray-600">VND-004</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT. XYZ</td>
            <td class="px-6 py-3 text-sm text-gray-600">Jakarta</td>
            <td class="px-6 py-3 text-sm text-gray-600">021-8899-0002</td>
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
            <td class="px-6 py-3 text-sm text-gray-600">VND-005</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT. LMN</td>
            <td class="px-6 py-3 text-sm text-gray-600">Bandung</td>
            <td class="px-6 py-3 text-sm text-gray-600">022-8899-0003</td>
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

          <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-sm text-gray-600">VND-006</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT. DEF</td>
            <td class="px-6 py-3 text-sm text-gray-600">Surabaya</td>
            <td class="px-6 py-3 text-sm text-gray-600">031-8899-0004</td>
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
            <td class="px-6 py-3 text-sm text-gray-600">VND-007</td>
            <td class="px-6 py-3 text-sm text-gray-600 font-medium">PT. GHI</td>
            <td class="px-6 py-3 text-sm text-gray-600">Medan</td>
            <td class="px-6 py-3 text-sm text-gray-600">061-8899-0005</td>
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


<!-- MODAL TAMBAH VENDOR -->
<div class="modal fade" id="modalVendor" tabindex="-1" role="dialog" aria-labelledby="modalVendorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-white rounded-lg shadow-lg">

      <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg border-b border-blue-700">
        <h5 class="font-semibold text-lg" id="modalVendorLabel">
          <i class="fas fa-building mr-2"></i> Tambah Vendor
        </h5>
        <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post">
        <div class="px-6 py-4">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Kode Vendor</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="VND-001">
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Nama Vendor</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Nama Vendor">
            </div>

            <div class="md:col-span-2">
              <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
              <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" rows="2" placeholder="Alamat lengkap"></textarea>
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Kota</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Jakarta">
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Telepon</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="021-xxxxxxx">
            </div>

            <div class="md:col-span-2">
              <label class="block text-gray-700 font-semibold mb-2">Email</label>
              <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="vendor@email.com">
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Contact Person</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Nama PIC">
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Status</label>
              <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                <option>Aktif</option>
                <option>Non Aktif</option>
              </select>
            </div>

          </div>

        </div>

        <div class="border-t border-gray-200 px-6 py-4 flex gap-2 justify-end">
          <button type="button" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200" data-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
            Simpan
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection