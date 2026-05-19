<?php

namespace App\Services;

use App\Mail\PurchaseOrderNotificationMail;
use App\Mail\WorkflowNotificationMail;
use App\Models\PurchaseOrder;
use App\Services\Concerns\SendsPortalEmails;

class PurchaseOrderNotificationService
{
    use SendsPortalEmails;

    public function notifyDeptHeadOnUpload(PurchaseOrder $purchaseOrder): void
    {
        $this->sendPoMail(
            $this->deptHeadRecipients(),
            $purchaseOrder,
            PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL,
            'Dept Head on PO upload'
        );
    }

    public function notifySupplierOnApprove(PurchaseOrder $purchaseOrder): void
    {
        $this->sendPoMail(
            $this->supplierRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            PurchaseOrderNotificationMail::TYPE_APPROVED,
            'Supplier on PO approve by Dept Head'
        );
    }

    public function notifyPurchasingOnReject(PurchaseOrder $purchaseOrder): void
    {
        $this->sendPoMail(
            $this->purchasingRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            PurchaseOrderNotificationMail::TYPE_REJECTED,
            'Purchasing on PO reject by Dept Head'
        );
    }

    public function notifyPurchasingOnSupplierApprove(PurchaseOrder $purchaseOrder): void
    {
        $this->sendPoMail(
            $this->purchasingRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            PurchaseOrderNotificationMail::TYPE_SUPPLIER_APPROVED,
            'Purchasing on PO approve by Supplier'
        );
    }

    public function notifyPurchasingOnSupplierReject(PurchaseOrder $purchaseOrder): void
    {
        $this->sendPoMail(
            $this->purchasingRecipientsForPo($purchaseOrder),
            $purchaseOrder,
            PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED,
            'Purchasing on PO reject by Supplier'
        );
    }

    private function sendPoMail($recipients, PurchaseOrder $purchaseOrder, string $type, string $context): void
    {
        $purchaseOrder->loadMissing('supplier', 'createdBy', 'items');

        $logContext = [
            'purchase_order_id' => $purchaseOrder->id,
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
                WorkflowNotificationMail::MODULE_PO,
                $type,
                $purchaseOrder,
                recipientName: $recipient->name,
                notes: $purchaseOrder->keterangan,
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
