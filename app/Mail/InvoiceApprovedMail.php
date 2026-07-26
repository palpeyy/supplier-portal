<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing('purchaseOrder.supplier', 'purchaseOrder.items');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $poNumber = $this->invoice->purchaseOrder->po_number ?? 'N/A';

        return new Envelope(
            subject: '[Invoice Divalidasi] PO #' . $poNumber . ' - Siap Diproses Pembayaran',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        try {
            foreach ($this->invoice->filePaths('invoice_file') as $index => $path) {
                if (Storage::disk('public')->exists($path)) {
                    $suffix = count($this->invoice->filePaths('invoice_file')) > 1 ? '-' . ($index + 1) : '';
                    $attachments[] = Attachment::fromStorageDisk('public', $path)
                        ->as('Invoice' . $suffix . '-' . $this->invoice->purchaseOrder->po_number . '-' . basename($path));
                }
            }

            foreach ($this->invoice->filePaths('surat_jalan_file') as $index => $path) {
                if (Storage::disk('public')->exists($path)) {
                    $suffix = count($this->invoice->filePaths('surat_jalan_file')) > 1 ? '-' . ($index + 1) : '';
                    $attachments[] = Attachment::fromStorageDisk('public', $path)
                        ->as('Surat-Jalan' . $suffix . '-' . $this->invoice->purchaseOrder->po_number . '-' . basename($path));
                }
            }

            foreach ($this->invoice->filePaths('faktur_pajak_file') as $index => $path) {
                if (Storage::disk('public')->exists($path)) {
                    $suffix = count($this->invoice->filePaths('faktur_pajak_file')) > 1 ? '-' . ($index + 1) : '';
                    $attachments[] = Attachment::fromStorageDisk('public', $path)
                        ->as('Faktur-Pajak' . $suffix . '-' . $this->invoice->purchaseOrder->po_number . '-' . basename($path));
                }
            }

            Log::info('Invoice attachments prepared: ' . count($attachments) . ' files', [
                'invoice_id' => $this->invoice->id,
                'po_number' => $this->invoice->purchaseOrder->po_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Error preparing invoice attachments: ' . $e->getMessage(), [
                'invoice_id' => $this->invoice->id,
            ]);
        }

        return $attachments;
    }
}
