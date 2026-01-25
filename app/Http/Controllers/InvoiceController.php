<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Query untuk invoice yang pending/revised (belum selesai)
        if ($userRole === 'Admin') {
            $ongoingInvoices = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['pending', 'revised'])
                ->latest()
                ->paginate(10, ['*'], 'ongoing_page');

            $completedInvoices = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->paginate(10, ['*'], 'completed_page');
        } else {
            // Untuk Supplier: tampilkan invoice mereka
            $ongoingQuery = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['pending', 'revised'])
                ->latest();

            $completedQuery = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['approved', 'rejected'])
                ->latest();

            if ($userRole === 'Supplier' && $user->supplier_id) {
                $ongoingQuery->whereHas('purchaseOrder', function ($q) use ($user) {
                    $q->where('supplier_id', $user->supplier_id);
                });

                $completedQuery->whereHas('purchaseOrder', function ($q) use ($user) {
                    $q->where('supplier_id', $user->supplier_id);
                });
            }

            $ongoingInvoices = $ongoingQuery->paginate(10, ['*'], 'ongoing_page');
            $completedInvoices = $completedQuery->paginate(10, ['*'], 'completed_page');
        }

        // Untuk Supplier: ambil juga PO received yang belum ada invoice
        $purchaseOrdersWithoutInvoice = collect();
        if ($userRole === 'Supplier' && $user->supplier_id) {
            $purchaseOrdersWithoutInvoice = PurchaseOrder::with('supplier', 'items')
                ->where('status', 'received')
                ->where('supplier_id', $user->supplier_id)
                ->whereDoesntHave('invoice')
                ->latest()
                ->get();
        }

        return view('invoices.index', compact('ongoingInvoices', 'completedInvoices', 'userRole', 'purchaseOrdersWithoutInvoice'), ['tittle' => 'Penagihan Invoice | Portal Supplier']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Supplier
        if ($userRole !== 'Supplier') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
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

        // Check if PO status is received
        if ($purchaseOrder->status !== 'received') {
            if ($request->ajax()) {
                return response()->json(['error' => 'PO ini belum diterima'], 422);
            }
            return redirect()->back()->with('error', 'PO ini belum diterima');
        }

        // Check if invoice already exists and not in revised status
        if ($purchaseOrder->invoice && $purchaseOrder->invoice->status !== 'revised') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invoice untuk PO ini sudah ada'], 422);
            }
            return redirect()->back()->with('error', 'Invoice untuk PO ini sudah ada');
        }

        $request->validate([
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_jalan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'faktur_pajak_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'invoice_file.required' => 'File invoice harus diupload',
            'invoice_file.file' => 'File invoice harus berupa file yang valid',
            'invoice_file.mimes' => 'File invoice harus berformat PDF, JPG, atau PNG',
            'invoice_file.max' => 'Ukuran file invoice maksimal 10MB',
            'surat_jalan_file.required' => 'File surat jalan harus diupload',
            'surat_jalan_file.file' => 'File surat jalan harus berupa file yang valid',
            'surat_jalan_file.mimes' => 'File surat jalan harus berformat PDF, JPG, atau PNG',
            'surat_jalan_file.max' => 'Ukuran file surat jalan maksimal 10MB',
            'faktur_pajak_file.required' => 'File faktur pajak harus diupload',
            'faktur_pajak_file.file' => 'File faktur pajak harus berupa file yang valid',
            'faktur_pajak_file.mimes' => 'File faktur pajak harus berformat PDF, JPG, atau PNG',
            'faktur_pajak_file.max' => 'Ukuran file faktur pajak maksimal 10MB',
        ]);

        DB::beginTransaction();
        try {
            // Upload invoice file
            $invoiceFile = $request->file('invoice_file');
            $invoiceFilename = time() . '_' . Str::random(10) . '_invoice.' . $invoiceFile->getClientOriginalExtension();
            $invoicePath = $invoiceFile->storeAs('invoices', $invoiceFilename, 'public');

            // Upload surat jalan file
            $suratJalanFile = $request->file('surat_jalan_file');
            $suratJalanFilename = time() . '_' . Str::random(10) . '_surat_jalan.' . $suratJalanFile->getClientOriginalExtension();
            $suratJalanPath = $suratJalanFile->storeAs('invoices', $suratJalanFilename, 'public');

            // Upload faktur pajak file
            $fakturPajakFile = $request->file('faktur_pajak_file');
            $fakturPajakFilename = time() . '_' . Str::random(10) . '_faktur_pajak.' . $fakturPajakFile->getClientOriginalExtension();
            $fakturPajakPath = $fakturPajakFile->storeAs('invoices', $fakturPajakFilename, 'public');

            // Create or update invoice
            if ($purchaseOrder->invoice && $purchaseOrder->invoice->status === 'revised') {
                // Update existing invoice (revision)
                $invoice = $purchaseOrder->invoice;

                // Delete old files
                if ($invoice->invoice_file && Storage::disk('public')->exists($invoice->invoice_file)) {
                    Storage::disk('public')->delete($invoice->invoice_file);
                }
                if ($invoice->surat_jalan_file && Storage::disk('public')->exists($invoice->surat_jalan_file)) {
                    Storage::disk('public')->delete($invoice->surat_jalan_file);
                }
                if ($invoice->faktur_pajak_file && Storage::disk('public')->exists($invoice->faktur_pajak_file)) {
                    Storage::disk('public')->delete($invoice->faktur_pajak_file);
                }

                $invoice->update([
                    'invoice_file' => $invoicePath,
                    'surat_jalan_file' => $suratJalanPath,
                    'faktur_pajak_file' => $fakturPajakPath,
                    'status' => 'pending',
                    'catatan_revisi' => null,
                ]);
            } else {
                // Create new invoice
                $invoice = Invoice::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'invoice_file' => $invoicePath,
                    'surat_jalan_file' => $suratJalanPath,
                    'faktur_pajak_file' => $fakturPajakPath,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => 'Invoice berhasil diupload']);
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded files if transaction fails
            if (isset($invoicePath) && Storage::disk('public')->exists($invoicePath)) {
                Storage::disk('public')->delete($invoicePath);
            }
            if (isset($suratJalanPath) && Storage::disk('public')->exists($suratJalanPath)) {
                Storage::disk('public')->delete($suratJalanPath);
            }
            if (isset($fakturPajakPath) && Storage::disk('public')->exists($fakturPajakPath)) {
                Storage::disk('public')->delete($fakturPajakPath);
            }

            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal upload invoice: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal upload invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('purchaseOrder.supplier', 'purchaseOrder.items');

        if (request()->ajax()) {
            return response()->json(['invoice' => $invoice]);
        }

        return view('invoices.show', compact('invoice'), ['tittle' => 'Detail Invoice | Portal Supplier']);
    }

    /**
     * Update invoice files (for revision)
     */
    public function update(Request $request, Invoice $invoice)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Supplier
        if ($userRole !== 'Supplier') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses');
        }

        // Check if status is revised
        if ($invoice->status !== 'revised') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invoice ini tidak dalam status revised'], 422);
            }
            return redirect()->back()->with('error', 'Invoice ini tidak dalam status revised');
        }

        $request->validate([
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_jalan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'faktur_pajak_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'invoice_file.required' => 'File invoice harus diupload',
            'invoice_file.file' => 'File invoice harus berupa file yang valid',
            'invoice_file.mimes' => 'File invoice harus berformat PDF, JPG, atau PNG',
            'invoice_file.max' => 'Ukuran file invoice maksimal 10MB',
            'surat_jalan_file.required' => 'File surat jalan harus diupload',
            'surat_jalan_file.file' => 'File surat jalan harus berupa file yang valid',
            'surat_jalan_file.mimes' => 'File surat jalan harus berformat PDF, JPG, atau PNG',
            'surat_jalan_file.max' => 'Ukuran file surat jalan maksimal 10MB',
            'faktur_pajak_file.required' => 'File faktur pajak harus diupload',
            'faktur_pajak_file.file' => 'File faktur pajak harus berupa file yang valid',
            'faktur_pajak_file.mimes' => 'File faktur pajak harus berformat PDF, JPG, atau PNG',
            'faktur_pajak_file.max' => 'Ukuran file faktur pajak maksimal 10MB',
        ]);

        DB::beginTransaction();
        try {
            $oldInvoicePath = $invoice->invoice_file;
            $oldSuratJalanPath = $invoice->surat_jalan_file;
            $oldFakturPajakPath = $invoice->faktur_pajak_file;

            // Upload invoice file
            $invoiceFile = $request->file('invoice_file');
            $invoiceFilename = time() . '_' . Str::random(10) . '_invoice.' . $invoiceFile->getClientOriginalExtension();
            $invoicePath = $invoiceFile->storeAs('invoices', $invoiceFilename, 'public');

            // Upload surat jalan file
            $suratJalanFile = $request->file('surat_jalan_file');
            $suratJalanFilename = time() . '_' . Str::random(10) . '_surat_jalan.' . $suratJalanFile->getClientOriginalExtension();
            $suratJalanPath = $suratJalanFile->storeAs('invoices', $suratJalanFilename, 'public');

            // Upload faktur pajak file
            $fakturPajakFile = $request->file('faktur_pajak_file');
            $fakturPajakFilename = time() . '_' . Str::random(10) . '_faktur_pajak.' . $fakturPajakFile->getClientOriginalExtension();
            $fakturPajakPath = $fakturPajakFile->storeAs('invoices', $fakturPajakFilename, 'public');

            // Update invoice
            $invoice->update([
                'invoice_file' => $invoicePath,
                'surat_jalan_file' => $suratJalanPath,
                'faktur_pajak_file' => $fakturPajakPath,
                'status' => 'pending',
                'catatan_revisi' => null,
            ]);

            // Delete old files
            if ($oldInvoicePath && Storage::disk('public')->exists($oldInvoicePath)) {
                Storage::disk('public')->delete($oldInvoicePath);
            }
            if ($oldSuratJalanPath && Storage::disk('public')->exists($oldSuratJalanPath)) {
                Storage::disk('public')->delete($oldSuratJalanPath);
            }
            if ($oldFakturPajakPath && Storage::disk('public')->exists($oldFakturPajakPath)) {
                Storage::disk('public')->delete($oldFakturPajakPath);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => 'Invoice berhasil direvisi']);
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice berhasil direvisi');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal revisi invoice: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal revisi invoice: ' . $e->getMessage());
        }
    }

    /**
     * Approve invoice (Admin only)
     */
    public function approve(Request $request, Invoice $invoice)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Admin
        if ($userRole !== 'Admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk approve'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk approve');
        }

        // Check if status is pending or revised
        if (!in_array($invoice->status, ['pending', 'revised'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invoice ini sudah diproses'], 422);
            }
            return redirect()->back()->with('error', 'Invoice ini sudah diproses');
        }

        $invoice->update([
            'status' => 'approved',
            'catatan_revisi' => null,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Invoice berhasil di-approve']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-approve');
    }

    /**
     * Reject invoice (Admin only)
     */
    public function reject(Request $request, Invoice $invoice)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Admin
        if ($userRole !== 'Admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk reject'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk reject');
        }

        // Check if status is pending or revised
        if (!in_array($invoice->status, ['pending', 'revised'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invoice ini sudah diproses'], 422);
            }
            return redirect()->back()->with('error', 'Invoice ini sudah diproses');
        }

        $request->validate([
            'keterangan' => 'required|string',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
        ]);

        $invoice->update([
            'status' => 'rejected',
            'catatan_revisi' => $request->keterangan,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Invoice berhasil di-reject']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-reject');
    }

    /**
     * Revise invoice (Admin only)
     */
    public function revise(Request $request, Invoice $invoice)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        // Check if user is Admin
        if ($userRole !== 'Admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki hak akses untuk revise'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk revise');
        }

        // Check if status is pending or revised
        if (!in_array($invoice->status, ['pending', 'revised'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invoice ini sudah diproses'], 422);
            }
            return redirect()->back()->with('error', 'Invoice ini sudah diproses');
        }

        $request->validate([
            'catatan_revisi' => 'required|string',
        ], [
            'catatan_revisi.required' => 'Catatan revisi harus diisi',
        ]);

        $invoice->update([
            'status' => 'revised',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Invoice berhasil di-revise']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-revise');
    }

    /**
     * Download invoice file
     */
    public function downloadInvoice(Invoice $invoice)
    {
        if (!$invoice->invoice_file || !Storage::disk('public')->exists($invoice->invoice_file)) {
            return redirect()->back()->with('error', 'File invoice tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($invoice->invoice_file);
        $mimeType = Storage::disk('public')->mimeType($invoice->invoice_file);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($invoice->invoice_file) . '"'
        ]);
    }

    /**
     * Download surat jalan file
     */
    public function downloadSuratJalan(Invoice $invoice)
    {
        if (!$invoice->surat_jalan_file || !Storage::disk('public')->exists($invoice->surat_jalan_file)) {
            return redirect()->back()->with('error', 'File surat jalan tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($invoice->surat_jalan_file);
        $mimeType = Storage::disk('public')->mimeType($invoice->surat_jalan_file);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($invoice->surat_jalan_file) . '"'
        ]);
    }

    /**
     * Download faktur pajak file
     */
    public function downloadFakturPajak(Invoice $invoice)
    {
        if (!$invoice->faktur_pajak_file || !Storage::disk('public')->exists($invoice->faktur_pajak_file)) {
            return redirect()->back()->with('error', 'File faktur pajak tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($invoice->faktur_pajak_file);
        $mimeType = Storage::disk('public')->mimeType($invoice->faktur_pajak_file);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($invoice->faktur_pajak_file) . '"'
        ]);
    }
}
