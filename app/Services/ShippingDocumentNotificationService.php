<?php

namespace App\Services;

use App\Mail\WorkflowNotificationMail;
use App\Models\PurchaseOrder;
use App\Models\ShippingDocument;
use App\Services\Concerns\SendsPortalEmails;

class ShippingDocumentNotificationService
{
    use SendsPortalEmails;

    public function notifyPurchasingOnCreated(PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument): void
    {
        $this->send(
            $this->purchasingRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $shippingDocument,
            'created',
            'Purchasing on shipping document created'
        );
    }

    public function notifySupplierOnApproved(PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $shippingDocument,
            'approved',
            'Supplier on shipping document approved'
        );
    }

    public function notifySupplierOnRejected(PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $shippingDocument,
            'rejected',
            'Supplier on shipping document rejected'
        );
    }

    public function notifySupplierOnRevised(PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument): void
    {
        $this->send(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            $shippingDocument,
            'revised',
            'Supplier on shipping document revised'
        );
    }

    private function send($recipients, PurchaseOrder $purchaseOrder, ShippingDocument $shippingDocument, string $type, string $context): void
    {
        $purchaseOrder->loadMissing('supplier', 'createdBy');
        $shippingDocument->loadMissing('items');

        $logContext = [
            'purchase_order_id' => $purchaseOrder->id,
            'shipping_document_id' => $shippingDocument->id,
            'no_surat_jalan' => $shippingDocument->no_surat_jalan,
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
                WorkflowNotificationMail::MODULE_SHIPPING,
                $type,
                $purchaseOrder,
                $shippingDocument,
                recipientName: $recipient->name,
                notes: $shippingDocument->notes,
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
