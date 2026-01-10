@extends('layout.main')

@section('page_title')
Detail Role
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Detail Role</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Role: {{ $role->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Nama Role:</strong></label>
                        <p>{{ $role->name }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Deskripsi:</strong></label>
                        <p>{{ $role->description ?? '-' }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Jumlah User:</strong></label>
                        <p>
                            <span class="badge badge-info">{{ $role->users()->count() }} user</span>
                        </p>
                    </div>

                    @if($role->users()->count() > 0)
                    <div class="form-group">
                        <label><strong>Daftar User:</strong></label>
                        <ul>
                            @foreach($role->users as $user)
                            <li>{{ $user->name }} ({{ $user->email }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-group">
                        <label><strong>Dibuat:</strong></label>
                        <p>{{ $role->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Diupdate:</strong></label>
                        <p>{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @if(!$role->users()->exists())
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus role ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    @else
                    <button class="btn btn-danger" disabled title="Role sedang digunakan oleh users">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    @endif
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection