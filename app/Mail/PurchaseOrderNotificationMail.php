<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public const TYPE_PENDING_APPROVAL = 'pending_approval';

    public const TYPE_APPROVED = 'approved';

    public const TYPE_REJECTED = 'rejected';

    public const TYPE_SUPPLIER_APPROVED = 'supplier_approved';

    public const TYPE_SUPPLIER_REJECTED = 'supplier_rejected';

    public function __construct(
        public PurchaseOrder $purchaseOrder,
        public string $type,
        public ?string $recipientName = null,
    ) {
        $this->purchaseOrder->loadMissing('supplier', 'createdBy', 'items');
    }

    public function envelope(): Envelope
    {
        $poNumber = $this->purchaseOrder->po_number ?? 'N/A';

        $subject = match ($this->type) {
            self::TYPE_PENDING_APPROVAL => '[PO Baru] Menunggu Approval — PO #' . $poNumber,
            self::TYPE_APPROVED => '[PO Disetujui] Konfirmasi Diperlukan — PO #' . $poNumber,
            self::TYPE_REJECTED => '[PO Ditolak] PO #' . $poNumber,
            default => '[Notifikasi PO] #' . $poNumber,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-order-notification',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->type === self::TYPE_REJECTED) {
            return [];
        }

        $attachments = [];

        try {
            if ($this->purchaseOrder->pdf_path && Storage::disk('public')->exists($this->purchaseOrder->pdf_path)) {
                $attachments[] = Attachment::fromStorageDisk('public', $this->purchaseOrder->pdf_path)
                    ->as('PO-' . ($this->purchaseOrder->po_number ?? 'document') . '.pdf');
            }
        } catch (\Exception $e) {
            Log::error('Error preparing PO email attachment: ' . $e->getMessage(), [
                'purchase_order_id' => $this->purchaseOrder->id,
            ]);
        }

        return $attachments;
    }
}
