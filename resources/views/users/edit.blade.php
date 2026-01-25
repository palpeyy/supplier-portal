@extends('layout.main')

@section('page_title')
Edit User
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Edit User</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2">
        <div class="flex-1 px-2 w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
                <div class="bg-yellow-500 text-white px-6 py-4">
                    <h3 class="font-semibold text-lg"><i class="fas fa-edit mr-2"></i> Edit User: {{ $user->name }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('users.update', $user->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
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
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-500 @error('name') border-red-500 @enderror"
                                id="name" name="name" placeholder="Masukkan nama" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-500 @error('email') border-red-500 @enderror"
                                id="email" name="email" placeholder="Masukkan email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role_id" class="block text-gray-700 font-semibold mb-2">Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-500 @error('role_id') border-red-500 @enderror" id="role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 font-semibold mb-2">Password <small class="text-gray-500">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-500 @error('password') border-red-500 @enderror"
                                id="password" name="password" placeholder="Masukkan password baru">
                            @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-500"
                                id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password">
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="border-t border-gray-200 px-6 py-4 flex gap-2">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Update
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