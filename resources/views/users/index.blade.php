@extends('layout.main')

@section('page_title')
Manajemen Users
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Users</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2 mb-4">
        <div class="flex-1 px-2 w-full sm:w-1/2"></div>
        <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalUser">
                <i class="fas fa-plus mr-2"></i> Tambah User
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
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Username</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Dibuat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $user->username ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-sm">
                            @if($user->role)
                            <span class="inline-block bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $user->role->name }}</span>
                            @else
                            <span class="inline-block bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm">
                            @if($user->supplier)
                            <span class="inline-block bg-cyan-600 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $user->supplier->nama }}</span>
                            @else
                            <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm flex gap-1">
                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200 edit-user" href="#" data-id="{{ $user->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition duration-200 delete-user" data-id="{{ $user->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-600">Tidak ada data users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>

<div id="flashData" data-success="{{ session('success') }}"></div>

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

<!-- MODAL TAMBAH/EDIT USER -->
<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-white rounded-lg shadow-lg">
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg border-b border-blue-700">
                <h5 class="font-semibold text-lg" id="modalUserLabel">
                    <i class="fas fa-user-plus mr-2"></i> Tambah User
                </h5>
                <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUser" action="{{ route('users.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="px-6 py-4">
                    <div id="errorMessages" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden">
                        <strong class="text-red-700">Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0 ml-6 text-red-600 list-disc"></ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Nama</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="name" name="name" placeholder="Masukkan nama" required>
                        </div>

                        <div>
                            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="email" name="email" placeholder="Masukkan email" required>
                        </div>

                        <div>
                            <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="username" name="username" placeholder="Masukkan username" required>
                            <small class="text-gray-500">Gunakan huruf, angka, dash, atau underscore</small>
                        </div>

                        <div>
                            <label for="role_id" class="block text-gray-700 font-semibold mb-2">Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="supplier_group">
                            <label for="supplier_id" class="block text-gray-700 font-semibold mb-2">Supplier <span class="text-red-600" id="supplier_required_label" style="display: none;">*</span></label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="supplier_id" name="supplier_id">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-gray-500" id="supplier_help">Opsional, hanya diperlukan jika role adalah Supplier</small>
                        </div>

                        <div>
                            <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="password" name="password" placeholder="Masukkan password" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required>
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
        const flashSuccessMessage = $('#flashData').data('success');
        if (flashSuccessMessage) {
            $('#modalSuccessMessage').text(flashSuccessMessage);
            $('#modalSuccess').modal('show');
        }

        // Toggle supplier field required based on role
        function toggleSupplierField() {
            let roleId = $('#role_id').val();
            let roleText = $('#role_id option:selected').text().toLowerCase();

            if (roleText.includes('supplier')) {
                $('#supplier_id').prop('required', true);
                $('#supplier_required_label').show();
                $('#supplier_help').text('Required untuk role Supplier');
                $('#supplier_help').removeClass('text-gray-500').addClass('text-red-600');
            } else {
                $('#supplier_id').prop('required', false);
                $('#supplier_required_label').hide();
                $('#supplier_help').text('Opsional, hanya diperlukan jika role adalah Supplier');
                $('#supplier_help').removeClass('text-red-600').addClass('text-gray-500');
            }
        }

        $('#role_id').on('change', function() {
            toggleSupplierField();
        });

        // Store user data for edit
        let currentUserData = null;

        // TAMBAH USER - Reset form untuk create
        $(document).on('click', '[data-toggle="modal"][data-target="#modalUser"]', function(e) {
            e.preventDefault();

            // Reset all form fields
            $('#formUser')[0].reset();
            $('#errorMessages').addClass('hidden');

            // Clear hidden method field
            $('#formUser').find('input[name="_method"]').remove();

            // Set form untuk CREATE
            $('#formUser').attr('action', '{{ route("users.store") }}');

            // Set password untuk CREATE (required)
            $('#password').prop('required', true);
            $('#password_confirmation').prop('required', true);

            // Update modal title
            $('#modalUserLabel').html('<i class="fas fa-user-plus mr-2"></i> Tambah User');

            // Reset supplier field
            $('#supplier_id').prop('required', false);
            $('#supplier_required_label').hide();
            $('#supplier_help').text('Opsional, hanya diperlukan jika role adalah Supplier');
            $('#supplier_help').removeClass('text-red-600').addClass('text-gray-500');

            currentUserData = null;
        });

        // EDIT USER - Load data dan set form untuk update
        $(document).on('click', '.edit-user', function(e) {
            e.preventDefault();
            let userId = $(this).data('id');

            $.ajax({
                url: `/users/${userId}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Reset form
                    $('#formUser')[0].reset();
                    $('#errorMessages').addClass('hidden');

                    // Fill form dengan data user
                    $('#name').val(response.user.name || '');
                    $('#username').val(response.user.username || '');
                    $('#email').val(response.user.email || '');
                    $('#role_id').val(response.user.role_id || '').trigger('change');

                    // Set supplier_id dengan delay
                    setTimeout(function() {
                        $('#supplier_id').val(response.user.supplier_id || '');
                        toggleSupplierField();
                    }, 100);

                    // Password optional untuk edit
                    $('#password').prop('required', false);
                    $('#password_confirmation').prop('required', false);

                    // Set form action untuk UPDATE
                    $('#formUser').attr('action', `/users/${userId}`);

                    // Clear dan tambah PUT method
                    $('#formUser').find('input[name="_method"]').remove();
                    $('#formUser').prepend('<input type="hidden" name="_method" value="PUT">');

                    // Update modal title
                    $('#modalUserLabel').html('<i class="fas fa-edit mr-2"></i> Edit User');

                    // Show modal
                    $('#modalUser').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading user:', xhr);
                    alert('Gagal memuat data user');
                }
            });
        });

        // SUBMIT FORM - Create atau Update
        $('#formUser').on('submit', function(e) {
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
                    $('#modalUser').modal('hide');
                    $('#modalSuccessMessage').text(response.success || 'User berhasil disimpan');
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

        // DELETE USER
        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            let userId = $(this).data('id');

            if (confirm('Yakin ingin menghapus user ini?')) {
                $.ajax({
                    url: `/users/${userId}`,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#modalSuccessMessage').text(response.success || 'User berhasil dihapus');
                        $('#modalSuccess').modal('show');

                        $('#modalSuccess').one('hidden.bs.modal', function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        alert('Gagal menghapus user');
                    }
                });
            }
        });
    });
</script>
@endpush