<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\ShippingDocument;
use App\Models\ShippingDocumentItem;
use App\Services\PdfExtractorService;
use App\Services\PurchaseOrderNotificationService;
use App\Services\ShippingDocumentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use setasign\Fpdi\Fpdi;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderNotificationService $poNotificationService,
        private readonly ShippingDocumentNotificationService $sjNotificationService,
    ) {}

    /**
     * Ownership rule:
     * - Supplier: restricted by supplier_id (already handled in several places)
     * - Admin/Purchasing: restricted to PO they uploaded (created_by = user id)
     * - Dept. Head: can access for approval workflow (not restricted)
     */
    private function ensurePurchasingOwnerAccess(PurchaseOrder $purchaseOrder): void
    {
        $user = auth()->user();
        $role = $user->role->name ?? null;

        if (in_array($role, ['Admin', 'Purchasing'], true)) {
            if ((int) $purchaseOrder->created_by !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk PO ini');
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Get suppliers for dropdown (only for Admin and Dept. Head)
        $suppliers = null;
        if (in_array($userRole, ['Admin', 'Dept. Head'])) {
            $suppliers = Supplier::all();
        }

        // Jika user adalah Supplier, hanya tampilkan PO yang supplier_id nya sesuai
        if ($userRole === 'Supplier' && $user->supplier_id) {
            Log::info('Supplier viewing PO - Supplier ID: ' . $user->supplier_id . ', User: ' . $user->email);

            // Supplier melihat PO yang approved (waiting for supplier action)
            $ongoingQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->where('supplier_id', $user->supplier_id)
                ->whereIn('status', ['approved', 'supplier_rejected'])
                ->latest();

            // Supplier melihat PO yang on_progress atau received (completed)
            $completedQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->where('supplier_id', $user->supplier_id)
                ->whereIn('status', ['on_progress', 'received'])
                ->latest();

            Log::info('Ongoing PO count for supplier: ' . $ongoingQuery->count());
            Log::info('Completed PO count for supplier: ' . $completedQuery->count());

            $pendingPurchaseOrders = collect();
            $ongoingPurchaseOrders = $ongoingQuery->paginate(10, ['*'], 'ongoing_page');
            $completedPurchaseOrders = $completedQuery->paginate(10, ['*'], 'completed_page');
            $rejectedPurchaseOrders = collect(); // Empty for supplier
        } else {
            // Admin dan Dept. Head: 4 tabs for approval workflow
            // Tab 1: Menunggu Approval - status = 'pending'
            $pendingQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->where('status', 'pending')
                ->latest();

            // Tab 2: Sedang Diproses - status = 'approved' atau 'supplier_rejected' atau 'on_progress'
            $ongoingQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->whereIn('status', ['approved', 'supplier_rejected', 'on_progress'])
                ->latest();

            // Tab 3: Selesai - status = 'received'
            $completedQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->where('status', 'received')
                ->latest();

            // Tab 4: Ditolak - status = 'rejected'
            $rejectedQuery = PurchaseOrder::with('supplier', 'createdBy')->withCount('items')
                ->where('status', 'rejected')
                ->latest();

            // Admin/Purchasing hanya boleh melihat PO yang mereka upload
            if (in_array($userRole, ['Admin', 'Purchasing'], true)) {
                $pendingQuery->where('created_by', $user->id);
                $ongoingQuery->where('created_by', $user->id);
                $completedQuery->where('created_by', $user->id);
                $rejectedQuery->where('created_by', $user->id);
            }

            $pendingPurchaseOrders = $pendingQuery->paginate(10, ['*'], 'pending_page');
            $ongoingPurchaseOrders = $ongoingQuery->paginate(10, ['*'], 'ongoing_page');
            $completedPurchaseOrders = $completedQuery->paginate(10, ['*'], 'completed_page');
            $rejectedPurchaseOrders = $rejectedQuery->paginate(10, ['*'], 'rejected_page');
        }

        return view('purchase-orders.index', compact('pendingPurchaseOrders', 'ongoingPurchaseOrders', 'completedPurchaseOrders', 'rejectedPurchaseOrders', 'userRole', 'suppliers'), ['tittle' => 'Purchase Order | Portal Supplier']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('purchase-orders.create', ['tittle' => 'Tambah Purchase Order | Portal Supplier']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        try {
            $rules = [
                'pdf_files' => 'required|array|min:1',
                'pdf_files.*' => 'required|file|mimes:pdf|max:10240', // Max 10MB per file
            ];

            $messages = [
                'pdf_files.required' => 'File PDF harus diisi',
                'pdf_files.array' => 'Format file tidak valid',
                'pdf_files.min' => 'Minimal 1 file PDF harus diupload',
                'pdf_files.*.required' => 'File PDF harus diisi',
                'pdf_files.*.file' => 'File harus berupa file yang valid',
                'pdf_files.*.mimes' => 'File harus berformat PDF',
                'pdf_files.*.max' => 'Ukuran file maksimal 10MB',
            ];

            // Validate supplier_id - wajib untuk Admin dan Dept. Head
            if (in_array($userRole, ['Admin', 'Dept. Head'])) {
                $rules['supplier_id'] = 'required|exists:suppliers,id';
                $messages['supplier_id.required'] = 'Supplier harus dipilih';
                $messages['supplier_id.exists'] = 'Supplier tidak valid';
            }

            $request->validate($rules, $messages);

            // CRITICAL: Check if supplier_id is provided for Admin/Dept Head
            if (in_array($userRole, ['Admin', 'Dept. Head'])) {
                if (empty($request->supplier_id)) {
                    throw new \Exception('Supplier harus dipilih untuk upload PO');
                }
            }
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        if (!$request->hasFile('pdf_files')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Tidak ada file yang diupload'], 422);
            }
            return redirect()->back()->with('error', 'Tidak ada file yang diupload');
        }

        $pdfExtractor = new PdfExtractorService();
        $successCount = 0;
        $errorMessages = [];
        $createdPurchaseOrders = [];

        DB::beginTransaction();
        try {
            foreach ($request->file('pdf_files') as $pdfFile) {
                try {
                    // Simpan file PDF
                    $filename = time() . '_' . Str::random(10) . '.' . $pdfFile->getClientOriginalExtension();
                    $path = $pdfFile->storeAs('purchase-orders', $filename, 'public');

                    // Extract data dari PDF
                    $extractedData = $pdfExtractor->extractPurchaseOrderData($path);

                    // Generate PO number jika tidak ditemukan dari PDF
                    if (empty($extractedData['po_number'])) {
                        $extractedData['po_number'] = 'PO-' . time() . '-' . Str::random(6);
                    }

                    // Ensure PO number is unique
                    $originalPoNumber = $extractedData['po_number'];
                    $counter = 1;
                    while (PurchaseOrder::where('po_number', $extractedData['po_number'])->exists()) {
                        $extractedData['po_number'] = $originalPoNumber . '-' . $counter;
                        $counter++;
                    }

                    // Determine supplier_id: use from request first (for Admin/Dept. Head)
                    $supplierId = null;

                    // Priority 1: From request (Admin/Dept. Head selection)
                    if (!empty($request->supplier_id)) {
                        $supplierId = $request->supplier_id;
                    }
                    // Priority 2: From extracted PDF data
                    elseif (!empty($extractedData['supplier_id'])) {
                        $supplierId = $extractedData['supplier_id'];
                    }
                    // Priority 3: From Supplier user's supplier_id
                    elseif ($userRole === 'Supplier' && $user->supplier_id) {
                        $supplierId = $user->supplier_id;
                    }

                    // CRITICAL: supplier_id HARUS ada
                    if (empty($supplierId)) {
                        throw new \Exception('Supplier tidak dapat ditentukan. Admin harus memilih supplier saat upload.');
                    }

                    // Verify supplier exists
                    $supplier = Supplier::find($supplierId);
                    if (!$supplier) {
                        throw new \Exception('Supplier dengan ID ' . $supplierId . ' tidak ditemukan');
                    }

                    // Set default values if not found
                    $poData = [
                        'po_number' => $extractedData['po_number'],
                        'date' => $extractedData['date'] ?? now(),
                        'item_count' => $extractedData['item_count'] ?? 0,
                        'delivery_date' => $extractedData['delivery_date'],
                        'currency' => $extractedData['currency'] ?? 'IDR',
                        'company_address' => $extractedData['company_address'],
                        'pdf_path' => $path,
                        'status' => 'pending',
                        'keterangan' => 'Menunggu Approval Dept. Head',
                        'supplier_id' => $supplierId,
                        'created_by' => $user->id,
                    ];

                    Log::info('Creating PO with supplier_id: ' . $supplierId . ' for supplier: ' . $supplier->nama);

                    // Create Purchase Order
                    $purchaseOrder = PurchaseOrder::create($poData);

                    // Create Purchase Order Items
                    if (!empty($extractedData['items']) && is_array($extractedData['items'])) {
                        foreach ($extractedData['items'] as $itemData) {
                            PurchaseOrderItem::create([
                                'purchase_order_id' => $purchaseOrder->id,
                                'item_number' => $itemData['item_number'] ?? null,
                                'material_code' => $itemData['material_code'],
                                'vendor_material' => $itemData['vendor_material'],
                                'description' => $itemData['description'] ?? '',
                                'quantity' => $itemData['quantity'] ?? 0,
                                'price_per_unit' => $itemData['price_per_unit'] ?? 0,
                                'net_value' => $itemData['net_value'] ?? 0,
                            ]);
                        }

                        // Update item count
                        $purchaseOrder->update(['item_count' => count($extractedData['items'])]);
                    }

                    if (in_array($userRole, ['Admin', 'Purchasing'], true)) {
                        $createdPurchaseOrders[] = $purchaseOrder;
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Error processing PDF file: ' . $pdfFile->getClientOriginalName() . ' - ' . $e->getMessage());
                    $errorMessages[] = $pdfFile->getClientOriginalName() . ': ' . $e->getMessage();

                    // Delete the uploaded file if processing failed
                    if (isset($path) && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            DB::commit();

            if (in_array($userRole, ['Admin', 'Purchasing'], true)) {
                foreach ($createdPurchaseOrders as $createdPo) {
                    $this->poNotificationService->notifyDeptHeadOnUpload($createdPo);
                }
            }

            $message = $successCount . ' file Purchase Order berhasil diupload';
            if (!empty($errorMessages)) {
                $message .= '. Beberapa file gagal: ' . implode(', ', $errorMessages);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => $message,
                    'success_count' => $successCount,
                    'errors' => $errorMessages
                ]);
            }

            return redirect()->route('purchase-orders.index')
                ->with('success', $message)
                ->with('errors', $errorMessages);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in store Purchase Order: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            $errorMessage = 'Gagal memproses file PDF: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $purchaseOrder->load('items', 'supplier', 'createdBy');
        $purchaseOrder->loadCount('items');

        if (request()->ajax()) {
            return response()->json(['purchase_order' => $purchaseOrder]);
        }

        return view('purchase-orders.show', compact('purchaseOrder'), ['tittle' => 'Detail Purchase Order | Portal Supplier']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        if (request()->ajax()) {
            $purchaseOrder->load('items', 'supplier', 'createdBy');
            return response()->json(['purchase_order' => $purchaseOrder]);
        }

        $purchaseOrder->load('items', 'supplier', 'createdBy');
        return view('purchase-orders.edit', compact('purchaseOrder'), ['tittle' => 'Edit Purchase Order | Portal Supplier']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $request->validate([
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number,' . $purchaseOrder->id,
            'date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'currency' => 'required|string|max:3',
        ], [
            'po_number.required' => 'PO Number harus diisi',
            'po_number.unique' => 'PO Number sudah ada',
            'date.required' => 'Tanggal harus diisi',
            'date.date' => 'Format tanggal tidak valid',
            'currency.required' => 'Currency harus diisi',
        ]);

        $purchaseOrder->update($request->only([
            'po_number',
            'date',
            'delivery_date',
            'currency',
            'company_address',
        ]));

        if ($request->ajax()) {
            return response()->json(['success' => 'Purchase Order berhasil diupdate']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        // Hapus file PDF jika ada
        if ($purchaseOrder->pdf_path && Storage::disk('public')->exists($purchaseOrder->pdf_path)) {
            Storage::disk('public')->delete($purchaseOrder->pdf_path);
        }

        $purchaseOrder->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Purchase Order berhasil dihapus']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dihapus');
    }

    /**
     * Download PDF file
     */
    public function download(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        if (!$purchaseOrder->pdf_path || !Storage::disk('public')->exists($purchaseOrder->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan');
        }

        // Jika status approved, tambahkan watermark
        if ($purchaseOrder->status === 'approved') {
            try {
                // Get the PDF file
                $pdfPath = Storage::disk('public')->path($purchaseOrder->pdf_path);

                // Create a new PDF instance with watermark
                $pdf = new Fpdi();

                // Get the number of pages
                $pageCount = $pdf->setSourceFile($pdfPath);

                // Process each page
                for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                    $templateId = $pdf->importPage($pageNum);
                    $pdf->addPage();
                    $pdf->useTemplate($templateId);

                    // Add watermark on bottom right
                    // Using lighter color to simulate transparency effect
                    $pdf->SetFont('Arial', 'B', 24);
                    $pdf->SetTextColor(144, 200, 154); // Lighter green for transparency effect

                    // Get page dimensions
                    $pageWidth = $pdf->GetPageWidth();
                    $pageHeight = $pdf->GetPageHeight();

                    // Position text at bottom right
                    $pdf->SetXY($pageWidth - 70, $pageHeight - 50);
                    $pdf->Write(10, 'APPROVED');

                    // Add second line
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY($pageWidth - 65, $pageHeight - 35);
                    $pdf->Write(5, 'by Dept Head');
                }

                // Output the PDF
                $filename = basename($purchaseOrder->pdf_path, '.pdf') . '_approved.pdf';
                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->Output('S');
                }, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            } catch (\Exception $e) {
                Log::error('Error adding watermark to PDF: ' . $e->getMessage());
                // Fall back to download original PDF if watermark fails
                return Storage::disk('public')->download($purchaseOrder->pdf_path);
            }
        }

        return Storage::disk('public')->download($purchaseOrder->pdf_path);
    }

    /**
     * Stream PDF for in-browser preview (iframe). Uses storage directly so preview works
     * even when the public/storage symlink is missing.
     */
    public function previewPdf(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        if (!$purchaseOrder->pdf_path || !Storage::disk('public')->exists($purchaseOrder->pdf_path)) {
            abort(404, 'File PDF tidak ditemukan');
        }

        $basename = basename($purchaseOrder->pdf_path);

        if ($purchaseOrder->status === 'approved') {
            try {
                $pdfPath = Storage::disk('public')->path($purchaseOrder->pdf_path);
                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($pdfPath);

                for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                    $templateId = $pdf->importPage($pageNum);
                    $pdf->addPage();
                    $pdf->useTemplate($templateId);

                    $pdf->SetFont('Arial', 'B', 24);
                    $pdf->SetTextColor(144, 200, 154);

                    $pageWidth = $pdf->GetPageWidth();
                    $pageHeight = $pdf->GetPageHeight();

                    $pdf->SetXY($pageWidth - 70, $pageHeight - 50);
                    $pdf->Write(10, 'APPROVED');

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY($pageWidth - 65, $pageHeight - 35);
                    $pdf->Write(5, 'by Dept Head');
                }

                $filename = basename($purchaseOrder->pdf_path, '.pdf').'_approved.pdf';

                return response($pdf->Output('S'), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$filename.'"',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                Log::error('Error building watermarked PDF for preview: '.$e->getMessage());

                return Storage::disk('public')->response($purchaseOrder->pdf_path, $basename, [
                    'Content-Disposition' => 'inline; filename="'.$basename.'"',
                ]);
            }
        }

        return Storage::disk('public')->response($purchaseOrder->pdf_path, $basename, [
            'Content-Disposition' => 'inline; filename="'.$basename.'"',
        ]);
    }

    /**
     * Approval method (existing)
     */
    public function approval()
    {
        return view('purchasing.purchaseOrderApproval', [
            'tittle' => 'Purchase Order Approval | Portal Supplier',
        ]);
    }

    /**
     * Approve Purchase Order
     */
    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Check if user is Dept. Head or Admin
        $userRole = auth()->user()->role->name ?? null;
        if (!in_array($userRole, ['Admin', 'Dept. Head'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk approve'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk approve');
        }

        // Check if status is pending
        if ($purchaseOrder->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini sudah diproses'], 422);
            }
            return redirect()->back()->with('error', 'PO ini sudah diproses');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'keterangan' => 'Menunggu Konfirmasi dari Supplier',
        ]);

        $this->poNotificationService->notifySupplierOnApprove($purchaseOrder->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'PO berhasil di-approve']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil di-approve');
    }

    /**
     * Reject Purchase Order
     */
    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'keterangan' => 'required|string',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
        ]);

        // Check if user is Dept. Head or Admin
        $userRole = auth()->user()->role->name ?? null;
        if (!in_array($userRole, ['Admin', 'Dept. Head'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk reject'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk reject');
        }

        // Check if status is pending
        if ($purchaseOrder->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini sudah diproses'], 422);
            }
            return redirect()->back()->with('error', 'PO ini sudah diproses');
        }

        $purchaseOrder->update([
            'status' => 'rejected',
            'keterangan' => $request->keterangan,
        ]);

        $this->poNotificationService->notifyPurchasingOnReject($purchaseOrder->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'PO berhasil di-reject']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil di-reject');
    }

    /**
     * Approve Purchase Order by Supplier
     */
    public function approveBySupplier(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Supplier
        if ($userRole !== 'Supplier') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk approve'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk approve');
        }

        // Check if user has supplier_id
        if (!$user->supplier_id) {
            Log::warning('User Supplier tidak memiliki supplier_id', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $userRole
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'User tidak terkait dengan supplier. Silakan hubungi admin untuk menghubungkan user dengan supplier.'
                ], 403);
            }
            return redirect()->back()->with('error', 'User tidak terkait dengan supplier. Silakan hubungi admin untuk menghubungkan user dengan supplier.');
        }

        // Check if PO belongs to this supplier
        if ($purchaseOrder->supplier_id !== $user->supplier_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini bukan untuk supplier Anda'], 403);
            }
            return redirect()->back()->with('error', 'PO ini bukan untuk supplier Anda');
        }

        // Check if status is approved (approved by Dept. Head)
        if ($purchaseOrder->status !== 'approved') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini belum di-approve oleh Dept. Head'], 422);
            }
            return redirect()->back()->with('error', 'PO ini belum di-approve oleh Dept. Head');
        }

        $purchaseOrder->update([
            'status' => 'on_progress',
            'keterangan' => 'pesanan sedang diproses',
        ]);

        $this->poNotificationService->notifyPurchasingOnSupplierApprove($purchaseOrder->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'PO berhasil dikonfirmasi']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil dikonfirmasi');
    }

    /**
     * Reject Purchase Order by Supplier
     */
    public function rejectBySupplier(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'keterangan' => 'required|string',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
        ]);

        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Supplier
        if ($userRole !== 'Supplier') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk reject'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk reject');
        }

        // Check if user has supplier_id
        if (!$user->supplier_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'User tidak terkait dengan supplier'], 403);
            }
            return redirect()->back()->with('error', 'User tidak terkait dengan supplier');
        }

        // Check if PO belongs to this supplier
        if ($purchaseOrder->supplier_id !== $user->supplier_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini bukan untuk supplier Anda'], 403);
            }
            return redirect()->back()->with('error', 'PO ini bukan untuk supplier Anda');
        }

        // Check if status is approved (approved by Dept. Head)
        if ($purchaseOrder->status !== 'approved') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini belum di-approve oleh Dept. Head'], 422);
            }
            return redirect()->back()->with('error', 'PO ini belum di-approve oleh Dept. Head');
        }

        $purchaseOrder->update([
            'status' => 'supplier_rejected',
            'keterangan' => $request->keterangan,
        ]);

        $this->poNotificationService->notifyPurchasingOnSupplierReject($purchaseOrder->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'PO berhasil di-reject']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil di-reject');
    }

    /**
     * Display Penerimaan Barang (PO dengan status on_progress dan received)
     */
    public function penerimaanBarang()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Admin, Dept. Head, or Supplier
        if (!in_array($userRole, ['Admin', 'Dept. Head', 'Supplier'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Ongoing query (on_progress)
        $ongoingQuery = PurchaseOrder::with('supplier', 'createdBy')
            ->withCount('items')
            ->where('status', 'on_progress');

        // Completed query (received)
        $completedQuery = PurchaseOrder::with('supplier', 'createdBy')
            ->withCount('items')
            ->where('status', 'received');

        // Jika user adalah Supplier, hanya tampilkan PO yang supplier_id nya sesuai
        if ($userRole === 'Supplier' && $user->supplier_id) {
            $ongoingQuery->where('supplier_id', $user->supplier_id);
            $completedQuery->where('supplier_id', $user->supplier_id);
        }

        // Admin/Purchasing hanya boleh melihat PO yang mereka upload
        if (in_array($userRole, ['Admin', 'Purchasing'], true)) {
            $ongoingQuery->where('created_by', $user->id);
            $completedQuery->where('created_by', $user->id);
        }

        $ongoingPurchaseOrders = $ongoingQuery->latest()->paginate(10, ['*'], 'ongoing_page');
        $completedPurchaseOrders = $completedQuery->latest()->paginate(10, ['*'], 'completed_page');

        return view('purchase-orders.penerimaan-barang', compact('ongoingPurchaseOrders', 'completedPurchaseOrders', 'userRole'), ['tittle' => 'Penerimaan Barang | Portal Supplier']);
    }

    /**
     * Print Surat Jalan
     */
    public function printSuratJalan(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        // Check if no_surat_jalan exists
        if (!$purchaseOrder->no_surat_jalan) {
            return redirect()->back()->with('error', 'No Surat Jalan tidak tersedia');
        }

        // Load items and supplier
        $purchaseOrder->load('items', 'supplier');

        return view('purchase-orders.print-surat-jalan', compact('purchaseOrder'));
    }

    /**
     * Confirm Penerimaan Barang (Status jadi received)
     */
    public function confirmReceived(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Admin
        if ($userRole !== 'Admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk konfirmasi penerimaan'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk konfirmasi penerimaan');
        }

        // Check if status is on_progress
        if ($purchaseOrder->status !== 'on_progress') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini tidak dalam status on_progress'], 422);
            }
            return redirect()->back()->with('error', 'PO ini tidak dalam status on_progress');
        }

        $purchaseOrder->update([
            'status' => 'received',
            'keterangan' => 'barang sudah diterima',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Barang berhasil dikonfirmasi diterima']);
        }

        return redirect()->route('purchase-orders.penerimaan-barang')->with('success', 'Barang berhasil dikonfirmasi diterima');
    }

    /**
     * Store new shipping document
     */
    public function storeShippingDocument(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check access
        if ($userRole === 'Supplier' && $user->supplier_id !== $purchaseOrder->supplier_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini bukan untuk supplier Anda'], 403);
            }
            return redirect()->back()->with('error', 'PO ini bukan untuk supplier Anda');
        }

        if (!in_array($userRole, ['Admin', 'Dept. Head', 'Supplier'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Validate request
        $request->validate([
            'no_surat_jalan' => 'required|string|max:255|unique:shipping_documents,no_surat_jalan,NULL,id,purchase_order_id,' . $purchaseOrder->id,
            'date' => 'required|date',
            'etd' => 'required|date',
            'eta' => 'required|date|after_or_equal:etd',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_shipped' => 'required|integer|min:1',
        ], [
            'no_surat_jalan.required' => 'No Surat Jalan harus diisi',
            'no_surat_jalan.unique' => 'No Surat Jalan sudah ada untuk PO ini',
            'date.required' => 'Tanggal harus diisi',
            'etd.required' => 'ETD harus diisi',
            'eta.required' => 'ETA harus diisi',
            'eta.after_or_equal' => 'ETA tidak boleh lebih awal dari ETD',
            'items.required' => 'Minimal 1 item harus diisi',
            'items.*.quantity_shipped.required' => 'Jumlah barang yang dikirim harus diisi',
            'items.*.quantity_shipped.min' => 'Jumlah barang yang dikirim minimal 1',
        ]);

        DB::beginTransaction();
        try {
            // Create shipping document
            $shippingDoc = ShippingDocument::create([
                'purchase_order_id' => $purchaseOrder->id,
                'no_surat_jalan' => $request->no_surat_jalan,
                'date' => $request->date,
                'etd' => $request->etd,
                'eta' => $request->eta,
                'status' => 'draft',
                'notes' => $request->notes ?? null,
            ]);

            // Create shipping document items with validation
            $purchaseOrder->load('items');
            foreach ($request->items as $itemData) {
                $poItem = $purchaseOrder->items->find($itemData['purchase_order_item_id']);

                if (!$poItem) {
                    throw new \Exception('Item tidak ditemukan dalam PO');
                }

                $quantityToShip = $itemData['quantity_shipped'];
                $quantityRemaining = $poItem->quantity - ($poItem->quantity_shipped ?? 0);

                if ($quantityToShip > $quantityRemaining) {
                    throw new \Exception("Kuantitas pengiriman untuk item {$poItem->material_code} ({$quantityToShip}) melebihi sisa yang tersedia ({$quantityRemaining})");
                }

                // Create shipping document item
                $shipItem = ShippingDocumentItem::create([
                    'shipping_document_id' => $shippingDoc->id,
                    'purchase_order_item_id' => $poItem->id,
                    'quantity_shipped' => $quantityToShip,
                ]);

                // Validate the item
                if (!$shipItem->isQuantityValid()) {
                    throw new \Exception($shipItem->getQuantityValidationError());
                }
            }

            // Confirm dan update fulfillment untuk draft shipping document
            if ($shippingDoc->canBeConfirmed()) {
                $shippingDoc->confirm();
            }

            // IMPORTANT: Jangan ubah status PO ke received di sini
            // Admin harus approve SJ terlebih dahulu melalui ASN page
            // Hanya update fulfillment status untuk tracking progress
            $purchaseOrder->refresh()->load('items');
            $purchaseOrder->updateFulfillmentStatus();

            DB::commit();

            if ($userRole === 'Supplier') {
                $this->sjNotificationService->notifyPurchasingOnCreated(
                    $purchaseOrder->fresh(),
                    $shippingDoc->fresh()
                );
            }

            Log::info('Shipping document created successfully', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDoc->id,
                'fulfillment_status' => $purchaseOrder->fulfillment_status,
                'fulfillment_percentage' => $purchaseOrder->fulfillment_percentage,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil dibuat',
                    'fulfillment_status' => $purchaseOrder->fulfillment_status,
                    'fulfillment_percentage' => $purchaseOrder->fulfillment_percentage,
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing shipping document: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show shipping documents for a purchase order
     */
    public function showShippingDocuments(PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if supplier can only access their own PO
        if ($userRole === 'Supplier' && $user->supplier_id !== $purchaseOrder->supplier_id) {
            return redirect()->back()->with('error', 'PO ini bukan untuk supplier Anda');
        }

        $purchaseOrder->load('items', 'supplier', 'shippingDocuments.items.purchaseOrderItem');

        return view('purchase-orders.shipping-documents', compact('purchaseOrder', 'userRole'), [
            'tittle' => 'Surat Jalan | Portal Supplier'
        ]);
    }

    /**
     * Print specific shipping document
     */
    public function printShippingDocument(PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        $shippingDocument->load('items.purchaseOrderItem', 'purchaseOrder.supplier');

        return view('purchase-orders.print-shipping-document', compact('shippingDocument', 'purchaseOrder'));
    }

    /**
     * Delete shipping document
     */
    public function deleteShippingDocument(Request $request, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Surat jalan tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        // Check if document is still in draft status
        if ($shippingDocument->status !== 'draft') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya dokumen dengan status draft yang dapat dihapus'], 422);
            }
            return redirect()->back()->with('error', 'Hanya dokumen dengan status draft yang dapat dihapus');
        }

        DB::beginTransaction();
        try {
            // Revert quantity_shipped for items in this document
            foreach ($shippingDocument->items as $item) {
                // Just reduce the shipped quantity from the draft doc, don't actually decrement
                // since it wasn't confirmed
                $item->delete();
            }

            // Recalculate fulfillment for all items in this PO
            $purchaseOrder->refresh()->load('items');
            foreach ($purchaseOrder->items as $item) {
                $item->recalculateQuantityShipped();
            }

            // Update PO fulfillment status
            $purchaseOrder->updateFulfillmentStatus();

            // Revert PO status if needed
            if (
                $purchaseOrder->status === 'received' &&
                in_array($purchaseOrder->keterangan, ['semua barang telah dikirim', 'semua barang telah diterima dan di-approve oleh Admin'])
            ) {
                if (!$purchaseOrder->isFullyShipped()) {
                    $purchaseOrder->update([
                        'status' => 'on_progress',
                        'fulfillment_status' => 'partial',
                        'keterangan' => 'pesanan sedang diproses',
                    ]);
                }
            }

            $shippingDocument->delete();
            DB::commit();

            Log::info('Shipping document deleted', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDocument->id,
                'fulfillment_status' => $purchaseOrder->fulfillment_status,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil dihapus',
                    'fulfillment_status' => $purchaseOrder->fulfillment_status,
                    'fulfillment_percentage' => $purchaseOrder->fulfillment_percentage,
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting shipping document: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Approve shipping document and update PO status if all items are shipped
     */
    public function approveShippingDocument(Request $request, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Only Admin/Purchasing/Dept. Head can approve SJ
        if (!in_array($userRole, ['Admin', 'Purchasing', 'Dept. Head'], true)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Surat jalan tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        // Check if document is confirmed
        if ($shippingDocument->status !== 'confirmed') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya surat jalan yang sudah confirmed yang dapat di-approve'], 422);
            }
            return redirect()->back()->with('error', 'Hanya surat jalan yang sudah confirmed yang dapat di-approve');
        }

        DB::beginTransaction();
        try {
            // Mark SJ as approved
            $shippingDocument->markAsApproved($user->id);

            Log::info('Shipping document approved', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDocument->id,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            DB::commit();

            $this->sjNotificationService->notifySupplierOnApproved(
                $purchaseOrder->fresh(),
                $shippingDocument->fresh()
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil di-approve',
                    'po_status' => $purchaseOrder->status,
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil di-approve');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving shipping document: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal approve surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Reject shipping document
     */
    public function rejectShippingDocument(Request $request, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Only Admin/Purchasing/Dept. Head can reject SJ
        if (!in_array($userRole, ['Admin', 'Purchasing', 'Dept. Head'], true)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Surat jalan tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        // Check if document is confirmed
        if ($shippingDocument->status !== 'confirmed') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya surat jalan yang confirmed yang dapat di-reject'], 422);
            }
            return redirect()->back()->with('error', 'Hanya surat jalan yang confirmed yang dapat di-reject');
        }

        DB::beginTransaction();
        try {
            // ROLLBACK: Decrement quantity_shipped untuk setiap item di SJ ini
            // Load SJ items with PO items
            $shippingDocument->load('items.purchaseOrderItem');

            foreach ($shippingDocument->items as $sjItem) {
                $poItem = $sjItem->purchaseOrderItem;
                if ($poItem) {
                    // Decrement quantity_shipped
                    $poItem->decrement('quantity_shipped', $sjItem->quantity_shipped);

                    Log::info('Rolled back quantity for PO item', [
                        'po_item_id' => $poItem->id,
                        'quantity_rolled_back' => $sjItem->quantity_shipped,
                        'new_quantity_shipped' => $poItem->quantity_shipped,
                    ]);
                }
            }

            // Mark SJ as rejected
            $shippingDocument->update(['status' => 'rejected']);

            // Recalculate fulfillment status untuk PO
            $purchaseOrder->refresh()->load('items');
            $purchaseOrder->updateFulfillmentStatus();

            DB::commit();

            $this->sjNotificationService->notifySupplierOnRejected(
                $purchaseOrder->fresh(),
                $shippingDocument->fresh()
            );

            Log::info('Shipping document rejected with rollback', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDocument->id,
                'fulfillment_status' => $purchaseOrder->fulfillment_status,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil di-reject (qty dikembalikan)',
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil di-reject');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting shipping document: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal reject surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Revise shipping document (return to draft for supplier to edit)
     */
    public function reviseShippingDocument(Request $request, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Only Admin/Purchasing/Dept. Head can revise SJ
        if (!in_array($userRole, ['Admin', 'Purchasing', 'Dept. Head'], true)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Surat jalan tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        // Check if document is confirmed
        if ($shippingDocument->status !== 'confirmed') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya surat jalan yang confirmed yang dapat di-revise'], 422);
            }
            return redirect()->back()->with('error', 'Hanya surat jalan yang confirmed yang dapat di-revise');
        }

        DB::beginTransaction();
        try {
            // ROLLBACK: Decrement quantity_shipped untuk setiap item di SJ ini
            // Load SJ items with PO items
            $shippingDocument->load('items.purchaseOrderItem');

            foreach ($shippingDocument->items as $sjItem) {
                $poItem = $sjItem->purchaseOrderItem;
                if ($poItem) {
                    // Decrement quantity_shipped
                    $poItem->decrement('quantity_shipped', $sjItem->quantity_shipped);

                    Log::info('Rolled back quantity for revise', [
                        'po_item_id' => $poItem->id,
                        'quantity_rolled_back' => $sjItem->quantity_shipped,
                        'new_quantity_shipped' => $poItem->quantity_shipped,
                    ]);
                }
            }

            // Return SJ to draft status so supplier can edit
            $shippingDocument->update(['status' => 'draft']);

            // Recalculate fulfillment status untuk PO
            $purchaseOrder->refresh()->load('items');
            $purchaseOrder->updateFulfillmentStatus();

            DB::commit();

            $this->sjNotificationService->notifySupplierOnRevised(
                $purchaseOrder->fresh(),
                $shippingDocument->fresh()
            );

            Log::info('Shipping document revised (returned to draft) with rollback', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDocument->id,
                'fulfillment_status' => $purchaseOrder->fulfillment_status,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil di-revise (qty dikembalikan ke Supplier untuk di-edit)',
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil di-revise');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error revising shipping document: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal revise surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Auto-generate shipping document with all remaining quantities
     */
    public function autoGenerateShippingDocument(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check access
        if ($userRole === 'Supplier' && $user->supplier_id !== $purchaseOrder->supplier_id) {
            return response()->json(['error' => 'PO ini bukan untuk supplier Anda'], 403);
        }

        if (!in_array($userRole, ['Admin', 'Dept. Head', 'Supplier'])) {
            return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
        }

        // Check if PO is on_progress
        if ($purchaseOrder->status !== 'on_progress') {
            return response()->json(['error' => 'PO harus dalam status on_progress'], 422);
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items');

            // Check if there are items with remaining quantity
            $itemsToShip = $purchaseOrder->items->filter(function ($item) {
                return $item->quantity_remaining > 0;
            });

            if ($itemsToShip->isEmpty()) {
                DB::rollBack();
                return response()->json(['error' => 'Semua barang sudah dikirim'], 422);
            }

            // Generate auto surat jalan number
            $lastSuratJalan = ShippingDocument::where('purchase_order_id', $purchaseOrder->id)
                ->orderBy('id', 'desc')
                ->first();

            $suratJalanNumber = 'SJ-' . $purchaseOrder->po_number . '-' . ($lastSuratJalan ? intval(substr($lastSuratJalan->no_surat_jalan, -3)) + 1 : 1);

            // Ensure uniqueness
            $baseNumber = $suratJalanNumber;
            $counter = 1;
            while (ShippingDocument::where('no_surat_jalan', $suratJalanNumber)->exists()) {
                $suratJalanNumber = $baseNumber . '-' . $counter;
                $counter++;
            }

            // Create shipping document with auto-generated number
            $shippingDoc = ShippingDocument::create([
                'purchase_order_id' => $purchaseOrder->id,
                'no_surat_jalan' => $suratJalanNumber,
                'date' => now(),
                'etd' => $purchaseOrder->etd ?? now(),
                'eta' => $purchaseOrder->eta ?? now()->addDays(7),
                'status' => 'draft',
                'notes' => 'Auto-generated on ' . now()->format('Y-m-d H:i:s'),
            ]);

            // Add all items with remaining quantity
            foreach ($itemsToShip as $poItem) {
                $quantityToShip = $poItem->quantity_remaining;

                // Create shipping document item
                $shipItem = ShippingDocumentItem::create([
                    'shipping_document_id' => $shippingDoc->id,
                    'purchase_order_item_id' => $poItem->id,
                    'quantity_shipped' => $quantityToShip,
                ]);

                // Validate the item
                if (!$shipItem->isQuantityValid()) {
                    throw new \Exception($shipItem->getQuantityValidationError());
                }
            }

            // Confirm dan update fulfillment
            if ($shippingDoc->canBeConfirmed()) {
                $shippingDoc->confirm();
            }

            // Check if all items are shipped and update PO status
            $purchaseOrder->refresh()->load('items');

            // IMPORTANT: Do NOT automatically mark PO as received when all items are shipped
            // Admin must approve all shipping documents first through the ASN page
            // Only update fulfillment status for tracking progress
            $purchaseOrder->updateFulfillmentStatus();

            DB::commit();

            if ($userRole === 'Supplier') {
                $this->sjNotificationService->notifyPurchasingOnCreated(
                    $purchaseOrder->fresh(),
                    $shippingDoc->fresh()
                );
            }

            Log::info('Auto-generated shipping document', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDoc->id,
                'no_surat_jalan' => $suratJalanNumber,
                'items_count' => $shippingDoc->items()->count(),
            ]);

            return response()->json([
                'success' => 'Surat jalan berhasil dibuat otomatis',
                'surat_jalan_number' => $suratJalanNumber,
                'po_status' => $purchaseOrder->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error auto-generating shipping document: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Mark approved shipping document as received
     */
    public function markShippingDocumentAsReceived(Request $request, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument)
    {
        $this->ensurePurchasingOwnerAccess($purchaseOrder);
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Only Admin and Dept. Head can mark as received
        if (!in_array($userRole, ['Admin', 'Purchasing', 'Dept. Head'], true)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Verify shipping document belongs to purchase order
        if ($shippingDocument->purchase_order_id !== $purchaseOrder->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Surat jalan tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Surat jalan tidak ditemukan');
        }

        // Check if document is approved
        if ($shippingDocument->status !== 'approved') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya surat jalan yang sudah approved yang dapat ditandai sebagai received'], 422);
            }
            return redirect()->back()->with('error', 'Hanya surat jalan yang sudah approved yang dapat ditandai sebagai received');
        }

        DB::beginTransaction();
        try {
            // Mark SJ as received
            $shippingDocument->update(['status' => 'received']);

            // Check if all SJ for this PO are received
            $purchaseOrder->load('shippingDocuments');
            $allReceived = $purchaseOrder->shippingDocuments->every(function ($doc) {
                return $doc->status === 'received';
            });

            // Mark PO as received if all SJ are received and fully shipped
            if ($allReceived && $purchaseOrder->isFullyShipped()) {
                $purchaseOrder->update([
                    'status' => 'received',
                    'fulfillment_status' => 'complete',
                    'keterangan' => 'semua barang telah diterima dan di-approve oleh Admin',
                ]);

                Log::info('PO marked as received after all SJ received', [
                    'po_id' => $purchaseOrder->id,
                    'shipping_doc_id' => $shippingDocument->id,
                ]);
            }

            DB::commit();

            Log::info('Shipping document marked as received', [
                'po_id' => $purchaseOrder->id,
                'shipping_doc_id' => $shippingDocument->id,
                'marked_by' => $user->id,
                'po_status' => $purchaseOrder->status,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Surat jalan berhasil ditandai sebagai received',
                    'po_status' => $purchaseOrder->status,
                ]);
            }

            return redirect()->back()->with('success', 'Surat jalan berhasil ditandai sebagai received');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking shipping document as received: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal menandai surat jalan sebagai received: ' . $e->getMessage());
        }
    }
}
