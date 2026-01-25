@extends('layout.main')

@section('page_title')
Tambah User
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Tambah User</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2">
        <div class="flex-1 px-2 w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-600">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h3 class="font-semibold text-lg"><i class="fas fa-user-plus mr-2"></i> Tambah User Baru</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('users.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="px-6 py-4">
                        @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <strong class="text-red-700 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> Terjadi Kesalahan!</strong>
                            <ul class="mt-2 mb-0 ml-6 text-red-600 list-disc">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Nama</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 @error('name') border-red-500 @enderror"
                                id="name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                            @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 @error('email') border-red-500 @enderror"
                                id="email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role_id" class="block text-gray-700 font-semibold mb-2">Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 @error('role_id') border-red-500 @enderror" id="role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 @error('password') border-red-500 @enderror"
                                id="password" name="password" placeholder="Masukkan password" required>
                            @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                                id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="border-t border-gray-200 px-6 py-4 flex gap-2">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection