@extends('layout.main')

@section('page_title')
Advanced Shipping Notice
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Advanced Shipping Notice</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Nav tabs -->
        <div class="bg-white p-0 border-b border-gray-200">
            <ul class="nav flex space-x-2 px-4 py-3" id="penerimaanBarangTabs" role="tablist">
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800" id="ongoing-tab" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="true">
                        <i class="fas fa-hourglass-half mr-2"></i> Sedang Diproses <span class="ml-2 inline-block bg-indigo-500 text-white text-xs px-2 py-0.5 rounded">{{ $ongoingPurchaseOrders->total() }}</span>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i> Selesai Diterima <span class="ml-2 inline-block bg-green-600 text-white text-xs px-2 py-0.5 rounded">{{ $completedPurchaseOrders->total() }}</span>
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

                <!-- ✅ Tab Sedang Diproses (Ongoing) -->
                <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surat Jalan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoingPurchaseOrders as $po)
                                @php $latestSj = $po->latestShippingDocument(); @endphp
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($ongoingPurchaseOrders->currentPage() - 1) * $ongoingPurchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <a href="{{ route('purchase-orders.shipping-documents', $po->id) }}" class="text-primary hover:underline">
                                            <i class="fas fa-file-alt mr-1"></i> Surat Jalan
                                        </a>
                                        @if($po->shippingDocuments->count() > 0)
                                        <span class="ml-2 inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">{{ $po->shippingDocuments->count() }} dokumen</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->po_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->supplier)
                                        <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->createdBy)
                                        <span class="badge badge-secondary">{{ $po->createdBy->name }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $latestSj?->etd ? $latestSj->etd->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $latestSj?->eta ? $latestSj->eta->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3">
                                        @if($po->status == 'on_progress')
                                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded">On Progress</span>
                                        @else
                                        <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded">{{ $po->status ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($userRole == 'Admin' || $userRole == 'Dept. Head')
                                            @if($po->shippingDocuments->count() > 0 && $po->shippingDocuments->where('status', 'confirmed')->count() > 0)
                                            <a class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded" href="{{ route('purchase-orders.shipping-documents', $po->id) }}" title="Lihat & Approve Surat Jalan">
                                                <i class="fas fa-check-circle mr-1"></i> Approve SJ
                                            </a>
                                            @elseif($po->shippingDocuments->count() > 0)
                                            <a class="inline-flex items-center px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-semibold rounded" href="{{ route('purchase-orders.shipping-documents', $po->id) }}" title="Lihat Surat Jalan">
                                                <i class="fas fa-file-invoice mr-1"></i> Lihat SJ
                                            </a>
                                            @else
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-300 text-gray-700 text-xs font-semibold rounded cursor-not-allowed" title="Menunggu Supplier membuat SJ">
                                                <i class="fas fa-clock mr-1"></i> Menunggu SJ
                                            </span>
                                            @endif
                                            @endif
                                            @if($po->status == 'on_progress' && $userRole == 'Supplier')
                                            <a class="inline-flex items-center px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded" href="{{ route('purchase-orders.shipping-documents', $po->id) }}" title="Input Surat Jalan">
                                                <i class="fas fa-file-invoice mr-1"></i> Input SJ
                                            </a>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Tidak ada data pesanan sedang diproses</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Ongoing -->
                    <div class="px-4 py-3">
                        {{ $ongoingPurchaseOrders->render() }}
                    </div>
                </div>
                <!-- ✅ AKHIR Tab Ongoing -->

                <!-- ✅ Tab Selesai Diterima (Completed) — sejajar dengan #ongoing, bukan di dalamnya -->
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surat Jalan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedPurchaseOrders as $po)
                                @php $latestSj = $po->latestShippingDocument(); @endphp
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($completedPurchaseOrders->currentPage() - 1) * $completedPurchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <a href="{{ route('purchase-orders.shipping-documents', $po->id) }}" class="text-primary hover:underline">
                                            <i class="fas fa-file-alt mr-1"></i> Surat Jalan
                                        </a>
                                        @if($po->shippingDocuments->count() > 0)
                                        <span class="ml-2 inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">{{ $po->shippingDocuments->count() }} dokumen</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->po_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->supplier)
                                        <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->createdBy)
                                        <span class="badge badge-secondary">{{ $po->createdBy->name }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $latestSj?->etd ? $latestSj->etd->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $latestSj?->eta ? $latestSj->eta->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Received</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Tidak ada data barang yang selesai diterima</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Completed -->
                    <div class="px-4 py-3">
                        {{ $completedPurchaseOrders->render() }}
                    </div>
                </div>
                <!-- ✅ AKHIR Tab Completed -->

            </div>
            <!-- AKHIR tab-content -->

        </div>
    </div>

    <!-- MODAL PREVIEW FILE PDF -->
    <div class="modal fade" id="modalPreviewFile" tabindex="-1" role="dialog" aria-labelledby="modalPreviewFileLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewFileLabel">
                        <i class="fas fa-file-pdf"></i> Preview File
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="previewLoading" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat file...</p>
                    </div>

                    <div id="previewContent" style="display: none;">
                        <iframe id="pdfIframe" style="width: 100%; height: 600px; border: none;"></iframe>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Tutup</button>
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded" id="btnPrintFile">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <a id="btnDownloadFile" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded" target="_blank">
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL PURCHASE ORDER -->
    <div class="modal fade" id="modalDetailPurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="modalDetailPurchaseOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailPurchaseOrderLabel">
                        <i class="fas fa-eye"></i> Detail Purchase Order
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="detailLoading" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div id="detailContent" style="display: none;">
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
                                                <td><strong id="detail_po_number">-</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal PO</th>
                                                <td>:</td>
                                                <td id="detail_date">-</td>
                                            </tr>
                                            <tr>
                                                <th>Delivery Date</th>
                                                <td>:</td>
                                                <td id="detail_delivery_date">-</td>
                                            </tr>
                                            <tr>
                                                <th>Currency</th>
                                                <td>:</td>
                                                <td><span class="badge badge-info" id="detail_currency">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Item</th>
                                                <td>:</td>
                                                <td><span class="badge badge-primary" id="detail_item_count">-</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <th width="40%">Supplier</th>
                                                <td>:</td>
                                                <td id="detail_supplier">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <th width="15%">Company Address</th>
                                                <td width="2%">:</td>
                                                <td id="detail_company_address">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Approval Watermark -->
                                <div id="approvalWatermark" style="display: none; position: absolute; bottom: 15px; right: 30px; text-align: right; z-index: 10;">
                                    <div style="font-size: 28px; font-weight: bold; color: #28a745; opacity: 0.7; letter-spacing: 2px;">APPROVED</div>
                                    <div style="font-size: 12px; color: #28a745; opacity: 0.7; margin-top: 5px;">by Dept Head</div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Order Items -->
                        <div class="card">
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
                                        <tbody id="detail_items_body">
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data items</td>
                                            </tr>
                                        </tbody>
                                    </table>
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

        // ============================================================
        // Tab switching — aktifkan style tab yang dipilih
        // ============================================================
        $('#penerimaanBarangTabs a[data-toggle="tab"]').on('click', function() {
            // Reset semua tab ke style tidak aktif
            $('#penerimaanBarangTabs a').removeClass('bg-indigo-100 text-indigo-800')
                                        .addClass('text-gray-700 hover:bg-gray-100');
            // Set tab yang diklik menjadi aktif
            $(this).removeClass('text-gray-700 hover:bg-gray-100')
                   .addClass('bg-indigo-100 text-indigo-800');
        });

        // ============================================================
        // Preview PDF
        // ============================================================
        $(document).on('click', '.preview-pdf', function(e) {
            e.preventDefault();
            let poId = $(this).data('id');
            let pdfUrl = `/purchase-orders/${poId}/preview-pdf`;

            $('#previewContent').hide();
            $('#previewLoading').show().html('<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat file...</p>');
            $('#modalPreviewFile').modal('show');

            $('#btnDownloadFile').attr('href', `/purchase-orders/${poId}/download`);
            $('#btnPrintFile').data('pdf-url', pdfUrl);

            $('#pdfIframe').off('load.previewPo').on('load.previewPo', function() {
                $('#previewLoading').hide();
                $('#previewContent').show();
            });

            $('#pdfIframe').attr('src', pdfUrl);
        });

        // ============================================================
        // Print PDF
        // ============================================================
        $(document).on('click', '#btnPrintFile', function() {
            let pdfUrl = $(this).data('pdf-url');
            if (pdfUrl) {
                let printWindow = window.open(pdfUrl, '_blank');
                printWindow.addEventListener('load', function() {
                    printWindow.print();
                });
            }
        });

        // ============================================================
        // Detail Purchase Order
        // ============================================================
        $(document).on('click', '.detail-po', function(e) {
            e.preventDefault();
            let poId = $(this).data('id');

            $('#detailContent').hide();
            $('#detailLoading').show().html('<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat data...</p>');
            $('#modalDetailPurchaseOrder').modal('show');

            $.ajax({
                url: `/purchase-orders/${poId}`,
                type: 'GET',
                success: function(response) {
                    let po = response.purchase_order;

                    function formatDate(dateString) {
                        if (!dateString) return '-';
                        let date = new Date(dateString);
                        let day   = String(date.getDate()).padStart(2, '0');
                        let month = String(date.getMonth() + 1).padStart(2, '0');
                        let year  = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    }

                    $('#detail_po_number').text(po.po_number || '-');
                    $('#detail_date').text(formatDate(po.created_at));
                    $('#detail_delivery_date').text(formatDate(po.delivery_date));
                    $('#detail_currency').text(po.currency || '-');
                    $('#detail_item_count').text(po.items_count || 0);
                    $('#detail_supplier').text(po.supplier ? po.supplier.nama : '-');
                    $('#detail_company_address').text(po.resolved_company_address || po.company_address || '-');

                    if (po.shows_dept_head_approval_mark) {
                        $('#approvalWatermark').show();
                    } else {
                        $('#approvalWatermark').hide();
                    }

                    let itemsBody = $('#detail_items_body');
                    itemsBody.html('');

                    if (po.items && po.items.length > 0) {
                        po.items.forEach(function(item, index) {
                            let pricePerUnit = item.price_per_unit
                                ? parseFloat(item.price_per_unit).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                : '0.00';
                            let netValue = item.net_value
                                ? parseFloat(item.net_value).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                : '0.00';

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

                    $('#detailLoading').hide();
                    $('#detailContent').show();
                },
                error: function() {
                    $('#detailLoading').html('<div class="alert alert-danger">Gagal memuat data Purchase Order</div>');
                }
            });
        });

        // ============================================================
        // Confirm Received
        // ============================================================
        $(document).on('click', '.confirm-received', function(e) {
            e.preventDefault();
            let poId = $(this).data('id');

            if (confirm('Yakin ingin konfirmasi barang sudah diterima?')) {
                $.ajax({
                    url: `/purchase-orders/${poId}/confirm-received`,
                    type: 'POST',
                    data: { '_token': '{{ csrf_token() }}' },
                    success: function() {
                        location.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.error || 'Gagal konfirmasi penerimaan barang';
                        alert(errorMsg);
                    }
                });
            }
        });

    });
</script>
@endpush