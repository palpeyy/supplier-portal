@extends('layout.main')

@section('page_title')
Penagihan Invoice
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Penagihan Invoice</li>
@endsection

@section('isi')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penagihan Invoice</h3>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi Kesalahan!</strong>
                <ul class="mt-2 mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($userRole === 'Supplier' && $purchaseOrdersWithoutInvoice->count() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Terdapat {{ $purchaseOrdersWithoutInvoice->count() }} Purchase Order yang siap untuk di-invoice.
            </div>
            @endif

            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>PO Number</th>
                        <th>Tanggal PO</th>
                        <th>Supplier</th>
                        <th>Status Invoice</th>
                        <th>Catatan Revisi</th>
                        <th>Tanggal Upload</th>
                        <th width="250">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</td>
                        <td><strong>{{ $invoice->purchaseOrder->po_number }}</strong></td>
                        <td>{{ $invoice->purchaseOrder->date ? $invoice->purchaseOrder->date->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($invoice->purchaseOrder->supplier)
                            <span class="badge badge-info">{{ $invoice->purchaseOrder->supplier->nama }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($invoice->status == 'revised')
                                <span class="badge badge-danger">Revised</span>
                            @elseif($invoice->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($invoice->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->catatan_revisi)
                            <span class="text-danger" title="{{ $invoice->catatan_revisi }}">
                                <i class="fas fa-exclamation-circle"></i> Ada catatan
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($userRole === 'Supplier' && $invoice->status == 'revised')
                            <button class="btn btn-warning btn-sm revise-invoice-supplier" data-id="{{ $invoice->id }}" title="Upload Revisi">
                                <i class="fas fa-upload"></i> Revisi
                            </button>
                            @endif

                            @if($userRole === 'Admin' && in_array($invoice->status, ['pending', 'revised']))
                            <button class="btn btn-success btn-sm approve-invoice" data-id="{{ $invoice->id }}" title="Aksi">
                                <i class="fas fa-tasks"></i> Aksi
                            </button>
                            @endif

                            @if($invoice->invoice_file && $invoice->surat_jalan_file && $invoice->faktur_pajak_file)
                            <button class="btn btn-primary btn-sm detail-invoice" data-id="{{ $invoice->id }}" title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data Invoice</td>
                    </tr>
                    @endforelse
                    
                    @if($userRole === 'Supplier')
                        @foreach($purchaseOrdersWithoutInvoice as $po)
                        <tr>
                            <td>-</td>
                            <td><strong>{{ $po->po_number }}</strong></td>
                            <td>{{ $po->date ? $po->date->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($po->supplier)
                                <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">Belum Upload</span>
                            </td>
                            <td><span class="text-muted">-</span></td>
                            <td><span class="text-muted">-</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm upload-invoice" data-po-id="{{ $po->id }}" title="Upload Invoice">
                                    <i class="fas fa-upload"></i> Upload Invoice
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

<!-- MODAL UPLOAD INVOICE (Supplier) -->
<div class="modal fade" id="modalUploadInvoice" tabindex="-1" role="dialog" aria-labelledby="modalUploadInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUploadInvoiceLabel">
                    <i class="fas fa-file-invoice"></i> Upload Invoice
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUploadInvoice" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert">
                        <strong>Terjadi Kesalahan!</strong>
                        <ul id="errorList" class="mt-2 mb-0"></ul>
                    </div>

                    <div class="form-group">
                        <label for="invoice_file">File Invoice <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="surat_jalan_file">File Surat Jalan/ASN <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="surat_jalan_file" name="surat_jalan_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="faktur_pajak_file">File Faktur Pajak <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="faktur_pajak_file" name="faktur_pajak_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REVISI INVOICE (Supplier - untuk revisi) -->
<div class="modal fade" id="modalReviseInvoice" tabindex="-1" role="dialog" aria-labelledby="modalReviseInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="modalReviseInvoiceLabel">
                    <i class="fas fa-exclamation-triangle"></i> Revisi Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formReviseInvoice" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div id="reviseErrorMessages" class="alert alert-danger d-none" role="alert">
                        <strong>Terjadi Kesalahan!</strong>
                        <ul id="reviseErrorList" class="mt-2 mb-0"></ul>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Catatan Revisi:</strong>
                        <p id="catatanRevisiText" class="mb-0"></p>
                    </div>

                    <div class="form-group">
                        <label for="revise_invoice_file">File Invoice (Baru) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="revise_invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="revise_surat_jalan_file">File Surat Jalan/ASN (Baru) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="revise_surat_jalan_file" name="surat_jalan_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="revise_faktur_pajak_file">File Faktur Pajak (Baru) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="revise_faktur_pajak_file" name="faktur_pajak_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB)</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-upload"></i> Upload Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL APPROVE/REJECT/REVISE INVOICE (Admin) -->
<div class="modal fade" id="modalApproveInvoice" tabindex="-1" role="dialog" aria-labelledby="modalApproveInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="modalApproveInvoiceLabel">
                    <i class="fas fa-check-circle"></i> Review Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="approveLoading" class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>

                <div id="approveContent" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Purchase Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="40%">PO Number</th>
                                            <td>:</td>
                                            <td><strong id="approve_po_number">-</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Supplier</th>
                                            <td>:</td>
                                            <td id="approve_supplier">-</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal PO</th>
                                            <td>:</td>
                                            <td id="approve_po_date">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> File Invoice</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Invoice</h6>
                                        <a id="approve_download_invoice" href="#" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Surat Jalan/ASN</h6>
                                        <a id="approve_download_surat_jalan" href="#" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Faktur Pajak</h6>
                                        <a id="approve_download_faktur_pajak" href="#" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnApproveInvoice">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" id="btnRejectInvoice">
                    <i class="fas fa-times"></i> Reject
                </button>
                <button type="button" class="btn btn-warning" id="btnReviseInvoice">
                    <i class="fas fa-edit"></i> Revise
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REJECT/REVISE KETERANGAN -->
<div class="modal fade" id="modalRejectReviseKeterangan" tabindex="-1" role="dialog" aria-labelledby="modalRejectReviseKeteranganLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modalRejectReviseHeader">
                <h5 class="modal-title text-white" id="modalRejectReviseKeteranganLabel">
                    <i class="fas fa-times-circle"></i> Reject Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formRejectReviseInvoice" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label id="labelKeterangan">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4" required placeholder="Masukkan keterangan reject atau catatan revisi..."></textarea>
                        <small class="form-text text-muted" id="helpKeterangan">Masukkan keterangan reject</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="btnSubmitRejectRevise">
                        <i class="fas fa-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAIL INVOICE -->
<div class="modal fade" id="modalDetailInvoice" tabindex="-1" role="dialog" aria-labelledby="modalDetailInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalDetailInvoiceLabel">
                    <i class="fas fa-eye"></i> Detail Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="invoiceDetailLoading" class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>

                <div id="invoiceDetailContent" style="display: none;">
                    <!-- Purchase Order Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Purchase Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="40%">PO Number</th>
                                            <td>:</td>
                                            <td><strong id="invoice_detail_po_number">-</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal PO</th>
                                            <td>:</td>
                                            <td id="invoice_detail_date">-</td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Date</th>
                                            <td>:</td>
                                            <td id="invoice_detail_delivery_date">-</td>
                                        </tr>
                                        <tr>
                                            <th>Currency</th>
                                            <td>:</td>
                                            <td><span class="badge badge-info" id="invoice_detail_currency">-</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Item</th>
                                            <td>:</td>
                                            <td><span class="badge badge-primary" id="invoice_detail_item_count">-</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="40%">Supplier</th>
                                            <td>:</td>
                                            <td id="invoice_detail_supplier">-</td>
                                        </tr>
                                        <tr>
                                            <th>Status Invoice</th>
                                            <td>:</td>
                                            <td id="invoice_detail_status">-</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Upload</th>
                                            <td>:</td>
                                            <td id="invoice_detail_upload_date">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Items -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Item Number</th>
                                            <th width="12%">Material Code</th>
                                            <th width="12%">Vendor Material</th>
                                            <th>Description</th>
                                            <th width="8%" class="text-center">Qty</th>
                                            <th width="12%" class="text-right">Price Per Unit</th>
                                            <th width="12%" class="text-right">Net Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice_detail_items_body">
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data items</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Invoice -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Dokumen</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <i class="fas fa-file-invoice fa-3x text-primary mb-3"></i>
                                        <h6>Invoice</h6>
                                        <button class="btn btn-primary btn-sm mt-2" id="btn_open_invoice">
                                            <i class="fas fa-external-link-alt"></i> Buka Dokumen
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <i class="fas fa-truck fa-3x text-success mb-3"></i>
                                        <h6>Surat Jalan/ASN</h6>
                                        <button class="btn btn-success btn-sm mt-2" id="btn_open_surat_jalan">
                                            <i class="fas fa-external-link-alt"></i> Buka Dokumen
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <i class="fas fa-receipt fa-3x text-warning mb-3"></i>
                                        <h6>Faktur Pajak</h6>
                                        <button class="btn btn-warning btn-sm mt-2" id="btn_open_faktur_pajak">
                                            <i class="fas fa-external-link-alt"></i> Buka Dokumen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentInvoiceId = null;
    let currentAction = null; // 'reject' or 'revise'
    let currentPoId = null;

    // Upload Invoice (Supplier)
    $(document).on('click', '.upload-invoice', function(e) {
        e.preventDefault();
        currentPoId = $(this).data('po-id');
        
        $('#formUploadInvoice').attr('action', `/invoices/${currentPoId}/store`);
        $('#formUploadInvoice')[0].reset();
        $('#errorMessages').addClass('d-none');
        $('#modalUploadInvoice').modal('show');
    });

    // Submit Upload Invoice
    $('#formUploadInvoice').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let action = $(this).attr('action');

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalUploadInvoice').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let errorList = $('#errorList');
                errorList.html('');
                
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || xhr.responseJSON.error;
                    if (typeof errors === 'object') {
                        $.each(errors, function(key, value) {
                            if (Array.isArray(value)) {
                                $.each(value, function(index, message) {
                                    errorList.append('<li>' + message + '</li>');
                                });
                            } else {
                                errorList.append('<li>' + value + '</li>');
                            }
                        });
                    } else {
                        errorList.append('<li>' + (errors || 'Terjadi kesalahan') + '</li>');
                    }
                } else {
                    errorList.append('<li>' + (xhr.responseJSON?.error || 'Gagal upload invoice') + '</li>');
                }
                
                $('#errorMessages').removeClass('d-none');
            }
        });
    });

    // Approve/Reject/Revise Invoice (Admin)
    $(document).on('click', '.approve-invoice, .reject-invoice, .revise-invoice', function(e) {
        e.preventDefault();
        currentInvoiceId = $(this).data('id');
        
        $('#approveContent').hide();
        $('#approveLoading').show();
        $('#modalApproveInvoice').modal('show');

        $.ajax({
            url: `/invoices/${currentInvoiceId}`,
            type: 'GET',
            success: function(response) {
                let invoice = response.invoice;
                let po = invoice.purchase_order;
                
                $('#approve_po_number').text(po.po_number);
                $('#approve_supplier').html(po.supplier ? '<span class="badge badge-info">' + po.supplier.nama + '</span>' : '-');
                $('#approve_po_date').text(po.date ? new Date(po.date).toLocaleDateString('id-ID') : '-');
                
                $('#approve_download_invoice').attr('href', `/invoices/${invoice.id}/download-invoice`);
                $('#approve_download_surat_jalan').attr('href', `/invoices/${invoice.id}/download-surat-jalan`);
                $('#approve_download_faktur_pajak').attr('href', `/invoices/${invoice.id}/download-faktur-pajak`);
                
                $('#approveLoading').hide();
                $('#approveContent').show();
            },
            error: function(xhr) {
                alert('Gagal memuat data invoice');
                $('#modalApproveInvoice').modal('hide');
            }
        });
    });

    // Approve Invoice
    $('#btnApproveInvoice').on('click', function() {
        if (!currentInvoiceId) return;
        
        if (!confirm('Apakah Anda yakin ingin approve invoice ini?')) return;
        
        $.ajax({
            url: `/invoices/${currentInvoiceId}/approve`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#modalApproveInvoice').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.error || 'Gagal approve invoice');
            }
        });
    });

    // Reject Invoice
    $('#btnRejectInvoice').on('click', function() {
        if (!currentInvoiceId) return;
        
        currentAction = 'reject';
        $('#modalRejectReviseKeteranganLabel').html('<i class="fas fa-times-circle"></i> Reject Invoice');
        $('#modalRejectReviseHeader').removeClass('bg-warning').addClass('bg-danger');
        $('#labelKeterangan').html('Keterangan Reject <span class="text-danger">*</span>');
        $('#helpKeterangan').text('Masukkan keterangan reject');
        $('#keterangan').val('');
        $('#btnSubmitRejectRevise').removeClass('btn-warning').addClass('btn-danger').html('<i class="fas fa-times"></i> Reject');
        $('#formRejectReviseInvoice').attr('action', `/invoices/${currentInvoiceId}/reject`);
        $('#modalRejectReviseKeterangan').modal('show');
    });

    // Revise Invoice
    $('#btnReviseInvoice').on('click', function() {
        if (!currentInvoiceId) return;
        
        currentAction = 'revise';
        $('#modalRejectReviseKeteranganLabel').html('<i class="fas fa-edit"></i> Revise Invoice');
        $('#modalRejectReviseHeader').removeClass('bg-danger').addClass('bg-warning');
        $('#labelKeterangan').html('Catatan Revisi <span class="text-danger">*</span>');
        $('#helpKeterangan').text('Masukkan catatan revisi (penjelasan apa yang salah dan apa yang harus direvisi)');
        $('#keterangan').val('');
        $('#btnSubmitRejectRevise').removeClass('btn-danger').addClass('btn-warning').html('<i class="fas fa-edit"></i> Revise');
        $('#formRejectReviseInvoice').attr('action', `/invoices/${currentInvoiceId}/revise`);
        $('#modalRejectReviseKeterangan').modal('show');
    });

    // Submit Reject/Revise
    $('#formRejectReviseInvoice').on('submit', function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        let action = $(this).attr('action');

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalRejectReviseKeterangan').modal('hide');
                $('#modalApproveInvoice').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || {};
                    let errorMsg = '';
                    $.each(errors, function(key, value) {
                        if (Array.isArray(value)) {
                            errorMsg += value.join(', ') + '\n';
                        } else {
                            errorMsg += value + '\n';
                        }
                    });
                    alert(errorMsg || 'Validasi gagal');
                } else {
                    alert(xhr.responseJSON?.error || 'Gagal memproses');
                }
            }
        });
    });

    // Revise Invoice (Supplier) - untuk invoice yang statusnya revised
    $(document).on('click', '.revise-invoice-supplier', function(e) {
        e.preventDefault();
        let invoiceId = $(this).data('id');
        
        // Load invoice data untuk revisi
        $.ajax({
            url: `/invoices/${invoiceId}`,
            type: 'GET',
            success: function(response) {
                let invoice = response.invoice;
                $('#catatanRevisiText').text(invoice.catatan_revisi || '-');
                $('#formReviseInvoice').attr('action', `/invoices/${invoiceId}`);
                $('#reviseErrorMessages').addClass('d-none');
                $('#modalReviseInvoice').modal('show');
            },
            error: function(xhr) {
                alert('Gagal memuat data invoice');
            }
        });
    });

    // Submit Revise Invoice
    $('#formReviseInvoice').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let action = $(this).attr('action');

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalReviseInvoice').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let errorList = $('#reviseErrorList');
                errorList.html('');
                
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || xhr.responseJSON.error;
                    if (typeof errors === 'object') {
                        $.each(errors, function(key, value) {
                            if (Array.isArray(value)) {
                                $.each(value, function(index, message) {
                                    errorList.append('<li>' + message + '</li>');
                                });
                            } else {
                                errorList.append('<li>' + value + '</li>');
                            }
                        });
                    } else {
                        errorList.append('<li>' + (errors || 'Terjadi kesalahan') + '</li>');
                    }
                } else {
                    errorList.append('<li>' + (xhr.responseJSON?.error || 'Gagal revisi invoice') + '</li>');
                }
                
                $('#reviseErrorMessages').removeClass('d-none');
            }
        });
    });

    // Detail Invoice
    $(document).on('click', '.detail-invoice', function(e) {
        e.preventDefault();
        let invoiceId = $(this).data('id');

        // Reset modal
        $('#invoiceDetailContent').hide();
        $('#invoiceDetailLoading').show();
        $('#modalDetailInvoice').modal('show');

        $.ajax({
            url: `/invoices/${invoiceId}`,
            type: 'GET',
            success: function(response) {
                let invoice = response.invoice;
                let po = invoice.purchase_order;

                // Format date helper
                function formatDate(dateString) {
                    if (!dateString) return '-';
                    let date = new Date(dateString);
                    let day = String(date.getDate()).padStart(2, '0');
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }

                function formatDateTime(dateString) {
                    if (!dateString) return '-';
                    let date = new Date(dateString);
                    let day = String(date.getDate()).padStart(2, '0');
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let year = date.getFullYear();
                    let hours = String(date.getHours()).padStart(2, '0');
                    let minutes = String(date.getMinutes()).padStart(2, '0');
                    return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                }

                // Fill Purchase Order Information
                $('#invoice_detail_po_number').text(po.po_number || '-');
                $('#invoice_detail_date').text(formatDate(po.date));
                $('#invoice_detail_delivery_date').text(formatDate(po.delivery_date));
                $('#invoice_detail_currency').text(po.currency || '-');
                $('#invoice_detail_item_count').text(po.items ? po.items.length : 0);
                $('#invoice_detail_supplier').text(po.supplier ? po.supplier.nama : '-');

                // Fill Invoice Information
                let statusBadge = '';
                if (invoice.status == 'pending') {
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                } else if (invoice.status == 'revised') {
                    statusBadge = '<span class="badge badge-danger">Revised</span>';
                } else if (invoice.status == 'approved') {
                    statusBadge = '<span class="badge badge-success">Approved</span>';
                } else if (invoice.status == 'rejected') {
                    statusBadge = '<span class="badge badge-danger">Rejected</span>';
                }
                $('#invoice_detail_status').html(statusBadge);
                $('#invoice_detail_upload_date').text(formatDateTime(invoice.created_at));

                // Fill Items Table
                let itemsBody = $('#invoice_detail_items_body');
                itemsBody.html('');

                if (po.items && po.items.length > 0) {
                    po.items.forEach(function(item, index) {
                        let pricePerUnit = item.price_per_unit ? parseFloat(item.price_per_unit).toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) : '0.00';

                        let netValue = item.net_value ? parseFloat(item.net_value).toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) : '0.00';

                        itemsBody.append(
                            '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + (item.item_number || '-') + '</td>' +
                            '<td>' + (item.material_code || '-') + '</td>' +
                            '<td>' + (item.vendor_material || '-') + '</td>' +
                            '<td>' + (item.description || '-') + '</td>' +
                            '<td class="text-center">' + (item.quantity || 0) + '</td>' +
                            '<td class="text-right">' + pricePerUnit + '</td>' +
                            '<td class="text-right">' + netValue + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    itemsBody.append('<tr><td colspan="8" class="text-center">Tidak ada data items</td></tr>');
                }

                // Set button click handlers for opening documents in new tab
                $('#btn_open_invoice').off('click').on('click', function() {
                    window.open(`/invoices/${invoiceId}/download-invoice`, '_blank');
                });

                $('#btn_open_surat_jalan').off('click').on('click', function() {
                    window.open(`/invoices/${invoiceId}/download-surat-jalan`, '_blank');
                });

                $('#btn_open_faktur_pajak').off('click').on('click', function() {
                    window.open(`/invoices/${invoiceId}/download-faktur-pajak`, '_blank');
                });

                // Show content
                $('#invoiceDetailLoading').hide();
                $('#invoiceDetailContent').show();
            },
            error: function() {
                $('#invoiceDetailLoading').html('<div class="alert alert-danger">Gagal memuat data Invoice</div>');
            }
        });
    });
});
</script>
@endpush
