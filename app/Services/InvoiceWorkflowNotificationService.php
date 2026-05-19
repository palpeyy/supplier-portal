<?php

namespace App\Services;

use App\Mail\WorkflowNotificationMail;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Services\Concerns\SendsPortalEmails;

class InvoiceWorkflowNotificationService
{
    use SendsPortalEmails;

    public function notifyPurchasingOnUpload(PurchaseOrder $purchaseOrder, Invoice $invoice): void
    {
        $this->send(
            $this->purchasingRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $invoice,
            'uploaded',
            'Purchasing on invoice uploaded'
        );
    }

    public function notifySupplierOnApproved(PurchaseOrder $purchaseOrder, Invoice $invoice): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $invoice,
            'approved',
            'Supplier on invoice approved'
        );
    }

    public function notifySupplierOnRejected(PurchaseOrder $purchaseOrder, Invoice $invoice): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $invoice,
            'rejected',
            'Supplier on invoice rejected',
            $invoice->catatan_revisi
        );
    }

    public function notifySupplierOnRevised(PurchaseOrder $purchaseOrder, Invoice $invoice): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $invoice,
            'revised',
            'Supplier on invoice revised',
            $invoice->catatan_revisi
        );
    }

    private function send($recipients, PurchaseOrder $purchaseOrder, Invoice $invoice, string $type, string $context, ?string $notes = null): void
    {
        $purchaseOrder->loadMissing('supplier', 'createdBy');
        $invoice->loadMissing('purchaseOrder');

        $logContext = [
            'purchase_order_id' => $purchaseOrder->id,
            'invoice_id' => $invoice->id,
            'po_number' => $purchaseOrder->po_number,
        ];

        if (!$this->isSmtpConfigured()) {
            return;
        }

        if ($recipients->isEmpty()) {
            \Illuminate\Support\Facades\Log::warning("Portal email ({$context}): tidak ada penerima.", $logContext);

            return;
        }

        foreach ($recipients as $recipient) {
            $mailable = new WorkflowNotificationMail(
                WorkflowNotificationMail::MODULE_INVOICE,
                $type,
                $purchaseOrder,
                invoice: $invoice,
                recipientName: $recipient->name,
                notes: $notes ?? $invoice->catatan_revisi,
            );

            try {
                \Illuminate\Support\Facades\Mail::to($recipient->email, $recipient->name)->send($mailable);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim portal email ({$context}): " . $e->getMessage(), array_merge($logContext, [
                    'to' => $recipient->email,
                ]));
            }
        }

        \Illuminate\Support\Facades\Log::info("Portal email ({$context}) dikirim ke {$recipients->count()} penerima.", $logContext);
    }
}
