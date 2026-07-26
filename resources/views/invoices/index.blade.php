@extends('layout.main')

@section('page_title')
Invoice
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Invoice</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Nav tabs -->
        <div class="bg-white p-0 border-b border-gray-200">
            <ul class="nav flex space-x-2 px-4 py-3" id="invoiceTabs" role="tablist">
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-yellow-100 text-yellow-800" id="ongoing-tab" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="true">
                        <i class="fas fa-hourglass-half mr-2"></i> Sedang Diproses <span class="ml-2 inline-block bg-yellow-500 text-white text-xs px-2 py-0.5 rounded">{{ $ongoingInvoices->total() }}</span>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i> Selesai <span class="ml-2 inline-block bg-green-600 text-white text-xs px-2 py-0.5 rounded">{{ $completedInvoices->total() }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body table-responsive p-0">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 0; border-radius: 0;">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 0; border-radius: 0;">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 0; border-radius: 0;">
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

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Ongoing Tab -->
                <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                    @if($userRole === 'Supplier' && $purchaseOrdersWithoutInvoice->count() > 0)
                    <div class="bg-blue-50 text-blue-700 p-3 rounded m-3">
                        <i class="fas fa-info-circle mr-2"></i> Terdapat {{ $purchaseOrdersWithoutInvoice->count() }} Purchase Order yang siap untuk di-invoice.
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal PO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Revisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="250">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoingInvoices as $invoice)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($ongoingInvoices->currentPage() - 1) * $ongoingInvoices->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900"><strong>{{ $invoice->purchaseOrder->po_number }}</strong></td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $invoice->purchaseOrder->created_at ? $invoice->purchaseOrder->created_at->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($invoice->purchaseOrder->supplier)
                                        <span class="badge badge-info">{{ $invoice->purchaseOrder->supplier->nama }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($invoice->status == 'pending')
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded">Pending</span>
                                        @elseif($invoice->status == 'revised')
                                        <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded">Revised</span>
                                        @elseif(in_array($invoice->status, ['completed', 'ready_for_payment'], true))
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Completed</span>
                                        @elseif($invoice->status == 'paid')
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Paid</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($invoice->catatan_revisi)
                                        <span class="text-danger" title="{{ $invoice->catatan_revisi }}">
                                            <i class="fas fa-exclamation-circle"></i> Ada catatan
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($userRole === 'Supplier' && $invoice->status == 'revised')
                                            <button class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded revise-invoice-supplier" data-id="{{ $invoice->id }}" title="Upload Revisi">
                                                <i class="fas fa-upload mr-2"></i> Revisi
                                            </button>
                                            @endif

                                            @if($userRole === 'Admin' && in_array($invoice->status, ['pending', 'revised']))
                                            <button class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded approve-invoice" data-id="{{ $invoice->id }}" title="Aksi">
                                                <i class="fas fa-tasks mr-2"></i> Aksi
                                            </button>
                                            @endif

                                            @if($invoice->hasAllDocuments())
                                            <button class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-invoice" data-id="{{ $invoice->id }}" title="Lihat Detail">
                                                <i class="fas fa-eye mr-2"></i> Detail
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada Invoice yang sedang diproses</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($userRole === 'Supplier' && $purchaseOrdersWithoutInvoice->count() > 0)
                    <div class="mt-4 border border-blue-200 rounded-lg overflow-hidden">
                        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                            <h6 class="mb-0 text-blue-800 font-semibold">
                                <i class="fas fa-file-upload mr-2"></i> PO Siap Di-invoice
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($purchaseOrdersWithoutInvoice as $po)
                                    <tr>
                                        <td class="px-6 py-3 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-900"><strong>{{ $po->po_number }}</strong></td>
                                        <td class="px-6 py-3 text-sm text-gray-900">{{ $po->created_at ? $po->created_at->format('d/m/Y') : '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            @if($po->supplier)
                                            <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded">Belum Upload</span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            <button class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded upload-invoice" data-po-id="{{ $po->id }}" title="Upload Invoice">
                                                <i class="fas fa-upload mr-2"></i> Upload Invoice
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal PO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Update</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="250">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedInvoices as $invoice)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($completedInvoices->currentPage() - 1) * $completedInvoices->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900"><strong>{{ $invoice->purchaseOrder->po_number }}</strong></td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $invoice->purchaseOrder->created_at ? $invoice->purchaseOrder->created_at->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($invoice->purchaseOrder->supplier)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">{{ $invoice->purchaseOrder->supplier->nama }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if(in_array($invoice->status, ['completed', 'ready_for_payment'], true))
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Completed</span>
                                        @elseif($invoice->status == 'paid')
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Paid</span>
                                        @elseif($invoice->status == 'rejected')
                                        <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $invoice->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($invoice->hasAllDocuments())
                                            <button class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-invoice" data-id="{{ $invoice->id }}" title="Lihat Detail">
                                                <i class="fas fa-eye mr-2"></i> Detail
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tidak ada Invoice yang selesai</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <h6>Sedang Diproses</h6>
                    {{ $ongoingInvoices->render() }}
                </div>
                <div class="col-md-6">
                    <h6>Selesai</h6>
                    {{ $completedInvoices->render() }}
                </div>
            </div>
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
                        <input type="file" class="form-control-file js-multi-file-input" id="invoice_file" name="invoice_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="invoice_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>

                    <div class="form-group">
                        <label for="surat_jalan_file">File Surat Jalan/ASN <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file js-multi-file-input" id="surat_jalan_file" name="surat_jalan_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="surat_jalan_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>

                    <div class="form-group">
                        <label for="faktur_pajak_file">File Faktur Pajak <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file js-multi-file-input" id="faktur_pajak_file" name="faktur_pajak_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="faktur_pajak_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
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
                        <input type="file" class="form-control-file js-multi-file-input" id="revise_invoice_file" name="invoice_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="revise_invoice_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>

                    <div class="form-group">
                        <label for="revise_surat_jalan_file">File Surat Jalan/ASN (Baru) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file js-multi-file-input" id="revise_surat_jalan_file" name="surat_jalan_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="revise_surat_jalan_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>

                    <div class="form-group">
                        <label for="revise_faktur_pajak_file">File Faktur Pajak (Baru) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file js-multi-file-input" id="revise_faktur_pajak_file" name="faktur_pajak_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <ul id="revise_faktur_pajak_file_list" class="list-unstyled small text-muted mt-2 mb-0"></ul>
                        <small class="form-text text-muted">Format: PDF, JPG, PNG (Maksimal 10MB per file). Bisa pilih beberapa file sekaligus atau tambah file secara bertahap.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
                        <i class="fas fa-upload"></i> Upload Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL APPROVE/REJECT INVOICE (Admin) -->
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
                                        <div id="approve_invoice_downloads" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Surat Jalan/ASN</h6>
                                        <div id="approve_surat_jalan_downloads" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Faktur Pajak</h6>
                                        <div id="approve_faktur_pajak_downloads" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                <button type="button" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded" id="btnApproveInvoice">
                    <i class="fas fa-check mr-2"></i> Approve
                </button>
                <button type="button" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded" id="btnRejectInvoice">
                    <i class="fas fa-times mr-2"></i> Reject
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
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded" id="btnSubmitRejectRevise">
                        <i class="fas fa-check mr-2"></i> Simpan
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
                    <div class="card mb-3" style="position: relative;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Purchase Order</h5>
                        </div>
                        <div class="card-body" style="position: relative; padding-bottom: 120px;">
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

                            <!-- Approval Watermark -->
                            <div id="invoice_approvalWatermark" style="display: none; position: absolute; bottom: 15px; right: 30px; text-align: right; z-index: 10;">
                                <div style="font-size: 28px; font-weight: bold; color: #28a745; opacity: 0.7; letter-spacing: 2px;">APPROVED</div>
                                <div style="font-size: 12px; color: #28a745; opacity: 0.7; margin-top: 5px;">by Dept Head</div>
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
                                            <th>Description</th>
                                            <th width="8%" class="text-center">Qty</th>
                                            <th width="12%" class="text-right">Price Per Unit</th>
                                            <th width="12%" class="text-right">Net Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice_detail_items_body">
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data items</td>
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
                                        <div id="detail_invoice_downloads" class="d-flex flex-wrap justify-content-center gap-2 mt-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <i class="fas fa-truck fa-3x text-success mb-3"></i>
                                        <h6>Surat Jalan/ASN</h6>
                                        <div id="detail_surat_jalan_downloads" class="d-flex flex-wrap justify-content-center gap-2 mt-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <i class="fas fa-receipt fa-3x text-warning mb-3"></i>
                                        <h6>Faktur Pajak</h6>
                                        <div id="detail_faktur_pajak_downloads" class="d-flex flex-wrap justify-content-center gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Setup CSRF token untuk semua AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let currentInvoiceId = null;
        let currentAction = null; // 'reject' or 'revise'
        let currentPoId = null;

        const multiFileStores = {};

        function normalizeFileList(files) {
            if (!files) {
                return [];
            }
            if (typeof files === 'string') {
                try {
                    const parsed = JSON.parse(files);
                    if (Array.isArray(parsed)) {
                        return parsed.filter(Boolean);
                    }
                    return parsed ? [parsed] : [];
                } catch (e) {
                    return files ? [files] : [];
                }
            }
            if (Array.isArray(files)) {
                return files.filter(Boolean);
            }
            return [files];
        }

        function fileIdentity(file) {
            return [file.name, file.size, file.lastModified].join('|');
        }

        function syncInputFiles(input, files) {
            const dataTransfer = new DataTransfer();
            files.forEach(function(file) {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        function renderSelectedFileList(inputId, listId) {
            const files = multiFileStores[inputId] || [];
            const listEl = document.getElementById(listId);

            if (!listEl) {
                return;
            }

            listEl.innerHTML = '';

            if (files.length === 0) {
                listEl.innerHTML = '<li class="text-muted">Belum ada file dipilih</li>';
                return;
            }

            files.forEach(function(file, index) {
                const item = document.createElement('li');
                item.className = 'd-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1';
                item.innerHTML =
                    '<span><i class="fas fa-file mr-1"></i> ' + file.name + ' (' + formatFileSize(file.size) + ')</span>' +
                    '<button type="button" class="btn btn-link btn-sm text-danger p-0 ml-2 js-remove-selected-file" ' +
                    'data-input-id="' + inputId + '" data-list-id="' + listId + '" data-index="' + index + '" title="Hapus">' +
                    '<i class="fas fa-times"></i></button>';
                listEl.appendChild(item);
            });
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) {
                return bytes + ' B';
            }
            if (bytes < 1024 * 1024) {
                return (bytes / 1024).toFixed(1) + ' KB';
            }
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function initMultiFileInput(inputId, listId) {
            const input = document.getElementById(inputId);
            if (!input) {
                return;
            }

            multiFileStores[inputId] = [];

            input.addEventListener('change', function() {
                const existing = multiFileStores[inputId] || [];
                const identities = new Set(existing.map(fileIdentity));

                Array.from(input.files || []).forEach(function(file) {
                    const identity = fileIdentity(file);
                    if (!identities.has(identity)) {
                        existing.push(file);
                        identities.add(identity);
                    }
                });

                multiFileStores[inputId] = existing;
                syncInputFiles(input, existing);
                renderSelectedFileList(inputId, listId);
            });

            renderSelectedFileList(inputId, listId);
        }

        function resetMultiFileInputs(formSelector) {
            $(formSelector).find('.js-multi-file-input').each(function() {
                const inputId = this.id;
                multiFileStores[inputId] = [];
                this.value = '';
                syncInputFiles(this, []);
                const listId = inputId + '_list';
                renderSelectedFileList(inputId, listId);
            });
        }

        function buildInvoiceFormData(form) {
            const formData = new FormData();
            const $form = $(form);
            const token = $form.find('input[name="_token"]').val();

            if (token) {
                formData.append('_token', token);
            }

            const method = $form.find('input[name="_method"]').val();
            if (method) {
                formData.append('_method', method);
            }

            $form.find('input[type="file"][name$="[]"]').each(function() {
                const fieldName = this.name;
                Array.from(this.files || []).forEach(function(file) {
                    formData.append(fieldName, file);
                });
            });

            return formData;
        }

        initMultiFileInput('invoice_file', 'invoice_file_list');
        initMultiFileInput('surat_jalan_file', 'surat_jalan_file_list');
        initMultiFileInput('faktur_pajak_file', 'faktur_pajak_file_list');
        initMultiFileInput('revise_invoice_file', 'revise_invoice_file_list');
        initMultiFileInput('revise_surat_jalan_file', 'revise_surat_jalan_file_list');
        initMultiFileInput('revise_faktur_pajak_file', 'revise_faktur_pajak_file_list');

        $(document).on('click', '.js-remove-selected-file', function() {
            const inputId = $(this).data('input-id');
            const listId = $(this).data('list-id');
            const index = parseInt($(this).data('index'), 10);
            const input = document.getElementById(inputId);

            if (!input || Number.isNaN(index)) {
                return;
            }

            const files = multiFileStores[inputId] || [];
            files.splice(index, 1);
            multiFileStores[inputId] = files;
            syncInputFiles(input, files);
            renderSelectedFileList(inputId, listId);
        });

        function renderDocumentDownloadButtons(containerSelector, invoiceId, files, downloadRoute, options) {
            options = options || {};
            let buttonClass = options.buttonClass || 'inline-flex items-center px-3 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded';
            let singleLabel = options.singleLabel || 'Download';
            let multipleLabelPrefix = options.multipleLabelPrefix || 'Download';
            let iconClass = options.iconClass || 'fas fa-download';

            let container = $(containerSelector);
            container.html('');

            let fileList = normalizeFileList(files);
            if (fileList.length === 0) {
                container.append('<span class="text-muted small">Tidak ada file</span>');
                return;
            }

            fileList.forEach(function(file, index) {
                let label = fileList.length > 1 ? multipleLabelPrefix + ' ' + (index + 1) : singleLabel;
                container.append(
                    '<a href="/invoices/' + invoiceId + '/' + downloadRoute + '?index=' + index + '" ' +
                    'class="' + buttonClass + '" target="_blank">' +
                    '<i class="' + iconClass + ' mr-2"></i> ' + label +
                    '</a>'
                );
            });
        }

        // Upload Invoice (Supplier)
        $(document).on('click', '.upload-invoice', function(e) {
            e.preventDefault();
            currentPoId = $(this).data('po-id');

            $('#formUploadInvoice').attr('action', `/invoices/${currentPoId}/store`);
            $('#formUploadInvoice')[0].reset();
            resetMultiFileInputs('#formUploadInvoice');
            $('#errorMessages').addClass('d-none');
            $('#modalUploadInvoice').modal('show');
        });

        // Submit Upload Invoice
        $('#formUploadInvoice').on('submit', function(e) {
            e.preventDefault();

            let formData = buildInvoiceFormData(this);
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

        // Approve/Reject Invoice (Admin)
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
                    $('#approve_po_date').text(po.created_at ? new Date(po.created_at).toLocaleDateString('id-ID') : '-');

                    renderDocumentDownloadButtons('#approve_invoice_downloads', invoice.id, invoice.invoice_file, 'download-invoice');
                    renderDocumentDownloadButtons('#approve_surat_jalan_downloads', invoice.id, invoice.surat_jalan_file, 'download-surat-jalan');
                    renderDocumentDownloadButtons('#approve_faktur_pajak_downloads', invoice.id, invoice.faktur_pajak_file, 'download-faktur-pajak');

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
                    console.log('Approve success:', response);
                    alert('Invoice berhasil di-approve');
                    $('#modalApproveInvoice').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    console.error('Approve error:', xhr);
                    let errorMsg = 'Gagal approve invoice';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    alert(errorMsg);
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

        // Submit Reject
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
                    $('#formReviseInvoice')[0].reset();
                    resetMultiFileInputs('#formReviseInvoice');
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

            let formData = buildInvoiceFormData(this);
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
                    $('#invoice_detail_date').text(formatDate(po.created_at));
                    $('#invoice_detail_delivery_date').text(formatDate(po.delivery_date));
                    $('#invoice_detail_currency').text(po.currency || '-');
                    $('#invoice_detail_item_count').text(po.items ? po.items.length : 0);
                    $('#invoice_detail_supplier').text(po.supplier ? po.supplier.nama : '-');

                    if (po.shows_dept_head_approval_mark) {
                        $('#invoice_approvalWatermark').show();
                    } else {
                        $('#invoice_approvalWatermark').hide();
                    }

                    // Fill Invoice Information
                    let statusBadge = '';
                    if (invoice.status == 'pending') {
                        statusBadge = '<span class="badge badge-warning">Pending</span>';
                    } else if (invoice.status == 'revised') {
                        statusBadge = '<span class="badge badge-danger">Revised</span>';
                    } else if (invoice.status == 'completed' || invoice.status == 'ready_for_payment') {
                        statusBadge = '<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Completed</span>';
                    } else if (invoice.status == 'paid') {
                        statusBadge = '<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Paid</span>';
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
                                '<td>' + (item.description || '-') + '</td>' +
                                '<td class="text-center">' + (item.quantity || 0) + '</td>' +
                                '<td class="text-right">' + pricePerUnit + '</td>' +
                                '<td class="text-right">' + netValue + '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        itemsBody.append('<tr><td colspan="7" class="text-center">Tidak ada data items</td></tr>');
                    }

                    let detailButtonOptions = {
                        buttonClass: 'inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded',
                        singleLabel: 'Buka Dokumen',
                        multipleLabelPrefix: 'Buka Dokumen',
                        iconClass: 'fas fa-external-link-alt'
                    };

                    renderDocumentDownloadButtons('#detail_invoice_downloads', invoiceId, invoice.invoice_file, 'download-invoice', detailButtonOptions);

                    renderDocumentDownloadButtons('#detail_surat_jalan_downloads', invoiceId, invoice.surat_jalan_file, 'download-surat-jalan', Object.assign({}, detailButtonOptions, {
                        buttonClass: 'inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded'
                    }));

                    renderDocumentDownloadButtons('#detail_faktur_pajak_downloads', invoiceId, invoice.faktur_pajak_file, 'download-faktur-pajak', Object.assign({}, detailButtonOptions, {
                        buttonClass: 'inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded'
                    }));

                    // Show content
                    $('#invoiceDetailLoading').hide();
                    $('#invoiceDetailContent').show();
                },
                error: function() {
                    $('#invoiceDetailLoading').html('<div class="alert alert-danger">Gagal memuat data Invoice</div>');
                }
            });
        });

        // Alur dipotong sampai Admin memvalidasi invoice (approve -> selesai).
    });
</script>
@endpush