<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Services\PdfExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;
        
        $query = PurchaseOrder::with('supplier')->withCount('items')->latest();
        
        // Jika user adalah Supplier, hanya tampilkan PO yang supplier_id nya sesuai
        if ($userRole === 'Supplier' && $user->supplier_id) {
            $query->where('supplier_id', $user->supplier_id);
            // Supplier melihat semua status (tidak filter on_progress)
        } else {
            // Admin dan Dept. Head: exclude status on_progress dan received (masuk ke menu Penerimaan Barang)
            $query->whereNotIn('status', ['on_progress', 'received']);
        }
        
        $purchaseOrders = $query->paginate(10);
        
        // Get suppliers for dropdown (only for Admin and Dept. Head)
        $suppliers = null;
        if (in_array($userRole, ['Admin', 'Dept. Head'])) {
            $suppliers = Supplier::all();
        }
        
        return view('purchase-orders.index', compact('purchaseOrders', 'userRole', 'suppliers'), ['tittle' => 'Purchase Order | Portal Supplier']);
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
                    
                    // Determine supplier_id: use from request first, then from extracted data
                    $supplierId = $request->supplier_id ?? $extractedData['supplier_id'] ?? null;
                    
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
                        'supplier_id' => $supplierId, // Use selected supplier or auto-detected supplier
                    ];
                    
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
            
            $message = $successCount . ' file PDF berhasil diupload dan diextract';
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
        $purchaseOrder->load('items', 'supplier');
        
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
        if (request()->ajax()) {
            $purchaseOrder->load('items', 'supplier');
            return response()->json(['purchase_order' => $purchaseOrder]);
        }

        $purchaseOrder->load('items', 'supplier');
        return view('purchase-orders.edit', compact('purchaseOrder'), ['tittle' => 'Edit Purchase Order | Portal Supplier']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
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
        if (!$purchaseOrder->pdf_path || !Storage::disk('public')->exists($purchaseOrder->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan');
        }

        return Storage::disk('public')->download($purchaseOrder->pdf_path);
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
        // Check if user is Dept. Head
        $userRole = auth()->user()->role->name ?? null;
        if ($userRole !== 'Dept. Head') {
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

        // Check if user is Dept. Head
        $userRole = auth()->user()->role->name ?? null;
        if ($userRole !== 'Dept. Head') {
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

        // Validate ETD, ETA, and No Surat Jalan
        $request->validate([
            'etd' => 'required|date',
            'eta' => 'required|date|after_or_equal:etd',
            'no_surat_jalan' => 'required|string|max:255',
        ], [
            'etd.required' => 'ETD (Estimated Time Delivery) harus diisi',
            'etd.date' => 'ETD harus berupa tanggal yang valid',
            'eta.required' => 'ETA (Estimated Time Arrive) harus diisi',
            'eta.date' => 'ETA harus berupa tanggal yang valid',
            'eta.after_or_equal' => 'ETA harus sama atau setelah ETD',
            'no_surat_jalan.required' => 'No Surat Jalan harus diisi',
            'no_surat_jalan.max' => 'No Surat Jalan maksimal 255 karakter',
        ]);

        $purchaseOrder->update([
            'status' => 'on_progress',
            'keterangan' => 'pesanan sedang diproses',
            'etd' => $request->etd,
            'eta' => $request->eta,
            'no_surat_jalan' => $request->no_surat_jalan,
        ]);

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

        if ($request->ajax()) {
            return response()->json(['success' => 'PO berhasil di-reject']);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil di-reject');
    }

    /**
     * Display Penerimaan Barang (PO dengan status on_progress)
     */
    public function penerimaanBarang()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;
        
        // Check if user is Admin, Dept. Head, or Supplier
        if (!in_array($userRole, ['Admin', 'Dept. Head', 'Supplier'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }
        
        $query = PurchaseOrder::with('supplier')
            ->withCount('items')
            ->whereIn('status', ['on_progress', 'received']);
        
        // Jika user adalah Supplier, hanya tampilkan PO yang supplier_id nya sesuai
        if ($userRole === 'Supplier' && $user->supplier_id) {
            $query->where('supplier_id', $user->supplier_id);
        }
        
        $purchaseOrders = $query->latest()->paginate(10);
        
        return view('purchase-orders.penerimaan-barang', compact('purchaseOrders', 'userRole'), ['tittle' => 'Penerimaan Barang | Portal Supplier']);
    }

    /**
     * Print Surat Jalan
     */
    public function printSuratJalan(PurchaseOrder $purchaseOrder)
    {
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
}
