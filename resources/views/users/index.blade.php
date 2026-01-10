@extends('layout.main')

@section('page_title')
Manajemen Users
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Users</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalUser">
                <i class="fas fa-plus"></i> Tambah User
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role)
                            <span class="badge badge-primary">{{ $user->role->name }}</span>
                            @else
                            <span class="badge badge-secondary">Tidak ada</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a class="btn btn-warning btn-sm edit-user" href="#" data-id="{{ $user->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        {{ $users->links() }}
    </div>
</div>

<!-- MODAL TAMBAH/EDIT USER -->
<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserLabel">
                    <i class="fas fa-user-plus"></i> Tambah User
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUser" action="{{ route('users.store') }}" method="post">
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
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required>
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
        $('#modalUser').on('show.bs.modal', function(e) {
            let btnTambah = $(e.relatedTarget);
            if (!btnTambah.hasClass('edit-user')) {
                $('#formUser')[0].reset();
                $('#formUser').attr('action', '{{ route("users.store") }}');
                $('#formUser').find('input[name="_method"]').remove();
                $('#modalUserLabel').html('<i class="fas fa-user-plus"></i> Tambah User');
                $('#password').prop('required', true);
                $('#password_confirmation').prop('required', true);
                $('#errorMessages').addClass('d-none');
            }
        });

        // Edit user
        $(document).on('click', '.edit-user', function(e) {
            e.preventDefault();
            let userId = $(this).data('id');

            $.ajax({
                url: `/users/${userId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#formUser')[0].reset();
                    $('#name').val(response.user.name);
                    $('#email').val(response.user.email);
                    $('#role_id').val(response.user.role_id);
                    $('#password').prop('required', false);
                    $('#password_confirmation').prop('required', false);

                    $('#formUser').attr('action', `/users/${userId}`);
                    if ($('#formUser').find('input[name="_method"]').length === 0) {
                        $('#formUser').prepend('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('#modalUserLabel').html('<i class="fas fa-edit"></i> Edit User');
                    $('#errorMessages').addClass('d-none');
                    $('#modalUser').modal('show');
                },
                error: function() {
                    alert('Gagal memuat data user');
                }
            });
        });

        // Submit form
        $('#formUser').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let action = $(this).attr('action');

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#modalUser').modal('hide');
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

        // Delete user
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
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal menghapus user');
                    }
                });
            }
        });
    });
</script>
@endpush