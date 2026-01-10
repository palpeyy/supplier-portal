@extends('layout.main')

@section('page_title')
Manajemen Roles
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Roles</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRole">
                <i class="fas fa-plus"></i> Tambah Role
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Role</th>
                        <th>Deskripsi</th>
                        <th>Jumlah User</th>
                        <th>Dibuat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->description ?? '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $role->users()->count() }} user</span>
                        </td>
                        <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a class="btn btn-warning btn-sm edit-role" href="#" data-id="{{ $role->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$role->users()->exists())
                            <button class="btn btn-danger btn-sm delete-role" data-id="{{ $role->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                            @else
                            <button class="btn btn-danger btn-sm" disabled title="Role masih digunakan">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data roles</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        {{ $roles->links() }}
    </div>
</div>

<!-- MODAL TAMBAH/EDIT ROLE -->
<div class="modal fade" id="modalRole" tabindex="-1" role="dialog" aria-labelledby="modalRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRoleLabel">
                    <i class="fas fa-plus-circle"></i> Tambah Role
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formRole" action="{{ route('roles.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert">
                        <strong>Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0"></ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Role</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama role" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Masukkan deskripsi role"></textarea>
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
                $('#errorMessages').addClass('d-none');
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
                    $('#errorMessages').addClass('d-none');
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

                        $('#errorMessages').removeClass('d-none');
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