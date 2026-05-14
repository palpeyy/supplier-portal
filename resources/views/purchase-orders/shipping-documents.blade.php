@extends('layout.main')

@section('page_title')
Surat Jalan / Pengiriman Barang
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('purchase-orders.penerimaan-barang') }}">Advanced Shipping Notice</a></li>
<li class="breadcrumb-item active">Surat Jalan - {{ $purchaseOrder->po_number }}</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- PO Info Card -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">PO Number</label>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Supplier</label>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $purchaseOrder->supplier->nama ?? '-' }}</p>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Status PO</label>
                    <div class="mt-1">
                        <span class="inline-block px-3 py-1 rounded-full text-white text-sm font-semibold 
                            @if($purchaseOrder->status === 'on_progress') bg-blue-600
                            @elseif($purchaseOrder->status === 'received') bg-green-600
                            @else bg-gray-600
                            @endif">
                            {{ str_replace('_', ' ', ucfirst($purchaseOrder->status)) }}
                        </span>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Total Items</label>
                    <p class="text-xl font-bold text-indigo-600 mt-1">{{ $purchaseOrder->items->count() }} item</p>
                </div>
            </div>
        </div>

        <!-- Items Shipped Status -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-bold text-gray-900 mb-5">📦 Status Pengiriman Barang</h3>
            <div class="space-y-3">
                @foreach($purchaseOrder->items as $item)
                <div class="flex items-center gap-4 bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-200 hover:shadow-sm transition-all">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $item->material_code ?? $item->item_number }} - {{ $item->description }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">Total: <span class="font-semibold">{{ $item->quantity }}</span> unit</p>
                    </div>
                    <div class="text-center min-w-fit">
                        <p class="text-sm font-bold text-gray-900">{{ $item->quantity_shipped }} / {{ $item->quantity }}</p>
                        <p class="text-xs text-gray-600">Sisa: <span class="font-bold text-orange-600">{{ $item->quantity_remaining }}</span></p>
                    </div>
                    <div style="width: 160px;">
                        <div class="w-full bg-gray-300 rounded-full h-2.5">
                            @php
                            $percentage = $item->quantity > 0 ? ($item->quantity_shipped / $item->quantity) * 100 : 0;
                            $barColor = $percentage >= 100 ? 'bg-green-500' : ($percentage > 0 ? 'bg-blue-500' : 'bg-gray-300');
                            @endphp
                            <div class="{{ $barColor }} h-2.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 text-center">{{ number_format($percentage, 0) }}%</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Actions & Alerts -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-semibold">Terjadi Kesalahan:</span>
                </div>
                <ul class="list-disc list-inside ml-6">
                    @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($purchaseOrder->status === 'on_progress' && $userRole === 'Supplier')
            <div class="flex gap-3 flex-wrap">
                <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold flex items-center gap-2 transition-colors"
                    onclick="confirmAutoGenerate()">
                    <i class="fas fa-magic"></i> Buat Surat Jalan Otomatis
                </button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold flex items-center gap-2 transition-colors"
                    data-toggle="modal" data-target="#addShippingModal">
                    <i class="fas fa-plus"></i> Buat Surat Jalan Manual
                </button>
            </div>
            @endif
        </div>

        <!-- Shipping Documents List -->
        <div class="p-6">
            @if($purchaseOrder->shippingDocuments->count() > 0)
            <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Daftar Surat Jalan</h3>
            <div class="space-y-4">
                @foreach($purchaseOrder->shippingDocuments as $doc)
                <div class="border border-gray-300 rounded-lg p-5 hover:shadow-lg transition-shadow bg-white">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-lg font-bold text-gray-900">{{ $doc->no_surat_jalan }}</h4>
                                <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    @if($doc->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($doc->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($doc->status === 'approved') bg-green-100 text-green-800
                                    @elseif($doc->status === 'received') bg-purple-100 text-purple-800
                                    @elseif($doc->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ strtoupper($doc->status) }}
                                </span>
                                @if($doc->isApproved())
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-600 text-white flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i> Approved by Admin
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-calendar mr-1"></i>
                                {{ $doc->date->format('d M Y') }}
                            </p>
                            @if($doc->notes)
                            <p class="text-sm text-gray-600 mt-2 italic">
                                <i class="fas fa-sticky-note mr-1 text-blue-500"></i>
                                {{ $doc->notes }}
                            </p>
                            @endif
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 transition-colors preview-shipping-doc"
                                data-id="{{ $doc->id }}" title="Preview Surat Jalan">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <a href="{{ route('purchase-orders.print-shipping-document', [$purchaseOrder->id, $doc->id]) }}"
                                target="_blank"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 transition-colors">
                                <i class="fas fa-print"></i> Cetak
                            </a>

                            @if(in_array($userRole, ['Admin', 'Dept. Head']) && $doc->status === 'confirmed')
                            <!-- Admin Approval Buttons for Confirmed Documents -->
                            <button type="button"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 approve-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}" title="Approve Surat Jalan">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                           <!--<button type="button"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 revise-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}" title="Revise - Kembalikan ke Supplier">
                                <i class="fas fa-edit"></i> Revise
                            </button> -->
                            <button type="button"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 reject-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}" title="Reject - Tolak SJ">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                            @elseif(in_array($userRole, ['Admin', 'Dept. Head']) && $doc->status === 'approved')
                            <!-- Admin Mark as Received for Approved Documents -->
                            <button type="button"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 mark-received-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}" title="Tandai Sebagai Received">
                                <i class="fas fa-check-double"></i> Mark Received
                            </button>
                            <button type="button"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 reject-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}" title="Reject - Tolak SJ">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                            @elseif($userRole === 'Supplier' && $doc->status === 'draft')
                            <!-- Supplier Delete Button (only for draft) -->
                            <button type="button"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-semibold flex items-center gap-2 delete-shipping-doc transition-colors"
                                data-id="{{ $doc->id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Document Details -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm font-bold text-gray-700 mb-3">📦 Barang yang Dikirim:</p>
                        <div class="space-y-2 bg-gray-50 p-3 rounded-lg">
                            @foreach($doc->items as $docItem)
                            <div class="flex justify-between text-sm text-gray-700 pb-2 border-b border-gray-200 last:border-0 last:pb-0">
                                <span>{{ $docItem->purchaseOrderItem->material_code ?? 'N/A' }} - {{ $docItem->purchaseOrderItem->description }}</span>
                                <span class="font-bold text-indigo-600">{{ $docItem->quantity_shipped }} unit</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 text-right">
                            <p class="text-sm font-bold text-gray-900">
                                Total Dikirim: <span class="text-lg text-green-600">{{ $doc->items->sum('quantity_shipped') }} unit</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <i class="fas fa-inbox text-5xl text-gray-400 mb-3"></i>
                <p class="text-xl text-gray-600 mb-4 font-semibold">Belum ada surat jalan yang dibuat</p>
                @if($purchaseOrder->status === 'on_progress')
                <p class="text-sm text-gray-500 mb-6">Mulai dengan membuat surat jalan untuk PO ini</p>
                <div class="flex justify-center gap-3">
                    <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold flex items-center gap-2 transition-colors"
                        onclick="confirmAutoGenerate()">
                        <i class="fas fa-magic"></i> Auto Generate
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold flex items-center gap-2 transition-colors"
                        data-toggle="modal" data-target="#addShippingModal">
                        <i class="fas fa-plus"></i> Buat Manual
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Shipping Document Modal - Only for Supplier -->
@if($userRole === 'Supplier')
<div class="modal fade" id="addShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-to-r from-blue-600 to-blue-700 text-white border-0 rounded-t-lg">
                <h5 class="modal-title font-bold text-lg">
                    <i class="fas fa-file-invoice mr-2"></i>Buat Surat Jalan Manual
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('purchase-orders.store-shipping-document', $purchaseOrder->id) }}" method="POST">
                @csrf
                <div class="modal-body p-6">
                    <div class="form-group mb-4">
                        <label for="no_surat_jalan" class="font-bold text-gray-700">No Surat Jalan <span class="text-red-600">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('no_surat_jalan') is-invalid @enderror"
                            id="no_surat_jalan" name="no_surat_jalan" placeholder="e.g., SJ-2024-001" required>
                        @error('no_surat_jalan')
                        <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="date" class="font-bold text-gray-700">Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" class="form-control form-control-lg @error('date') is-invalid @enderror"
                            id="date" name="date" value="{{ date('Y-m-d') }}" required>
                        @error('date')
                        <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="etd" class="font-bold text-gray-700">ETD (Estimated Time Departure) <span class="text-red-600">*</span></label>
                        <input type="date" class="form-control form-control-lg @error('etd') is-invalid @enderror"
                            id="etd" name="etd" value="{{ old('etd', $purchaseOrder->etd ? $purchaseOrder->etd->format('Y-m-d') : '') }}" required>
                        @error('etd')
                        <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="eta" class="font-bold text-gray-700">ETA (Estimated Time Arrival) <span class="text-red-600">*</span></label>
                        <input type="date" class="form-control form-control-lg @error('eta') is-invalid @enderror"
                            id="eta" name="eta" value="{{ old('eta', $purchaseOrder->eta ? $purchaseOrder->eta->format('Y-m-d') : '') }}" required>
                        @error('eta')
                        <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="notes" class="font-bold text-gray-700">Catatan (Opsional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="2" placeholder="Masukkan catatan tambahan..."></textarea>
                        @error('notes')
                        <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h6 class="font-bold text-gray-900 mb-4">
                        <i class="fas fa-box mr-2 text-blue-600"></i>Barang yang Dikirim <span class="text-red-600">*</span>
                    </h6>
                    <div id="itemsContainer" class="space-y-3">
                        @foreach($purchaseOrder->items as $index => $item)
                        @if($item->quantity_remaining > 0)
                        <div class="border border-gray-300 p-4 rounded-lg bg-gray-50">
                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">

                            <div class="mb-3">
                                <p class="font-semibold text-gray-900">
                                    {{ $item->material_code ?? 'N/A' }} - {{ $item->description }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <strong>Total PO:</strong> {{ $item->quantity }} unit |
                                    <strong>Sudah dikirim:</strong> {{ $item->quantity_shipped }} unit |
                                    <strong>Sisa:</strong> <span class="text-orange-600 font-bold">{{ $item->quantity_remaining }}</span> unit
                                </p>
                            </div>

                            <label class="text-sm font-semibold text-gray-700 mb-2 block">Jumlah Dikirim:</label>
                            <div class="flex items-center gap-2">
                                <input type="number"
                                    name="items[{{ $index }}][quantity_shipped]"
                                    class="form-control quantity-input @error('items.' . $index . '.quantity_shipped') is-invalid @enderror"
                                    min="0"
                                    max="{{ $item->quantity_remaining }}"
                                    data-max="{{ $item->quantity_remaining }}"
                                    value="0"
                                    placeholder="0">
                                <span class="text-gray-600 text-sm font-semibold">/ {{ $item->quantity_remaining }} unit</span>
                            </div>
                            @error('items.' . $index . '.quantity_shipped')
                            <small class="text-red-600">{{ $message }}</small>
                            @enderror
                        </div>
                        @endif
                        @endforeach
                    </div>

                    @if($purchaseOrder->items->where('quantity_remaining', '>', 0)->count() === 0)
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Semua barang sudah dikirim
                    </div>
                    @endif
                </div>

                <div class="modal-footer bg-gray-50 border-t px-6 py-4">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Simpan Surat Jalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Preview Shipping Document Modal -->
<div class="modal fade" id="previewShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xxl" role="document" style="max-width: 95%;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-to-r from-purple-600 to-purple-700 text-white border-0">
                <h5 class="modal-title font-bold text-lg">
                    <i class="fas fa-eye mr-2"></i>Preview Surat Jalan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="previewContent" style="min-height: 600px; overflow-y: auto;">
                <div class="text-center p-6">
                    <i class="fas fa-spinner fa-spin text-3xl text-purple-600"></i>
                    <p class="mt-2 text-gray-600">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 border-t px-6 py-4">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
                <a id="printFromPreview" href="#" target="_blank" class="btn btn-primary bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Cetak
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .progress-bar-info {
        background-color: #17a2b8;
    }

    .progress-bg-green {
        background-color: #28a745;
    }
</style>
@endpush

@push('scripts')
<script>
    // Simpan PO ID sebagai variabel JS yang di-render oleh Blade sekali saja
    const PO_ID = {{ $purchaseOrder->id }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    function confirmAutoGenerate() {
        if (confirm('Apakah Anda yakin ingin membuat Surat Jalan otomatis untuk semua barang dengan sisa pengiriman?\n\nSistem akan:\n- Generate No. Surat Jalan otomatis\n- Menggunakan tanggal hari ini\n- Mengirim semua sisa barang')) {
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        setTimeout(() => location.reload(), 500);
                    } else {
                        alert('Error: ' + (data.error || data.message || 'Terjadi kesalahan'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-magic"></i> Buat Surat Jalan Otomatis';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-magic"></i> Buat Surat Jalan Otomatis';
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Preview shipping document
        document.querySelectorAll('.preview-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                const docId = this.dataset.id;
                const previewUrl = `/purchase-orders/${PO_ID}/shipping-documents/${docId}/print`;

                document.getElementById('printFromPreview').href = previewUrl;
                document.getElementById('previewContent').innerHTML =
                    `<iframe src="${previewUrl}" style="width: 100%; height: 100%; border: none; min-height: 800px;"></iframe>`;

                $('#previewShippingModal').modal('show');
            });
        });

        // Delete shipping document
        document.querySelectorAll('.delete-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus surat jalan ini?')) {
                    const docId = this.dataset.id;

                    fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + data.error);
                            }
                        })
                        .catch(error => alert('Error: ' + error));
                }
            });
        });

        // Approve shipping document
        document.querySelectorAll('.approve-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin approve surat jalan ini?')) {
                    const docId = this.dataset.id;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Surat jalan berhasil di-approve');
                                location.reload();
                            } else {
                                alert('Error: ' + data.error);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Approve';
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Approve';
                        });
                }
            });
        });

        // Reject shipping document
        document.querySelectorAll('.reject-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin reject (tolak) surat jalan ini?')) {
                    const docId = this.dataset.id;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Surat jalan berhasil di-reject');
                                location.reload();
                            } else {
                                alert('Error: ' + data.error);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Reject';
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Reject';
                        });
                }
            });
        });

        // Revise shipping document
        document.querySelectorAll('.revise-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin revise (kembalikan) surat jalan ini ke Supplier?\n\nSurat jalan akan dikembalikan ke status draft untuk di-edit ulang.')) {
                    const docId = this.dataset.id;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/revise`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Surat jalan berhasil di-revise (dikembalikan ke Supplier)');
                                location.reload();
                            } else {
                                alert('Error: ' + data.error);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-edit mr-2"></i>Revise';
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-edit mr-2"></i>Revise';
                        });
                }
            });
        });

        // Mark shipping document as received
        document.querySelectorAll('.mark-received-shipping-doc').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menandai surat jalan ini sebagai Received?')) {
                    const docId = this.dataset.id;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    fetch(`/purchase-orders/${PO_ID}/shipping-documents/${docId}/mark-received`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Surat jalan berhasil ditandai sebagai Received');
                                location.reload();
                            } else {
                                alert('Error: ' + data.error);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-check-double mr-2"></i>Mark Received';
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-check-double mr-2"></i>Mark Received';
                        });
                }
            });
        });

        // Validate quantity input
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.dataset.max);
                const value = parseInt(this.value) || 0;

                if (value > max) {
                    this.value = max;
                    alert(`Maksimal yang dapat dikirim adalah ${max} unit`);
                }
                if (value < 0) {
                    this.value = 0;
                }
            });

            input.addEventListener('input', function() {
                const max = parseInt(this.dataset.max);
                const value = parseInt(this.value) || 0;
                if (value > max) {
                    this.classList.add('border-red-500');
                } else if (value >= 0) {
                    this.classList.remove('border-red-500');
                }
            });
        });
    });
</script>
@endpush