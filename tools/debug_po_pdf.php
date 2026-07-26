<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = (int) ($argv[1] ?? 0);
$query = \App\Models\PurchaseOrder::with('items')->latest();
if ($id > 0) {
    $query->where('id', $id);
}
$pos = $query->take(5)->get();

foreach ($pos as $po) {
    echo "=== PO #{$po->id} {$po->po_number} ===\n";
    echo "PDF: {$po->pdf_path}\n";
    echo "Items in DB: {$po->items->count()} | item_count field: {$po->item_count}\n";
    echo "Address: {$po->company_address}\n";

    if (! $po->pdf_path || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($po->pdf_path)) {
        echo "PDF file missing\n\n";
        continue;
    }

    try {
        $extractor = new \App\Services\PdfExtractorService();
        $data = $extractor->extractPurchaseOrderData($po->pdf_path);
        echo 'Extracted items: '.count($data['items'] ?? [])."\n";
        echo 'Extracted address: '.($data['company_address'] ?? '-')."\n";

        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($po->pdf_path);
        $parser = new \Smalot\PdfParser\Parser();
        $text = $parser->parseFile($fullPath)->getText();
        echo "--- PDF TEXT (first 1200 chars) ---\n";
        echo substr($text, 0, 1200)."\n";
    } catch (\Throwable $e) {
        echo 'ERROR: '.$e->getMessage()."\n";
    }
    echo "\n";
}
