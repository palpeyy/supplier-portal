@extends('layout.main')

@section('page_title')
Detail User
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Detail User</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail User: {{ $user->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Nama:</strong></label>
                        <p>{{ $user->name }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Email:</strong></label>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Role:</strong></label>
                        <p>
                            @if($user->role)
                            <span class="badge badge-primary">{{ $user->role->name }}</span>
                            @else
                            <span class="badge badge-secondary">Tidak ada</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label><strong>Email Terverifikasi:</strong></label>
                        <p>
                            @if($user->email_verified_at)
                            <span class="badge badge-success">Ya - {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                            @else
                            <span class="badge badge-danger">Belum</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label><strong>Dibuat:</strong></label>
                        <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Diupdate:</strong></label>
                        <p>{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection