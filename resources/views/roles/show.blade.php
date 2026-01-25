@extends('layout.main')

@section('page_title')
Detail Role
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Detail Role</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="px-4">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-600">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h3 class="font-semibold text-lg"><i class="fas fa-info-circle mr-2"></i> Detail Role: {{ $role->name }}</h3>
                </div>

                <div class="px-6 py-4">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Nama Role:</label>
                        <p class="text-gray-600">{{ $role->name }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi:</label>
                        <p class="text-gray-600">{{ $role->description ?? '-' }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Jumlah User:</label>
                        <p>
                            <span class="inline-block bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $role->users()->count() }} user</span>
                        </p>
                    </div>

                    @if($role->users()->count() > 0)
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Daftar User:</label>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach($role->users as $user)
                            <li>{{ $user->name }} ({{ $user->email }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Dibuat:</label>
                        <p class="text-gray-600">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Diupdate:</label>
                        <p class="text-gray-600">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 flex gap-2 flex-wrap">
                    <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    @if(!$role->users()->exists())
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200" onclick="return confirm('Yakin ingin menghapus role ini?')">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                    @else
                    <button class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed" disabled title="Role sedang digunakan oleh users">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                    @endif
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection