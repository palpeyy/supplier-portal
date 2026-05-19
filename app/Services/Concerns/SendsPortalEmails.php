<?php

namespace App\Services\Concerns;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait SendsPortalEmails
{
    protected function isSmtpConfigured(): bool
    {
        $smtpUsername = (string) config('mail.mailers.smtp.username');
        $smtpPassword = (string) config('mail.mailers.smtp.password');

        if (
            $smtpUsername === '' ||
            $smtpPassword === '' ||
            $smtpUsername === 'your_email@gmail.com' ||
            $smtpPassword === 'your_gmail_app_password'
        ) {
            Log::warning('Portal email: SMTP belum dikonfigurasi. Email tidak dikirim.');

            return false;
        }

        return true;
    }

    protected function deptHeadRecipients(): Collection
    {
        return User::whereHas('role', function ($q) {
            $q->where('name', 'Dept. Head');
        })->whereNotNull('email')->get();
    }

    protected function purchasingRecipientsForPo(PurchaseOrder $purchaseOrder): Collection
    {
        $purchaseOrder->loadMissing('createdBy');

        $creator = $purchaseOrder->createdBy;
        if (!$creator?->email) {
            return collect();
        }

        return collect([$creator]);
    }

    protected function supplierRecipientsForPo(PurchaseOrder $purchaseOrder): Collection
    {
        if (empty($purchaseOrder->supplier_id)) {
            return collect();
        }

        return User::where('supplier_id', $purchaseOrder->supplier_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Supplier');
            })
            ->whereNotNull('email')
            ->get();
    }

    protected function sendMailableToUsers(Collection $recipients, Mailable $mailable, string $context, array $logContext = []): void
    {
        if (!$this->isSmtpConfigured()) {
            return;
        }

        if ($recipients->isEmpty()) {
            Log::warning("Portal email ({$context}): tidak ada penerima.", $logContext);

            return;
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email, $recipient->name)->send($mailable);
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim portal email ({$context}): " . $e->getMessage(), array_merge($logContext, [
                    'to' => $recipient->email,
                ]));
            }
        }

        Log::info("Portal email ({$context}) dikirim ke {$recipients->count()} penerima.", $logContext);
    }
}
