@extends('layout.main')

@section('page_title')
Tambah Role
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Tambah Role</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2">
        <div class="flex-1 px-2 w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-600">
                <div class="bg-green-600 text-white px-6 py-4">
                    <h3 class="font-semibold text-lg"><i class="fas fa-plus-circle mr-2"></i> Tambah Role Baru</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('roles.store') }}" method="POST" novalidate>
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
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Role</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 @error('name') border-red-500 @enderror"
                                id="name" name="name" placeholder="Masukkan nama role" value="{{ old('name') }}" required>
                            @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 @error('description') border-red-500 @enderror"
                                id="description" name="description" rows="3" placeholder="Masukkan deskripsi role">{{ old('description') }}</textarea>
                            @error('description')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="border-t border-gray-200 px-6 py-4 flex gap-2">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                        <a href="{{ route('roles.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
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