@extends('layout.main')

@section('page_title')
Manajemen Supplier
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Suppliers</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2 mb-4">
        <div class="flex-1 px-2 w-full sm:w-1/2"></div>
        <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalSupplier">
                <i class="fas fa-plus mr-2"></i> Tambah Supplier
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            @if ($message = Session::get('success'))
            <div class="m-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <span class="text-green-700 font-semibold">{{ $message }}</span>
                <button type="button" class="float-right text-green-700 font-bold text-xl leading-none hover:text-green-900" onclick="this.parentElement.style.display='none';">×</button>
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="m-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <span class="text-red-700 font-semibold">{{ $message }}</span>
                <button type="button" class="float-right text-red-700 font-bold text-xl leading-none hover:text-red-900" onclick="this.parentElement.style.display='none';">×</button>
            </div>
            @endif

            <table class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Supplier</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Alamat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">PIC</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Telephone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Contact Person</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Dibuat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 font-medium">{{ $supplier->nama }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $supplier->alamat ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $supplier->pic ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $supplier->telephone ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $supplier->contact_person ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm flex gap-1">
                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200 edit-supplier" href="#" data-id="{{ $supplier->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition duration-200 delete-supplier" data-id="{{ $supplier->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-600">Tidak ada data supplier</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $suppliers->links() }}
    </div>
</div>

<!-- MODAL SUCCESS -->
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="modalSuccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content bg-white rounded-lg shadow-lg">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg border-b border-green-700 flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <h5 class="font-semibold text-lg" id="modalSuccessLabel">Berhasil</h5>
                <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="px-6 py-6 text-center">
                <p id="modalSuccessMessage" class="text-gray-700 font-semibold mb-6"></p>
                <button type="button" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH/EDIT SUPPLIER -->
<div class="modal fade" id="modalSupplier" tabindex="-1" role="dialog" aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-white rounded-lg shadow-lg">
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg border-b border-blue-700">
                <h5 class="font-semibold text-lg" id="modalSupplierLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Supplier
                </h5>
                <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formSupplier" action="{{ route('suppliers.store') }}" method="post">
                @csrf
                <div class="px-6 py-4">
                    <div id="errorMessages" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden">
                        <strong class="text-red-700">Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0 ml-6 text-red-600 list-disc"></ul>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="nama" class="block text-gray-700 font-semibold mb-2">Nama Supplier <span class="text-red-600">*</span></label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="nama" name="nama" placeholder="Masukkan nama supplier" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-gray-700 font-semibold mb-2">Email <span class="text-red-600">*</span></label>
                                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="email" name="email" placeholder="Masukkan email supplier" required>
                                <small class="form-text text-muted">Email untuk login supplier</small>
                            </div>

                            <div>
                                <label for="password" class="block text-gray-700 font-semibold mb-2">Password <span class="text-red-600">*</span></label>
                                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="password" name="password" placeholder="Masukkan password (min 6 karakter)" required>
                            </div>
                        </div>

                        <div>
                            <label for="alamat" class="block text-gray-700 font-semibold mb-2">Alamat</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat supplier"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="pic" class="block text-gray-700 font-semibold mb-2">PIC (Person In Charge)</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="pic" name="pic" placeholder="Masukkan PIC">
                            </div>

                            <div>
                                <label for="telephone" class="block text-gray-700 font-semibold mb-2">Telephone</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="telephone" name="telephone" placeholder="Masukkan nomor telephone">
                            </div>
                        </div>

                        <div>
                            <label for="contact_person" class="block text-gray-700 font-semibold mb-2">Contact Person</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="contact_person" name="contact_person" placeholder="Masukkan contact person">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 flex gap-2 justify-end">
                    <button type="button" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200" data-dismiss="modal">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show success modal jika ada flash session
        @if(Session::has('success'))
        $('#modalSuccessMessage').text('{{ Session::get('
            success ') }}');
        $('#modalSuccess').modal('show');
        @endif

        // TAMBAH SUPPLIER - Reset form untuk create
        $(document).on('click', '[data-toggle="modal"][data-target="#modalSupplier"]', function(e) {
            e.preventDefault();

            // Reset all form fields
            $('#formSupplier')[0].reset();
            $('#errorMessages').addClass('hidden');

            // Clear hidden method field
            $('#formSupplier').find('input[name="_method"]').remove();

            // Set form untuk CREATE
            $('#formSupplier').attr('action', '{{ route("suppliers.store") }}');

            // Set email untuk CREATE (required, tidak readonly)
            $('#email').val('').prop('readonly', false).prop('required', true);

            // Set password untuk CREATE (required)
            $('#password').val('').prop('required', true).prop('placeholder', 'Masukkan password (min 6 karakter)');

            // Update modal title
            $('#modalSupplierLabel').html('<i class="fas fa-plus-circle mr-2"></i> Tambah Supplier');
        });

        // EDIT SUPPLIER - Load data dan set form untuk update
        $(document).on('click', '.edit-supplier', function(e) {
            e.preventDefault();
            let supplierId = $(this).data('id');

            $.ajax({
                url: `/suppliers/${supplierId}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Reset form
                    $('#formSupplier')[0].reset();
                    $('#errorMessages').addClass('hidden');

                    // Fill form dengan data supplier
                    $('#nama').val(response.supplier.nama);
                    $('#alamat').val(response.supplier.alamat || '');
                    $('#pic').val(response.supplier.pic || '');
                    $('#telephone').val(response.supplier.telephone || '');
                    $('#contact_person').val(response.supplier.contact_person || '');

                    // Get email dari user relationship
                    let email = '';
                    if (response.supplier.users && response.supplier.users.length > 0) {
                        email = response.supplier.users[0].email;
                    }

                    // Set email untuk EDIT (readonly, tidak required)
                    $('#email').val(email).prop('readonly', true).prop('required', false);

                    // Set password untuk EDIT (optional)
                    $('#password').val('').prop('required', false).prop('placeholder', 'Biarkan kosong jika tidak ingin mengubah password');

                    // Set form action untuk UPDATE
                    $('#formSupplier').attr('action', `/suppliers/${supplierId}`);

                    // Clear dan tambah PUT method
                    $('#formSupplier').find('input[name="_method"]').remove();
                    $('#formSupplier').prepend('<input type="hidden" name="_method" value="PUT">');

                    // Update modal title
                    $('#modalSupplierLabel').html('<i class="fas fa-edit mr-2"></i> Edit Supplier');

                    // Show modal
                    $('#modalSupplier').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading supplier:', xhr);
                    alert('Gagal memuat data supplier');
                }
            });
        });

        // SUBMIT FORM - Create atau Update
        $('#formSupplier').on('submit', function(e) {
            e.preventDefault();

            let action = $(this).attr('action');
            let formData = new FormData(this);

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalSupplier').modal('hide');
                    $('#modalSuccessMessage').text(response.success);
                    $('#modalSuccess').modal('show');

                    // Reload page after modal success closed
                    $('#modalSuccess').one('hidden.bs.modal', function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.error('Form submission error:', xhr);
                    if (xhr.status === 422) {
                        // Validation errors
                        let errors = xhr.responseJSON.errors;
                        let errorList = $('#errorList');
                        errorList.html('');

                        $.each(errors, function(key, value) {
                            errorList.append('<li>' + value[0] + '</li>');
                        });

                        $('#errorMessages').removeClass('hidden');
                    } else {
                        alert('Terjadi kesalahan: ' + xhr.statusText);
                    }
                }
            });
        });

        // DELETE SUPPLIER
        $(document).on('click', '.delete-supplier', function(e) {
            e.preventDefault();
            let supplierId = $(this).data('id');

            if (confirm('Yakin ingin menghapus supplier ini?')) {
                $.ajax({
                    url: `/suppliers/${supplierId}`,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#modalSuccessMessage').text(response.success);
                        $('#modalSuccess').modal('show');

                        $('#modalSuccess').one('hidden.bs.modal', function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        alert('Gagal menghapus supplier');
                    }
                });
            }
        });
    });
</script>
@endpush