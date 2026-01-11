@extends('layout.main')

@section('page_title')
Manajemen Supplier
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Suppliers</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalSupplier">
                <i class="fas fa-plus"></i> Tambah Supplier
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
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>PIC</th>
                        <th>Telephone</th>
                        <th>Contact Person</th>
                        <th>Dibuat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                        <td>{{ $supplier->nama }}</td>
                        <td>{{ $supplier->alamat ?? '-' }}</td>
                        <td>{{ $supplier->pic ?? '-' }}</td>
                        <td>{{ $supplier->telephone ?? '-' }}</td>
                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                        <td>{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a class="btn btn-warning btn-sm edit-supplier" href="#" data-id="{{ $supplier->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm delete-supplier" data-id="{{ $supplier->id }}" title="Hapus" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data supplier</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>
</div>

<!-- MODAL TAMBAH/EDIT SUPPLIER -->
<div class="modal fade" id="modalSupplier" tabindex="-1" role="dialog" aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSupplierLabel">
                    <i class="fas fa-plus-circle"></i> Tambah Supplier
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formSupplier" action="{{ route('suppliers.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert">
                        <strong>Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0"></ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama">Nama Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama supplier" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat supplier"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pic">PIC (Person In Charge)</label>
                                <input type="text" class="form-control" id="pic" name="pic" placeholder="Masukkan PIC">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">Telephone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Masukkan nomor telephone">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Masukkan contact person">
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
        $('#modalSupplier').on('show.bs.modal', function(e) {
            let btnTambah = $(e.relatedTarget);
            if (!btnTambah.hasClass('edit-supplier')) {
                $('#formSupplier')[0].reset();
                $('#formSupplier').attr('action', '{{ route("suppliers.store") }}');
                $('#formSupplier').find('input[name="_method"]').remove();
                $('#modalSupplierLabel').html('<i class="fas fa-plus-circle"></i> Tambah Supplier');
                $('#errorMessages').addClass('d-none');
            }
        });

        // Edit supplier
        $(document).on('click', '.edit-supplier', function(e) {
            e.preventDefault();
            let supplierId = $(this).data('id');

            $.ajax({
                url: `/suppliers/${supplierId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#formSupplier')[0].reset();
                    $('#nama').val(response.supplier.nama);
                    $('#alamat').val(response.supplier.alamat);
                    $('#pic').val(response.supplier.pic);
                    $('#telephone').val(response.supplier.telephone);
                    $('#contact_person').val(response.supplier.contact_person);

                    $('#formSupplier').attr('action', `/suppliers/${supplierId}`);
                    if ($('#formSupplier').find('input[name="_method"]').length === 0) {
                        $('#formSupplier').prepend('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('#modalSupplierLabel').html('<i class="fas fa-edit"></i> Edit Supplier');
                    $('#errorMessages').addClass('d-none');
                    $('#modalSupplier').modal('show');
                },
                error: function() {
                    alert('Gagal memuat data supplier');
                }
            });
        });

        // Submit form
        $('#formSupplier').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let action = $(this).attr('action');

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#modalSupplier').modal('hide');
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

        // Delete supplier
        $(document).on('click', '.delete-supplier', function(e) {
            e.preventDefault();
            let supplierId = $(this).data('id');

            if (confirm('Yakin ingin menghapus supplier ini?')) {
                $.ajax({
                    url: `/suppliers/${supplierId}`,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal menghapus supplier');
                    }
                });
            }
        });
    });
</script>
@endpush

