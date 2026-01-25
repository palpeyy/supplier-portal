@extends('layout.main')

@section('page_title')
Detail User
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Detail User</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2">
        <div class="flex-1 px-2 w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h3 class="font-semibold text-lg">Detail User: {{ $user->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Nama:</label>
                        <p class="text-gray-600">{{ $user->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Email:</label>
                        <p class="text-gray-600">{{ $user->email }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Role:</label>
                        <p>
                            @if($user->role)
                            <span class="inline-block bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $user->role->name }}</span>
                            @else
                            <span class="inline-block bg-gray-400 text-white px-3 py-1 rounded-full text-sm font-semibold">Tidak ada</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Email Terverifikasi:</label>
                        <p>
                            @if($user->email_verified_at)
                            <span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold">Ya - {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                            @else
                            <span class="inline-block bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">Belum</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Dibuat:</label>
                        <p class="text-gray-600">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Diupdate:</label>
                        <p class="text-gray-600">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="border-t border-gray-200 px-6 py-4 flex gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200" onclick="return confirm('Yakin ingin menghapus user ini?')">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection