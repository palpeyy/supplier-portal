<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\ShippingDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorkflowNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public const MODULE_PO = 'po';

    public const MODULE_SHIPPING = 'shipping';

    public const MODULE_INVOICE = 'invoice';

    public function __construct(
        public string $module,
        public string $type,
        public PurchaseOrder $purchaseOrder,
        public ?ShippingDocument $shippingDocument = null,
        public ?Invoice $invoice = null,
        public ?string $recipientName = null,
        public ?string $notes = null,
    ) {
        $this->purchaseOrder->loadMissing('supplier', 'createdBy', 'items');

        if ($this->shippingDocument) {
            $this->shippingDocument->loadMissing('items');
        }

        if ($this->invoice) {
            $this->invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.items');
        }
    }

    public function envelope(): Envelope
    {
        $poNumber = $this->purchaseOrder->po_number ?? 'N/A';

        $subject = match ($this->module) {
            self::MODULE_PO => match ($this->type) {
                PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL => '[PO Baru] Menunggu Approval — PO #' . $poNumber,
                PurchaseOrderNotificationMail::TYPE_APPROVED => '[PO Disetujui] Konfirmasi Diperlukan — PO #' . $poNumber,
                PurchaseOrderNotificationMail::TYPE_REJECTED => '[PO Ditolak] PO #' . $poNumber,
                PurchaseOrderNotificationMail::TYPE_SUPPLIER_APPROVED => '[PO Dikonfirmasi Supplier] PO #' . $poNumber,
                PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED => '[PO Ditolak Supplier] PO #' . $poNumber,
                default => '[Notifikasi PO] #' . $poNumber,
            },
            self::MODULE_SHIPPING => match ($this->type) {
                'created' => '[Surat Jalan Baru] PO #' . $poNumber,
                'approved' => '[Surat Jalan Disetujui] PO #' . $poNumber,
                'rejected' => '[Surat Jalan Ditolak] PO #' . $poNumber,
                'revised' => '[Surat Jalan Perlu Revisi] PO #' . $poNumber,
                default => '[Notifikasi Surat Jalan] PO #' . $poNumber,
            },
            self::MODULE_INVOICE => match ($this->type) {
                'uploaded' => '[Tagihan Baru] PO #' . $poNumber,
                'approved' => '[Tagihan Divalidasi] PO #' . $poNumber,
                'rejected' => '[Tagihan Ditolak] PO #' . $poNumber,
                'revised' => '[Tagihan Perlu Revisi] PO #' . $poNumber,
                default => '[Notifikasi Tagihan] PO #' . $poNumber,
            },
            default => '[Notifikasi Portal] PO #' . $poNumber,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.workflow-notification',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        try {
            if ($this->module === self::MODULE_PO && $this->type !== PurchaseOrderNotificationMail::TYPE_REJECTED
                && $this->type !== PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED) {
                if ($this->purchaseOrder->pdf_path && Storage::disk('public')->exists($this->purchaseOrder->pdf_path)) {
                    $attachments[] = Attachment::fromStorageDisk('public', $this->purchaseOrder->pdf_path)
                        ->as('PO-' . ($this->purchaseOrder->po_number ?? 'document') . '.pdf');
                }
            }

            if ($this->module === self::MODULE_INVOICE && $this->invoice && $this->type === 'uploaded') {
                foreach ([
                    'invoice_file' => 'Invoice',
                    'surat_jalan_file' => 'Surat-Jalan',
                    'faktur_pajak_file' => 'Faktur-Pajak',
                ] as $field => $label) {
                    $paths = $this->invoice->filePaths($field);
                    foreach ($paths as $index => $path) {
                        if (Storage::disk('public')->exists($path)) {
                            $suffix = count($paths) > 1 ? '-' . ($index + 1) : '';
                            $attachments[] = Attachment::fromStorageDisk('public', $path)
                                ->as($label . $suffix . '-' . ($this->purchaseOrder->po_number ?? 'doc') . '-' . basename($path));
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error preparing workflow email attachments: ' . $e->getMessage(), [
                'module' => $this->module,
                'type' => $this->type,
                'purchase_order_id' => $this->purchaseOrder->id,
            ]);
        }

        return $attachments;
    }
}
