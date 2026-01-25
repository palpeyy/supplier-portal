@extends('layout.main')

@section('page_title')
Manajemen Roles
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Roles</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2 mb-4">
        <div class="flex-1 px-2 w-full sm:w-1/2"></div>
        <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
            <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalRole">
                <i class="fas fa-plus mr-2"></i> Tambah Role
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
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Role</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Jumlah User</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Dibuat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 font-medium">{{ $role->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $role->description ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="inline-block bg-cyan-600 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $role->users()->count() }} user</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $role->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm flex gap-1">
                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200 edit-role" href="#" data-id="{{ $role->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$role->users()->exists())
                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition duration-200 delete-role" data-id="{{ $role->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                            @else
                            <button class="inline-flex items-center px-2 py-1 bg-gray-400 text-white text-xs font-semibold rounded cursor-not-allowed" disabled title="Role masih digunakan">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-600">Tidak ada data roles</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $roles->links() }}
    </div>
</div>

<!-- MODAL TAMBAH/EDIT ROLE -->
<div class="modal fade" id="modalRole" tabindex="-1" role="dialog" aria-labelledby="modalRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-white rounded-lg shadow-lg">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg border-b border-green-700">
                <h5 class="font-semibold text-lg" id="modalRoleLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Role
                </h5>
                <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formRole" action="{{ route('roles.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="px-6 py-4">
                    <div id="errorMessages" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden">
                        <strong class="text-red-700">Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0 ml-6 text-red-600 list-disc"></ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Role</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600" id="name" name="name" placeholder="Masukkan nama role" required>
                        </div>

                        <div class="col-span-full">
                            <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600" id="description" name="description" rows="3" placeholder="Masukkan deskripsi role"></textarea>
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
        // Reset form saat modal dibuka untuk tambah
        $('#modalRole').on('show.bs.modal', function(e) {
            let btnTambah = $(e.relatedTarget);
            if (!btnTambah.hasClass('edit-role')) {
                $('#formRole')[0].reset();
                $('#formRole').attr('action', '{{ route("roles.store") }}');
                $('#formRole').find('input[name="_method"]').remove();
                $('#modalRoleLabel').html('<i class="fas fa-plus-circle"></i> Tambah Role');
                $('#errorMessages').addClass('hidden');
            }
        });

        // Edit role
        $(document).on('click', '.edit-role', function(e) {
            e.preventDefault();
            let roleId = $(this).data('id');

            $.ajax({
                url: `/roles/${roleId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#formRole')[0].reset();
                    $('#name').val(response.role.name);
                    $('#description').val(response.role.description);

                    $('#formRole').attr('action', `/roles/${roleId}`);
                    if ($('#formRole').find('input[name="_method"]').length === 0) {
                        $('#formRole').prepend('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('#modalRoleLabel').html('<i class="fas fa-edit"></i> Edit Role');
                    $('#errorMessages').addClass('hidden');
                    $('#modalRole').modal('show');
                },
                error: function() {
                    alert('Gagal memuat data role');
                }
            });
        });

        // Submit form
        $('#formRole').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let action = $(this).attr('action');

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#modalRole').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = $('#errorList');
                        errorList.html('');

                        $.each(errors, function(key, value) {
                            errorList.append('<li>' + value[0] + '</li>');
                        });

                        $('#errorMessages').removeClass('hidden');
                    }
                }
            });
        });

        // Delete role
        $(document).on('click', '.delete-role', function(e) {
            e.preventDefault();
            let roleId = $(this).data('id');

            if (confirm('Yakin ingin menghapus role ini?')) {
                $.ajax({
                    url: `/roles/${roleId}`,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal menghapus role');
                    }
                });
            }
        });
    });
</script>
@endpush