<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceApprovedMail;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\InvoiceWorkflowNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    private const STATUS_COMPLETED = 'completed';

    /** @deprecated Status lama; tetap dikenali untuk data/backward-compat */
    private const STATUS_READY_FOR_PAYMENT = 'ready_for_payment';

    private const STATUS_PAID = 'paid';

    /** Status invoice yang sudah divalidasi Admin (tab Selesai + dashboard). */
    private const COMPLETED_STATUSES = [
        self::STATUS_COMPLETED,
        self::STATUS_READY_FOR_PAYMENT,
        self::STATUS_PAID,
    ];

    public function __construct(
        private readonly InvoiceWorkflowNotificationService $invoiceNotificationService,
    ) {}

    /**
     * Ownership rule for purchasing-side users (Admin/Purchasing):
     * invoice can only be accessed if the related PO was uploaded by them.
     */
    private function ensurePurchasingOwnerAccess(Invoice $invoice): void
    {
        $user = auth()->user();
        $role = $user->role->name ?? null;

        if (in_array($role, ['Admin', 'Purchasing'], true)) {
            $poCreatedBy = (int) ($invoice->purchaseOrder->created_by ?? 0);
            if ($poCreatedBy !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk invoice ini');
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
        // Setelah Admin memvalidasi invoice -> status completed (tab Selesai).
        if (in_array($userRole, ['Admin', 'Purchasing'], true)) {
            $ongoingInvoices = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['pending', 'revised'])
                ->whereHas('purchaseOrder', function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->latest()
                ->paginate(10, ['*'], 'ongoing_page');

            $completedInvoices = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->where(function ($q) {
                    $q->whereIn('status', self::COMPLETED_STATUSES)
                        ->orWhere('status', 'rejected');
                })
                ->whereHas('purchaseOrder', function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->latest()
                ->paginate(10, ['*'], 'completed_page');
        } else {
            // Untuk Supplier: tampilkan invoice mereka
            $ongoingQuery = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->whereIn('status', ['pending', 'revised'])
                ->latest();

            $completedQuery = Invoice::with('purchaseOrder.supplier', 'purchaseOrder.items')
                ->where(function ($q) {
                    $q->whereIn('status', self::COMPLETED_STATUSES)
                        ->orWhere('status', 'rejected');
                })
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

        $request->validate($this->invoiceUploadValidationRules(), $this->invoiceUploadValidationMessages());

        DB::beginTransaction();
        try {
            $invoicePaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'invoice_file'), 'invoice');
            $suratJalanPaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'surat_jalan_file'), 'surat_jalan');
            $fakturPajakPaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'faktur_pajak_file'), 'faktur_pajak');

            // Create or update invoice
            if ($purchaseOrder->invoice && $purchaseOrder->invoice->status === 'revised') {
                // Update existing invoice (revision)
                $invoice = $purchaseOrder->invoice;

                // Delete old files
                $this->deleteDocumentFiles($invoice->filePaths('invoice_file'));
                $this->deleteDocumentFiles($invoice->filePaths('surat_jalan_file'));
                $this->deleteDocumentFiles($invoice->filePaths('faktur_pajak_file'));

                $invoice->update([
                    'invoice_file' => $invoicePaths,
                    'surat_jalan_file' => $suratJalanPaths,
                    'faktur_pajak_file' => $fakturPajakPaths,
                    'status' => 'pending',
                    'catatan_revisi' => null,
                ]);
            } else {
                // Create new invoice
                $invoice = Invoice::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'invoice_file' => $invoicePaths,
                    'surat_jalan_file' => $suratJalanPaths,
                    'faktur_pajak_file' => $fakturPajakPaths,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            $purchaseOrder->loadMissing('supplier', 'createdBy');
            $this->invoiceNotificationService->notifyPurchasingOnUpload($purchaseOrder, $invoice->fresh());

            if ($request->ajax()) {
                return response()->json(['success' => 'Invoice berhasil diupload']);
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded files if transaction fails
            if (! empty($invoicePaths)) {
                $this->deleteDocumentFiles($invoicePaths);
            }
            if (! empty($suratJalanPaths)) {
                $this->deleteDocumentFiles($suratJalanPaths);
            }
            if (! empty($fakturPajakPaths)) {
                $this->deleteDocumentFiles($fakturPajakPaths);
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
        $this->ensurePurchasingOwnerAccess($invoice);

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

        $request->validate($this->invoiceUploadValidationRules(), $this->invoiceUploadValidationMessages());

        DB::beginTransaction();
        try {
            $oldInvoicePaths = $invoice->filePaths('invoice_file');
            $oldSuratJalanPaths = $invoice->filePaths('surat_jalan_file');
            $oldFakturPajakPaths = $invoice->filePaths('faktur_pajak_file');

            $invoicePaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'invoice_file'), 'invoice');
            $suratJalanPaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'surat_jalan_file'), 'surat_jalan');
            $fakturPajakPaths = $this->uploadDocumentFiles($this->collectUploadedFiles($request, 'faktur_pajak_file'), 'faktur_pajak');

            // Update invoice
            $invoice->update([
                'invoice_file' => $invoicePaths,
                'surat_jalan_file' => $suratJalanPaths,
                'faktur_pajak_file' => $fakturPajakPaths,
                'status' => 'pending',
                'catatan_revisi' => null,
            ]);

            // Delete old files
            $this->deleteDocumentFiles($oldInvoicePaths);
            $this->deleteDocumentFiles($oldSuratJalanPaths);
            $this->deleteDocumentFiles($oldFakturPajakPaths);

            DB::commit();

            $invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.createdBy');
            $this->invoiceNotificationService->notifyPurchasingOnUpload($invoice->purchaseOrder, $invoice->fresh());

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

        $invoice->loadMissing('purchaseOrder');
        $this->ensurePurchasingOwnerAccess($invoice);

        // Check if user is Admin/Purchasing
        if (!in_array($userRole, ['Admin', 'Purchasing'], true)) {
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
            'status' => self::STATUS_COMPLETED,
            'catatan_revisi' => null,
        ]);

        // Email notification should never block approval.
        $this->sendApprovalEmailToFinance($invoice);
        $invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.createdBy');
        $this->invoiceNotificationService->notifySupplierOnApproved($invoice->purchaseOrder, $invoice->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'Invoice berhasil divalidasi (status: Completed). Email Finance terkirim.']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil divalidasi (status: Completed). Email Finance terkirim.');
    }

    /**
     * Send invoice approved email notification to all Finance users.
     */
    private function sendApprovalEmailToFinance(Invoice $invoice): void
    {
        try {
            $smtpUsername = (string) config('mail.mailers.smtp.username');
            $smtpPassword = (string) config('mail.mailers.smtp.password');

            // Skip sending if SMTP credentials are not configured yet.
            if (
                $smtpUsername === '' ||
                $smtpPassword === '' ||
                $smtpUsername === 'your_email@gmail.com' ||
                $smtpPassword === 'your_gmail_app_password'
            ) {
                Log::warning('Invoice approved email: SMTP belum dikonfigurasi (MAIL_USERNAME/MAIL_PASSWORD masih kosong/placeholder). Email tidak dikirim.', [
                    'invoice_id' => $invoice->id,
                ]);
                return;
            }

            // Ambil semua user dengan role Finance
            $financeUsers = User::whereHas('role', function ($q) {
                $q->where('name', 'Finance');
            })->whereNotNull('email')->get();

            if ($financeUsers->isEmpty()) {
                Log::warning('Invoice approved email: tidak ada user Finance yang ditemukan.');
                return;
            }

            foreach ($financeUsers as $financeUser) {
                try {
                    Mail::to($financeUser->email, $financeUser->name)
                        ->send(new InvoiceApprovedMail($invoice));
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim email notifikasi Finance ke user tertentu: ' . $e->getMessage(), [
                        'invoice_id' => $invoice->id,
                        'to' => $financeUser->email,
                    ]);
                }
            }

            Log::info('Invoice approved email berhasil dikirim ke ' . $financeUsers->count() . ' user Finance.', [
                'invoice_id' => $invoice->id,
                'po_number'  => $invoice->purchaseOrder->po_number ?? 'N/A',
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan proses approve
            Log::error('Gagal mengirim email notifikasi Finance: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    /**
     * Reject invoice (Admin only)
     */
    public function reject(Request $request, Invoice $invoice)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;

        $invoice->loadMissing('purchaseOrder');
        $this->ensurePurchasingOwnerAccess($invoice);

        // Check if user is Admin/Purchasing
        if (!in_array($userRole, ['Admin', 'Purchasing'], true)) {
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

        $invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.createdBy');
        $this->invoiceNotificationService->notifySupplierOnRejected($invoice->purchaseOrder, $invoice->fresh());

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

        $invoice->loadMissing('purchaseOrder');
        $this->ensurePurchasingOwnerAccess($invoice);

        // Check if user is Admin/Purchasing
        if (!in_array($userRole, ['Admin', 'Purchasing'], true)) {
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

        $invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.createdBy');
        $this->invoiceNotificationService->notifySupplierOnRevised($invoice->purchaseOrder, $invoice->fresh());

        if ($request->ajax()) {
            return response()->json(['success' => 'Invoice berhasil di-revise']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-revise');
    }

    /**
     * Download invoice file
     */
    public function downloadInvoice(Request $request, Invoice $invoice)
    {
        return $this->downloadDocumentFile($request, $invoice, 'invoice_file', 'File invoice tidak ditemukan');
    }

    /**
     * Download surat jalan file
     */
    public function downloadSuratJalan(Request $request, Invoice $invoice)
    {
        return $this->downloadDocumentFile($request, $invoice, 'surat_jalan_file', 'File surat jalan tidak ditemukan');
    }

    /**
     * Download faktur pajak file
     */
    public function downloadFakturPajak(Request $request, Invoice $invoice)
    {
        return $this->downloadDocumentFile($request, $invoice, 'faktur_pajak_file', 'File faktur pajak tidak ditemukan');
    }

    /**
     * @return array<string, mixed>
     */
    private function invoiceUploadValidationRules(): array
    {
        $fileRule = 'file|mimes:pdf,jpg,jpeg,png|max:10240';

        return [
            'invoice_file' => 'required|array|min:1',
            'invoice_file.*' => $fileRule,
            'surat_jalan_file' => 'required|array|min:1',
            'surat_jalan_file.*' => $fileRule,
            'faktur_pajak_file' => 'required|array|min:1',
            'faktur_pajak_file.*' => $fileRule,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function invoiceUploadValidationMessages(): array
    {
        return [
            'invoice_file.required' => 'File invoice harus diupload',
            'invoice_file.array' => 'File invoice harus berupa daftar file',
            'invoice_file.min' => 'Minimal 1 file invoice harus diupload',
            'invoice_file.*.file' => 'File invoice harus berupa file yang valid',
            'invoice_file.*.mimes' => 'File invoice harus berformat PDF, JPG, atau PNG',
            'invoice_file.*.max' => 'Ukuran file invoice maksimal 10MB',
            'surat_jalan_file.required' => 'File surat jalan harus diupload',
            'surat_jalan_file.array' => 'File surat jalan harus berupa daftar file',
            'surat_jalan_file.min' => 'Minimal 1 file surat jalan harus diupload',
            'surat_jalan_file.*.file' => 'File surat jalan harus berupa file yang valid',
            'surat_jalan_file.*.mimes' => 'File surat jalan harus berformat PDF, JPG, atau PNG',
            'surat_jalan_file.*.max' => 'Ukuran file surat jalan maksimal 10MB',
            'faktur_pajak_file.required' => 'File faktur pajak harus diupload',
            'faktur_pajak_file.array' => 'File faktur pajak harus berupa daftar file',
            'faktur_pajak_file.min' => 'Minimal 1 file faktur pajak harus diupload',
            'faktur_pajak_file.*.file' => 'File faktur pajak harus berupa file yang valid',
            'faktur_pajak_file.*.mimes' => 'File faktur pajak harus berformat PDF, JPG, atau PNG',
            'faktur_pajak_file.*.max' => 'Ukuran file faktur pajak maksimal 10MB',
        ];
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function collectUploadedFiles(Request $request, string $field): array
    {
        $files = $request->file($field);

        if ($files === null) {
            return [];
        }

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        return array_values(array_filter(
            $files,
            fn ($file) => $file instanceof UploadedFile && $file->isValid()
        ));
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array<int, string>
     */
    private function uploadDocumentFiles(array $files, string $prefix): array
    {
        $paths = [];

        foreach ($files as $index => $file) {
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
            $filename = $prefix . '_' . now()->format('YmdHisv') . '_' . $index . '_' . Str::random(8) . '.' . $extension;
            $paths[] = $file->storeAs('invoices', $filename, 'public');
        }

        return $paths;
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteDocumentFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function downloadDocumentFile(Request $request, Invoice $invoice, string $field, string $notFoundMessage)
    {
        $invoice->loadMissing('purchaseOrder');
        $this->ensurePurchasingOwnerAccess($invoice);

        $files = $invoice->filePaths($field);
        if (empty($files)) {
            return redirect()->back()->with('error', $notFoundMessage);
        }

        $index = (int) $request->query('index', 0);
        if (! isset($files[$index]) || ! Storage::disk('public')->exists($files[$index])) {
            return redirect()->back()->with('error', $notFoundMessage);
        }

        $storedPath = $files[$index];
        $filePath = Storage::disk('public')->path($storedPath);
        $mimeType = Storage::disk('public')->mimeType($storedPath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($storedPath) . '"',
        ]);
    }
}
