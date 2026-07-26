@extends('layout.main')

@section('page_title')
Surat Jalan
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('purchase-orders.penerimaan-barang') }}">Advanced Shipping Notice</a></li>
<li class="breadcrumb-item active">{{ $purchaseOrder->po_number }}</li>
@endsection

@section('isi')
<div class="container-fluid py-3">
    <div class="mb-3">
        <a href="{{ route('purchase-orders.penerimaan-barang') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke ASN
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Terjadi Kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">Surat Jalan — {{ $purchaseOrder->po_number }}</h3>
        </div>
        <div class="card-body pb-0">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-3">
                    <small class="text-muted text-uppercase d-block">PO Number</small>
                    <strong class="text-lg">{{ $purchaseOrder->po_number }}</strong>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <small class="text-muted text-uppercase d-block">Supplier</small>
                    <strong>{{ $purchaseOrder->supplier->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <small class="text-muted text-uppercase d-block">Status PO</small>
                    @php
                    $statusClass = match($purchaseOrder->status) {
                        'on_progress' => 'badge-primary',
                        'received' => 'badge-success',
                        default => 'badge-secondary',
                    };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($purchaseOrder->status)) }}</span>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <small class="text-muted text-uppercase d-block">Total Items</small>
                    <strong class="text-primary">{{ $purchaseOrder->items->count() }} item</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box-open mr-1"></i> Status Pengiriman Barang</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Item</th>
                            <th class="text-center" style="width: 140px;">Dikirim / Total</th>
                            <th class="text-center" style="width: 100px;">Sisa</th>
                            <th style="width: 180px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                        @php
                        $percentage = $item->quantity > 0 ? ($item->quantity_shipped / $item->quantity) * 100 : 0;
                        $barClass = $percentage >= 100 ? 'bg-success' : ($percentage > 0 ? 'bg-primary' : 'bg-secondary');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $item->material_code ?? $item->item_number }}</strong>
                                <span class="text-muted"> — {{ $item->description }}</span>
                            </td>
                            <td class="text-center">{{ $item->quantity_shipped }} / {{ $item->quantity }}</td>
                            <td class="text-center text-warning font-weight-bold">{{ $item->quantity_remaining }}</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $barClass }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($percentage, 0) }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($purchaseOrder->status === 'on_progress' && $userRole === 'Supplier')
        <div class="card-footer">
            <button type="button" class="btn btn-success btn-sm" onclick="confirmAutoGenerate()">
                <i class="fas fa-magic mr-1"></i> Buat Surat Jalan Otomatis
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addShippingModal">
                <i class="fas fa-plus mr-1"></i> Buat Surat Jalan Manual
            </button>
        </div>
        @endif
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-alt mr-1"></i> Daftar Surat Jalan</h3>
        </div>
        <div class="card-body">
            @if($purchaseOrder->shippingDocuments->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>No. Surat Jalan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Total Dikirim</th>
                            <th>Catatan</th>
                            <th class="text-center" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->shippingDocuments as $doc)
                        <tr>
                            <td>
                                <strong>{{ $doc->no_surat_jalan }}</strong>
                                @if($doc->isApproved())
                                <br><span class="badge badge-success mt-1"><i class="fas fa-check-circle"></i> Approved</span>
                                @endif
                            </td>
                            <td>{{ $doc->date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                $docBadge = match($doc->status) {
                                    'draft' => 'badge-warning',
                                    'confirmed' => 'badge-info',
                                    'approved' => 'badge-success',
                                    'received' => 'badge-primary',
                                    'rejected' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                                @endphp
                                <span class="badge {{ $docBadge }}">{{ strtoupper($doc->status) }}</span>
                            </td>
                            <td class="text-center font-weight-bold text-success">{{ $doc->items->sum('quantity_shipped') }} unit</td>
                            <td><small>{{ $doc->notes ?: '-' }}</small></td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-secondary btn-xs preview-shipping-doc" data-id="{{ $doc->id }}" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('purchase-orders.print-shipping-document', [$purchaseOrder->id, $doc->id]) }}"
                                    target="_blank" class="btn btn-primary btn-xs" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if(in_array($userRole, ['Admin', 'Dept. Head']) && $doc->status === 'confirmed')
                                <button type="button" class="btn btn-success btn-xs approve-shipping-doc" data-id="{{ $doc->id }}" title="Approve">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs reject-shipping-doc" data-id="{{ $doc->id }}" title="Reject">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                @elseif(in_array($userRole, ['Admin', 'Dept. Head']) && $doc->status === 'approved')
                                <button type="button" class="btn btn-purple btn-xs mark-received-shipping-doc" data-id="{{ $doc->id }}" title="Mark Received" style="background:#6f42c1;color:#fff;">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs reject-shipping-doc" data-id="{{ $doc->id }}" title="Reject">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                @elseif($userRole === 'Supplier' && $doc->status === 'draft')
                                <button type="button" class="btn btn-danger btn-xs delete-shipping-doc" data-id="{{ $doc->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        <tr class="bg-light">
                            <td colspan="6" class="py-2">
                                <small class="text-muted font-weight-bold d-block mb-1">Barang yang dikirim:</small>
                                <ul class="mb-0 pl-3">
                                    @foreach($doc->items as $docItem)
                                    <li>
                                        {{ $docItem->purchaseOrderItem->material_code ?? 'N/A' }} — {{ $docItem->purchaseOrderItem->description }}
                                        <strong class="text-primary">({{ $docItem->quantity_shipped }} unit)</strong>
                                    </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-2 font-weight-bold">Belum ada surat jalan</p>
                @if($purchaseOrder->status === 'on_progress' && $userRole === 'Supplier')
                <p class="small mb-3">Buat surat jalan untuk melanjutkan pengiriman barang</p>
                <button type="button" class="btn btn-success btn-sm" onclick="confirmAutoGenerate()">
                    <i class="fas fa-magic mr-1"></i> Auto Generate
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addShippingModal">
                    <i class="fas fa-plus mr-1"></i> Buat Manual
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@if($userRole === 'Supplier')
<div class="modal fade" id="addShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-invoice mr-2"></i>Buat Surat Jalan Manual</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('purchase-orders.store-shipping-document', $purchaseOrder->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="no_surat_jalan">No Surat Jalan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_surat_jalan') is-invalid @enderror"
                            id="no_surat_jalan" name="no_surat_jalan" placeholder="e.g., SJ-2024-001" required>
                        @error('no_surat_jalan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                id="date" name="date" value="{{ date('Y-m-d') }}" required>
                            @error('date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="etd">ETD <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('etd') is-invalid @enderror"
                                id="etd" name="etd" value="{{ old('etd', $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}" required>
                            @error('etd')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="eta">ETA <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('eta') is-invalid @enderror"
                                id="eta" name="eta" value="{{ old('eta', $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->copy()->addDays(7)->format('Y-m-d') : '') }}" required>
                            @error('eta')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2"></textarea>
                        @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <hr>
                    <h6 class="font-weight-bold mb-3"><i class="fas fa-box mr-1"></i> Barang yang Dikirim</h6>
                    @foreach($purchaseOrder->items as $index => $item)
                    @if($item->quantity_remaining > 0)
                    <div class="border rounded p-3 mb-2 bg-light">
                        <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                        <p class="mb-1 font-weight-bold">{{ $item->material_code ?? 'N/A' }} — {{ $item->description }}</p>
                        <p class="small text-muted mb-2">
                            Total: {{ $item->quantity }} | Dikirim: {{ $item->quantity_shipped }} | Sisa: <strong class="text-warning">{{ $item->quantity_remaining }}</strong>
                        </p>
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <input type="number" name="items[{{ $index }}][quantity_shipped]"
                                class="form-control quantity-input @error('items.' . $index . '.quantity_shipped') is-invalid @enderror"
                                min="0" max="{{ $item->quantity_remaining }}" data-max="{{ $item->quantity_remaining }}" value="0">
                            <div class="input-group-append"><span class="input-group-text">/ {{ $item->quantity_remaining }}</span></div>
                        </div>
                        @error('items.' . $index . '.quantity_shipped')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    @endif
                    @endforeach
                    @if($purchaseOrder->items->where('quantity_remaining', '>', 0)->count() === 0)
                    <div class="alert alert-info mb-0"><i class="fas fa-info-circle mr-1"></i> Semua barang sudah dikirim</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="previewShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Preview Surat Jalan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" id="previewContent" style="min-height: 500px;">
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <a id="printFromPreview" href="#" target="_blank" class="btn btn-primary"><i class="fas fa-print mr-1"></i> Cetak</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const PO_ID = {{ $purchaseOrder->id }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    function confirmAutoGenerate() {
        if (!confirm('Buat Surat Jalan otomatis untuk semua sisa barang?')) {
            return;
        }
        const btn = event.target.closest('button');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';

        fetch(`/purchase-orders/${PO_ID}/shipping-documents/auto-generate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || data.message || 'Terjadi kesalahan'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i> Buat Surat Jalan Otomatis';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i> Buat Surat Jalan Otomatis';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.preview-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                const docId = this.dataset.id;
                const previewUrl = `/purchase-orders/${PO_ID}/shipping-documents/${docId}/print`;
                document.getElementById('printFromPreview').href = previewUrl;
                document.getElementById('previewContent').innerHTML =
                    `<iframe src="${previewUrl}" style="width:100%;height:75vh;border:none;"></iframe>`;
                $('#previewShippingModal').modal('show');
            });
        });

        document.querySelectorAll('.delete-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (!confirm('Hapus surat jalan ini?')) return;
                const docId = this.dataset.id;
                fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r => r.json()).then(data => data.success ? location.reload() : alert('Error: ' + data.error));
            });
        });

        document.querySelectorAll('.approve-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (!confirm('Approve surat jalan ini?')) return;
                const docId = this.dataset.id;
                const btn = this;
                btn.disabled = true;
                fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/approve`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r => r.json()).then(data => {
                    if (data.success) location.reload();
                    else { alert('Error: ' + data.error); btn.disabled = false; }
                });
            });
        });

        document.querySelectorAll('.reject-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (!confirm('Reject surat jalan ini?')) return;
                const docId = this.dataset.id;
                const btn = this;
                btn.disabled = true;
                fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/reject`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r => r.json()).then(data => {
                    if (data.success) location.reload();
                    else { alert('Error: ' + data.error); btn.disabled = false; }
                });
            });
        });

        document.querySelectorAll('.mark-received-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (!confirm('Tandai surat jalan sebagai Received?')) return;
                const docId = this.dataset.id;
                const btn = this;
                btn.disabled = true;
                fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/mark-received`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r => r.json()).then(data => {
                    if (data.success) location.reload();
                    else { alert('Error: ' + data.error); btn.disabled = false; }
                });
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.dataset.max, 10);
                let value = parseInt(this.value, 10) || 0;
                if (value > max) { this.value = max; alert(`Maksimal ${max} unit`); }
                if (value < 0) this.value = 0;
            });
        });
    });
</script>
@endpush
