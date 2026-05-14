<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$to = $argv[1] ?? 'naufalaulia05@gmail.com';
$invoiceId = $argv[2] ?? null;

try {
    $invoiceQuery = App\Models\Invoice::query()->latest('id');
    if ($invoiceId !== null) {
        $invoiceQuery->where('id', (int) $invoiceId);
    }

    /** @var App\Models\Invoice|null $invoice */
    $invoice = $invoiceQuery->first();

    if (!$invoice) {
        echo "FAILED: invoice_not_found\n";
        exit(1);
    }

    Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\InvoiceApprovedMail($invoice));

    echo "SENT InvoiceApprovedMail invoice_id={$invoice->id} to={$to}\n";
} catch (Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

