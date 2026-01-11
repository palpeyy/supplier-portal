@extends('layout.main')

@section('page_title')
Penerimaan Barang
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Penerimaan Barang</li>
@endsection

@section('isi')
<div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Purchase Order - Pesanan Sedang Diproses</h3>
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

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO Number</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Jumlah Item</th>
                                <th>Delivery Date</th>
                                <th>ETD</th>
                                <th>ETA</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                            <tr>
                                <td>{{ ($purchaseOrders->currentPage() - 1) * $purchaseOrders->perPage() + $loop->iteration }}</td>
                                <td>{{ $po->po_number }}</td>
                                <td>{{ $po->date ? $po->date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($po->supplier)
                                    <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $po->items_count }} item</span>
                                </td>
                                <td>{{ $po->delivery_date ? $po->delivery_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $po->etd ? $po->etd->format('d/m/Y') : '-' }}</td>
                                <td>{{ $po->eta ? $po->eta->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($po->status == 'on_progress')
                                        <span class="badge badge-primary">On Progress</span>
                                    @elseif($po->status == 'received')
                                        <span class="badge badge-success">Received</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $po->status ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>{{ $po->keterangan ?? '-' }}</td>
                                <td>
                                    @if($userRole == 'Admin' && $po->status == 'on_progress')
                                    <a class="btn btn-success btn-sm confirm-received" href="#" data-id="{{ $po->id }}" title="Konfirmasi Barang Diterima">
                                        <i class="fas fa-check"></i> Konfirmasi
                                    </a>
                                    @endif
                                    <a class="btn btn-primary btn-sm detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($po->pdf_path)
                                    <a class="btn btn-info btn-sm" href="{{ route('purchase-orders.download', $po->id) }}" title="Download PDF" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data Penerimaan Barang</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $purchaseOrders->links() }}
                </div>
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
                                            <th width="12%">Vendor Material</th>
                                            <th>Description</th>
                                            <th width="8%" class="text-center">Qty</th>
                                            <th width="12%" class="text-right">Price Per Unit</th>
                                            <th width="12%" class="text-right">Net Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_items_body">
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data items</td>
                                        </tr>
                                    </tbody>
                                </table>
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
        // Detail Purchase Order
        $(document).on('click', '.detail-po', function(e) {
            e.preventDefault();
            let poId = $(this).data('id');

            $('#detailContent').hide();
            $('#detailLoading').show();
            $('#modalDetailPurchaseOrder').modal('show');

            $.ajax({
                url: `/purchase-orders/${poId}`,
                type: 'GET',
                success: function(response) {
                    let po = response.purchase_order;
                    
                    // Format date helper
                    function formatDate(dateString) {
                        if (!dateString) return '-';
                        let date = new Date(dateString);
                        let day = String(date.getDate()).padStart(2, '0');
                        let month = String(date.getMonth() + 1).padStart(2, '0');
                        let year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    }

                    // Fill Purchase Order Information
                    $('#detail_po_number').text(po.po_number || '-');
                    $('#detail_date').text(formatDate(po.date));
                    $('#detail_delivery_date').text(formatDate(po.delivery_date));
                    $('#detail_currency').text(po.currency || '-');
                    $('#detail_item_count').text(po.items_count || 0);
                    $('#detail_supplier').text(po.supplier ? po.supplier.nama : '-');
                    $('#detail_company_address').text(po.company_address || '-');

                    // Fill Items Table
                    let itemsBody = $('#detail_items_body');
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

                    // Show content
                    $('#detailLoading').hide();
                    $('#detailContent').show();
                },
                error: function() {
                    $('#detailLoading').html('<div class="alert alert-danger">Gagal memuat data Purchase Order</div>');
                }
            });
        });

        // Confirm Received
        $(document).on('click', '.confirm-received', function(e) {
            e.preventDefault();
            let poId = $(this).data('id');

            if (confirm('Yakin ingin konfirmasi barang sudah diterima?')) {
                $.ajax({
                    url: `/purchase-orders/${poId}/confirm-received`,
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
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

