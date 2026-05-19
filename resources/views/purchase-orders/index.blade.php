@extends('layout.main')

@section('page_title')
Purchase Order
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">Purchase Order</li>
@endsection

@section('isi')
<div class="w-full max-w-full px-4 py-6">
    <div class="flex flex-wrap -mx-2 mb-4">
        <div class="flex-1 px-2 w-full sm:w-1/2"></div>
        <div class="flex-1 px-2 w-full sm:w-1/2 text-right">
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200 transform hover:-translate-y-0.5" data-toggle="modal" data-target="#modalPurchaseOrder">
                <i class="fas fa-plus mr-2"></i> Tambah Purchase Order
            </button>
        </div>
    </div>

    <div class="card">
        <!-- Nav tabs -->
        <div class="bg-white p-0 pt-3 border-b border-gray-200">
            <ul class="nav flex space-x-2 px-4" id="purchaseOrderTabs" role="tablist">
                {{-- Menunggu Approval Tab - only for Admin and Dept. Head --}}
                @if($userRole !== 'Supplier')
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-red-100 text-red-800" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                        <i class="fas fa-exclamation-circle mr-2"></i> Menunggu Approval <span class="ml-2 inline-block bg-red-500 text-white text-xs px-2 py-0.5 rounded">{{ $pendingPurchaseOrders->total() }}</span>
                    </a>
                </li>
                @endif
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium @if($userRole === 'Supplier') bg-yellow-100 text-yellow-800 @else text-gray-700 hover:bg-gray-100 @endif" id="ongoing-tab" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="@if($userRole === 'Supplier') true @else false @endif">
                        <i class="fas fa-hourglass-half mr-2"></i> Sedang Diproses <span class="ml-2 inline-block bg-yellow-500 text-white text-xs px-2 py-0.5 rounded">{{ $ongoingPurchaseOrders->total() }}</span>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i> Selesai <span class="ml-2 inline-block bg-green-600 text-white text-xs px-2 py-0.5 rounded">{{ $completedPurchaseOrders->total() }}</span>
                    </a>
                </li>
                {{-- Ditolak Tab - only for Admin and Dept. Head --}}
                @if($userRole !== 'Supplier')
                <li>
                    <a class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">
                        <i class="fas fa-times-circle mr-2"></i> Ditolak <span class="ml-2 inline-block bg-red-500 text-white text-xs px-2 py-0.5 rounded">{{ $rejectedPurchaseOrders->total() }}</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="card-body table-responsive p-0">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 0; border-radius: 0;">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 0; border-radius: 0;">
                {{ $message }}
                @if($errors = Session::get('errors'))
                <ul class="mt-2 mb-0">
                    @foreach($errors as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @endif
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Tab panes -->
            <div class="tab-content">
                {{-- Menunggu Approval Tab - only for Admin and Dept. Head --}}
                @if($userRole !== 'Supplier')
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="250">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPurchaseOrders as $po)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($pendingPurchaseOrders->currentPage() - 1) * $pendingPurchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->po_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->date ? $po->date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->supplier)
                                        <span class="badge badge-info">{{ $po->supplier->nama }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <span class="badge badge-info">{{ $po->items_count }} item</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->delivery_date ? $po->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <span class="badge badge-warning">Pending</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if(in_array($userRole, ['Admin', 'Dept. Head']))
                                            <a class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded approve-po" href="#" data-id="{{ $po->id }}" title="Review & Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded edit-po" href="#" data-id="{{ $po->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded delete-po" data-id="{{ $po->id }}" title="Hapus" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Tidak ada PO yang menunggu approval</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingPurchaseOrders->links() }}
                    </div>
                </div>
                @endif

                <!-- Ongoing Tab -->
                <div class="tab-pane fade @if($userRole === 'Supplier') show active @endif" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="250">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoingPurchaseOrders as $po)
                                <tr>
                                    <td>{{ ($ongoingPurchaseOrders->currentPage() - 1) * $ongoingPurchaseOrders->perPage() + $loop->iteration }}</td>
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
                                        @if($po->createdBy)
                                        <span class="badge badge-secondary">{{ $po->createdBy->name }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $po->items_count }} item</span>
                                    </td>
                                    <td>{{ $po->delivery_date ? $po->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($po->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($po->status == 'approved')
                                        <span class="badge badge-info">Approved</span>
                                        @elseif($po->status == 'on_progress')
                                        <span class="badge badge-primary">On Progress</span>
                                        @elseif($po->status == 'received')
                                        <span class="badge badge-success">Received</span>
                                        @elseif($po->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                        @elseif($po->status == 'supplier_rejected')
                                        <span class="badge badge-danger">Rejected by Supplier</span>
                                        @else
                                        <span class="badge badge-secondary">{{ $po->status ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $po->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($userRole == 'Dept. Head' && $po->status == 'pending')
                                            <a class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded approve-po" href="#" data-id="{{ $po->id }}" title="Review & Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            @if($userRole == 'Supplier' && $po->status == 'approved')
                                            <a class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded approve-po-supplier" href="#" data-id="{{ $po->id }}" title="Review & Confirm">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                            @endif
                                            @if($po->status == 'on_progress' && $po->no_surat_jalan)
                                            <a class="inline-flex items-center px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded" href="{{ route('purchase-orders.print-surat-jalan', $po->id) }}" title="Print Surat Jalan" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded edit-po" href="#" data-id="{{ $po->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded delete-po" data-id="{{ $po->id }}" title="Hapus" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Tidak ada data Purchase Order yang sedang diproses</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $ongoingPurchaseOrders->links() }}
                    </div>
                </div>

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="250">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedPurchaseOrders as $po)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($completedPurchaseOrders->currentPage() - 1) * $completedPurchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->po_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->date ? $po->date->format('d/m/Y') : '-' }}</td>
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
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <span class="badge badge-info">{{ $po->items_count }} item</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->delivery_date ? $po->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        @if($po->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($po->status == 'approved')
                                        <span class="badge badge-info">Approved</span>
                                        @elseif($po->status == 'on_progress')
                                        <span class="badge badge-primary">On Progress</span>
                                        @elseif($po->status == 'received')
                                        <span class="badge badge-success">Received</span>
                                        @elseif($po->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                        @elseif($po->status == 'supplier_rejected')
                                        <span class="badge badge-danger">Rejected by Supplier</span>
                                        @else
                                        <span class="badge badge-secondary">{{ $po->status ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                            @endif
                                            @if($po->status == 'on_progress' && $po->no_surat_jalan)
                                            <a class="inline-flex items-center px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded" href="{{ route('purchase-orders.print-surat-jalan', $po->id) }}" title="Print Surat Jalan" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Tidak ada data Purchase Order yang selesai</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $completedPurchaseOrders->links() }}
                    </div>
                </div>

                {{-- Rejected Tab - only for Admin and Dept. Head --}}
                @if($userRole !== 'Supplier')
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan Ditolak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedPurchaseOrders as $po)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ ($rejectedPurchaseOrders->currentPage() - 1) * $rejectedPurchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->po_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->date ? $po->date->format('d/m/Y') : '-' }}</td>
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
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <span class="badge badge-info">{{ $po->items_count }} item</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $po->delivery_date ? $po->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <div class="bg-red-50 border border-red-200 rounded px-3 py-2">
                                            <p class="text-red-800">{{ $po->keterangan ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <a class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded detail-po" href="#" data-id="{{ $po->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($po->pdf_path)
                                            <button class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded preview-pdf" data-id="{{ $po->id }}" title="Preview PDF" type="button">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                            @endif
                                            <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded edit-po" href="#" data-id="{{ $po->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded delete-po" data-id="{{ $po->id }}" title="Hapus" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada data Purchase Order yang ditolak</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $rejectedPurchaseOrders->links() }}
                    </div>
                </div>
                @endif
            </div>


        </div>
    </div>

    <!-- MODAL TAMBAH PURCHASE ORDER (UPLOAD MULTIPLE PDF) -->
    <div class="modal fade" id="modalPurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="modalPurchaseOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPurchaseOrderLabel">
                        <i class="fas fa-file-pdf"></i> Upload Purchase Order (PDF)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formPurchaseOrder" action="{{ route('purchase-orders.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div id="errorMessages" class="alert alert-danger d-none" role="alert">
                            <strong>Terjadi Kesalahan!</strong>
                            <ul id="errorList" class="mt-2 mb-0"></ul>
                        </div>

                        @if(in_array($userRole ?? '', ['Admin', 'Dept. Head']) && isset($suppliers))
                        <div class="form-group">
                            <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih supplier yang akan menerima purchase order ini</small>
                        </div>
                        @elseif($userRole === 'Supplier')
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Informasi:</strong> PO yang Anda upload akan otomatis terassign ke data supplier Anda
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="pdf_files">Upload File PDF Purchase Order</label>
                            <small class="form-text text-muted mb-2"> file PDF  (Maksimal 10MB per file)</small>
                            <input type="file" class="form-control-file" id="pdf_files" name="pdf_files[]" multiple accept=".pdf" required>
                            <div id="filePreview" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                            <i class="fas fa-upload mr-2"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PURCHASE ORDER -->
    <div class="modal fade" id="modalEditPurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="modalEditPurchaseOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPurchaseOrderLabel">
                        <i class="fas fa-edit"></i> Edit Purchase Order
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditPurchaseOrder" action="" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div id="errorMessagesEdit" class="alert alert-danger d-none" role="alert">
                            <strong>Terjadi Kesalahan!</strong>
                            <ul id="errorListEdit" class="mt-2 mb-0"></ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_po_number">PO Number</label>
                                    <input type="text" class="form-control" id="edit_po_number" name="po_number" placeholder="Masukkan PO Number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_date">Tanggal PO</label>
                                    <input type="date" class="form-control" id="edit_date" name="date" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_delivery_date">Delivery Date</label>
                                    <input type="date" class="form-control" id="edit_delivery_date" name="delivery_date">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_currency">Currency</label>
                                    <select class="form-control" id="edit_currency" name="currency" required>
                                        <option value="IDR">IDR</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_company_address">Company Address</label>
                                    <textarea class="form-control" id="edit_company_address" name="company_address" rows="2" placeholder="Masukkan alamat company"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Simpan</button>
                    </div>
                </form>
            </div>
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
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modalDetailPurchaseOrderLabel">
                        <i class="fas fa-eye"></i> Detail Purchase Order
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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
                                            <tr>
                                                <th>Created By</th>
                                                <td>:</td>
                                                <td id="detail_created_by">-</td>
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

    <!-- MODAL APPROVE/REJECT PURCHASE ORDER -->
    <div class="modal fade" id="modalApprovePurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="modalApprovePurchaseOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modalApprovePurchaseOrderLabel">
                        <i class="fas fa-check-circle"></i> Review & Approve Purchase Order
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
                                                <td><strong id="approve_po_number">-</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal PO</th>
                                                <td>:</td>
                                                <td id="approve_date">-</td>
                                            </tr>
                                            <tr>
                                                <th>Delivery Date</th>
                                                <td>:</td>
                                                <td id="approve_delivery_date">-</td>
                                            </tr>
                                            <tr>
                                                <th>Currency</th>
                                                <td>:</td>
                                                <td><span class="badge badge-info" id="approve_currency">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Item</th>
                                                <td>:</td>
                                                <td><span class="badge badge-primary" id="approve_item_count">-</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <th width="40%">Supplier</th>
                                                <td>:</td>
                                                <td id="approve_supplier">-</td>
                                            </tr>
                                            <tr>
                                                <th>Created By</th>
                                                <td>:</td>
                                                <td id="approve_created_by">-</td>
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
                                                <td id="approve_company_address">-</td>
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
                                                <th>Description</th>
                                                <th width="8%" class="text-center">Qty</th>
                                                <th width="12%" class="text-right">Price Per Unit</th>
                                                <th width="12%" class="text-right">Net Value</th>
                                            </tr>
                                        </thead>
                                        <tbody id="approve_items_body">
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
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded" id="btnRejectPO">
                        <i class="fas fa-times mr-2"></i> Reject
                    </button>
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded" id="btnApprovePO">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL SUCCESS -->
    <div class="modal fade" id="modalSuccess" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Berhasil</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modalSuccessMessage"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL REJECT KETERANGAN -->
    <div class="modal fade" id="modalRejectKeterangan" tabindex="-1" role="dialog" aria-labelledby="modalRejectKeteranganLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalRejectKeteranganLabel">
                        <i class="fas fa-times-circle"></i> Reject Purchase Order
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formRejectPO" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reject_keterangan">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_keterangan" name="keterangan" rows="4" placeholder="Masukkan alasan reject" required></textarea>
                            <small class="form-text text-muted">Mohon isi keterangan alasan reject Purchase Order ini</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded" data-dismiss="modal">Batal</button>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                            <i class="fas fa-times mr-2"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Show success modal jika ada flash session
            @if(Session::has('success'))
            $('#modalSuccessMessage').text("{{ Session::get('success') }}");
            $('#modalSuccess').modal('show');
            @endif
            // Preview PDF (served via app route so it works without public/storage symlink)
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

            // Print PDF
            $(document).on('click', '#btnPrintFile', function() {
                let pdfUrl = $(this).data('pdf-url');
                if (pdfUrl) {
                    let printWindow = window.open(pdfUrl, '_blank');
                    printWindow.addEventListener('load', function() {
                        printWindow.print();
                    });
                }
            });

            // Input mask for ETD/ETA format DD/MM/YYYY
            function setupDateInputMask(input) {
                $(input).on('input', function(e) {
                    let value = $(this).val().replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2);
                    }
                    if (value.length >= 5) {
                        value = value.substring(0, 5) + '/' + value.substring(5, 9);
                    }
                    $(this).val(value);
                });

                $(input).on('keypress', function(e) {
                    let char = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                    }
                });
            }

            // Initialize date input mask for ETD/ETA when modal opens
            $('#modalApprovePurchaseOrder').on('shown.bs.modal', function() {
                setupDateInputMask('#etd');
                setupDateInputMask('#eta');
            });
            // Reset form saat modal dibuka untuk tambah
            $('#modalPurchaseOrder').on('show.bs.modal', function(e) {
                let btnTambah = $(e.relatedTarget);
                if (!btnTambah.hasClass('edit-po')) {
                    $('#formPurchaseOrder')[0].reset();
                    $('#formPurchaseOrder').attr('action', '{{ route("purchase-orders.store") }}');
                    $('#filePreview').html('');
                    $('#errorMessages').addClass('d-none');
                }
            });

            // Preview file yang dipilih
            $('#pdf_files').on('change', function() {
                let files = this.files;
                let preview = $('#filePreview');
                preview.html('');

                if (files.length > 0) {
                    preview.append('<h6>File yang akan diupload (' + files.length + ' file):</h6>');
                    preview.append('<ul class="list-group mt-2">');
                    Array.from(files).forEach(function(file, index) {
                        let fileSize = (file.size / 1024 / 1024).toFixed(2);
                        preview.find('ul').append(
                            '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                            '<span><i class="fas fa-file-pdf text-danger"></i> ' + file.name + '</span>' +
                            '<span class="badge badge-primary badge-pill">' + fileSize + ' MB</span>' +
                            '</li>'
                        );
                    });
                    preview.append('</ul>');
                }
            });

            // Submit form upload PDF
            $('#formPurchaseOrder').on('submit', function(e) {
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

                        // Tutup modal upload
                        $('#modalPurchaseOrder').modal('hide');

                        // Susun pesan
                        let message = response.success || 'Berhasil upload Purchase Order';

                        if (response.errors && response.errors.length > 0) {
                            message += ' | Beberapa file gagal: ' + response.errors.join(', ');
                        }

                        // Tampilkan modal success
                        $('#modalSuccessMessage').text(message);
                        $('#modalSuccess').modal('show');

                        // Reset form
                        $('#formPurchaseOrder')[0].reset();
                        $('#filePreview').html('');
                        $('#errorMessages').addClass('d-none');

                        // OPTIONAL: reload setelah user tutup modal
                        $('#modalSuccess').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                            location.reload();
                        });
                    },

                    error: function(xhr) {
                        let errorList = $('#errorList');
                        errorList.html('');

                        let errors = xhr.responseJSON?.errors || xhr.responseJSON?.error || ['Terjadi kesalahan'];

                        if (Array.isArray(errors)) {
                            errors.forEach(function(message) {
                                errorList.append('<li>' + message + '</li>');
                            });
                        } else if (typeof errors === 'object') {
                            $.each(errors, function(key, value) {
                                if (Array.isArray(value)) {
                                    value.forEach(function(msg) {
                                        errorList.append('<li>' + msg + '</li>');
                                    });
                                } else {
                                    errorList.append('<li>' + value + '</li>');
                                }
                            });
                        } else {
                            errorList.append('<li>' + errors + '</li>');
                        }

                        $('#errorMessages').removeClass('d-none');
                    }
                });
            });

            // Edit Purchase Order
            $(document).on('click', '.edit-po', function(e) {
                e.preventDefault();
                let poId = $(this).data('id');

                $.ajax({
                    url: `/purchase-orders/${poId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        let po = response.purchase_order;
                        $('#formEditPurchaseOrder')[0].reset();
                        $('#edit_po_number').val(po.po_number);
                        $('#edit_date').val(po.date ? po.date.split('T')[0] : '');
                        $('#edit_delivery_date').val(po.delivery_date ? po.delivery_date.split('T')[0] : '');
                        $('#edit_currency').val(po.currency);
                        $('#edit_company').val(po.company || '');
                        $('#edit_company_number').val(po.company_number || '');
                        $('#edit_company_address').val(po.company_address || '');
                        $('#edit_delivery_to').val(po.delivery_to || '');
                        $('#edit_contact_person').val(po.contact_person || '');
                        $('#edit_telephone').val(po.telephone || '');
                        $('#edit_term_of_payment').val(po.term_of_payment || '');

                        $('#formEditPurchaseOrder').attr('action', `/purchase-orders/${poId}`);
                        $('#errorMessagesEdit').addClass('d-none');
                        $('#modalEditPurchaseOrder').modal('show');
                    },
                    error: function() {
                        alert('Gagal memuat data Purchase Order');
                    }
                });
            });

            // Submit form edit
            $('#formEditPurchaseOrder').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let action = $(this).attr('action');

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: formData + '&_method=PUT',
                    success: function(response) {

                        $('#modalPurchaseOrder').modal('hide');

                        let message = response.success;

                        if (response.errors && response.errors.length > 0) {
                            message += ' | Beberapa file gagal: ' + response.errors.join(', ');
                        }

                        $('#modalSuccessMessage').text(message);
                        $('#modalSuccess').modal('show');

                        $('#formPurchaseOrder')[0].reset();
                        $('#filePreview').html('');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = $('#errorListEdit');
                            errorList.html('');

                            $.each(errors, function(key, value) {
                                $.each(value, function(index, message) {
                                    errorList.append('<li>' + message + '</li>');
                                });
                            });

                            $('#errorMessagesEdit').removeClass('d-none');
                        }
                    }
                });
            });

            // Delete Purchase Order
            $(document).on('click', '.delete-po', function(e) {
                e.preventDefault();
                let poId = $(this).data('id');

                if (confirm('Yakin ingin menghapus Purchase Order ini?')) {
                    $.ajax({
                        url: `/purchase-orders/${poId}`,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menghapus Purchase Order');
                        }
                    });
                }
            });

            // Detail Purchase Order
            $(document).on('click', '.detail-po', function(e) {
                e.preventDefault();
                let poId = $(this).data('id');

                // Reset modal
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
                        $('#detail_created_by').text(po.created_by && po.created_by.name ? po.created_by.name : '-');
                        $('#detail_company_address').text(po.company_address || '-');

                        // Show/hide approval watermark based on status
                        if (po.status === 'approved') {
                            $('#approvalWatermark').show();
                        } else {
                            $('#approvalWatermark').hide();
                        }

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

                        // Show content
                        $('#detailLoading').hide();
                        $('#detailContent').show();
                    },
                    error: function() {
                        $('#detailLoading').html('<div class="alert alert-danger">Gagal memuat data Purchase Order</div>');
                    }
                });
            });

            // Approve/Reject Purchase Order
            let currentPoId = null;
            $(document).on('click', '.approve-po', function(e) {
                e.preventDefault();
                currentPoId = $(this).data('id');

                // Reset modal
                $('#approveContent').hide();
                $('#approveLoading').show();
                $('#modalApprovePurchaseOrder').modal('show');

                $.ajax({
                    url: `/purchase-orders/${currentPoId}`,
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
                        $('#approve_po_number').text(po.po_number || '-');
                        $('#approve_date').text(formatDate(po.date));
                        $('#approve_delivery_date').text(formatDate(po.delivery_date));
                        $('#approve_currency').text(po.currency || '-');
                        $('#approve_item_count').text(po.items_count || 0);
                        $('#approve_supplier').text(po.supplier ? po.supplier.nama : '-');
                        $('#approve_created_by').text(po.created_by && po.created_by.name ? po.created_by.name : '-');
                        $('#approve_company_address').text(po.company_address || '-');

                        // Fill Items Table
                        let itemsBody = $('#approve_items_body');
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

                        // Show content
                        $('#approveLoading').hide();
                        $('#approveContent').show();

                        // Hide ETD/ETA form for Dept. Head
                        $('#supplierETDFormTop').hide();
                    },
                    error: function() {
                        $('#approveLoading').html('<div class="alert alert-danger">Gagal memuat data Purchase Order</div>');
                    }
                });
            });

            // Button Approve (for Dept. Head)
            $('#btnApprovePO').on('click', function() {
                if (!currentPoId) return;

                if (confirm('Yakin ingin approve Purchase Order ini?')) {
                    $.ajax({
                        url: `/purchase-orders/${currentPoId}/approve`,
                        type: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#modalApprovePurchaseOrder').modal('hide');
                            location.reload();
                        },
                        error: function(xhr) {
                            let errorMsg = xhr.responseJSON?.error || 'Gagal approve Purchase Order';
                            alert(errorMsg);
                        }
                    });
                }
            });

            // Button Reject
            $('#btnRejectPO').on('click', function() {
                if (!currentPoId) return;
                $('#formRejectPO')[0].reset();
                $('#formRejectPO').attr('action', `/purchase-orders/${currentPoId}/reject`);
                $('#modalApprovePurchaseOrder').modal('hide');
                $('#modalRejectKeterangan').modal('show');
            });

            // Submit Reject Form
            $('#formRejectPO').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let action = $(this).attr('action');

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#modalRejectKeterangan').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            alert(errorMsg);
                        } else {
                            let errorMsg = xhr.responseJSON?.error || 'Gagal reject Purchase Order';
                            alert(errorMsg);
                        }
                    }
                });
            });

            // Approve/Reject Purchase Order by Supplier
            $(document).on('click', '.approve-po-supplier', function(e) {
                e.preventDefault();
                currentPoId = $(this).data('id');

                // Reset modal and form
                $('#approveContent').hide();
                $('#approveLoading').show();
                $('#etd').val('');
                $('#eta').val('');
                $('#no_surat_jalan').val('');
                $('#modalApprovePurchaseOrder').modal('show');

                $.ajax({
                    url: `/purchase-orders/${currentPoId}`,
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
                        $('#approve_po_number').text(po.po_number || '-');
                        $('#approve_date').text(formatDate(po.date));
                        $('#approve_delivery_date').text(formatDate(po.delivery_date));
                        $('#approve_currency').text(po.currency || '-');
                        $('#approve_item_count').text(po.items_count || 0);
                        $('#approve_supplier').text(po.supplier ? po.supplier.nama : '-');
                        $('#approve_company_address').text(po.company_address || '-');

                        // Fill Items Table
                        let itemsBody = $('#approve_items_body');
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

                        // Show content
                        $('#approveLoading').hide();
                        $('#approveContent').show();

                        // Update button handlers for Supplier
                        $('#btnApprovePO').off('click').on('click', function() {
                            if (!currentPoId) return;

                            if (confirm('Yakin ingin approve Purchase Order ini?')) {
                                $.ajax({
                                    url: `/purchase-orders/${currentPoId}/approve-supplier`,
                                    type: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        $('#modalApprovePurchaseOrder').modal('hide');
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        let errorMsg = xhr.responseJSON?.error || 'Gagal approve Purchase Order';
                                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                                            let errors = xhr.responseJSON.errors;
                                            let errorMessages = [];
                                            $.each(errors, function(key, value) {
                                                errorMessages.push(value[0]);
                                            });
                                            errorMsg = errorMessages.join('\n');
                                        }
                                        alert(errorMsg);
                                    }
                                });
                            }
                        });

                        $('#btnRejectPO').off('click').on('click', function() {
                            if (!currentPoId) return;
                            $('#formRejectPO')[0].reset();
                            $('#formRejectPO').attr('action', `/purchase-orders/${currentPoId}/reject-supplier`);
                            $('#modalApprovePurchaseOrder').modal('hide');
                            $('#modalRejectKeterangan').modal('show');
                        });
                    },
                    error: function() {
                        $('#approveLoading').html('<div class="alert alert-danger">Gagal memuat data Purchase Order</div>');
                    }
                });
            });
        });
    </script>
    @endpush