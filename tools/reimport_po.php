<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = (int) ($argv[1] ?? 0);
if ($id <= 0) {
    echo "Usage: php tools/reimport_po.php {purchase_order_id}\n";
    exit(1);
}

$po = \App\Models\PurchaseOrder::find($id);
if (! $po || ! $po->pdf_path) {
    echo "PO not found or no PDF.\n";
    exit(1);
}

$extractor = new \App\Services\PdfExtractorService();
$data = $extractor->extractPurchaseOrderData($po->pdf_path);

\Illuminate\Support\Facades\DB::transaction(function () use ($po, $data) {
    $po->items()->delete();
    $po->update([
        'company_address' => \App\Models\PurchaseOrder::resolveCompanyAddress($data['company_address'] ?? null),
        'item_count' => count($data['items'] ?? []),
        'delivery_date' => $data['delivery_date'] ?? $po->delivery_date,
        'currency' => $data['currency'] ?? $po->currency,
    ]);

    foreach ($data['items'] ?? [] as $itemData) {
        \App\Models\PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'item_number' => $itemData['item_number'] ?? null,
            'material_code' => $itemData['material_code'],
            'vendor_material' => $itemData['vendor_material'],
            'description' => $itemData['description'] ?? '',
            'quantity' => $itemData['quantity'] ?? 0,
            'price_per_unit' => $itemData['price_per_unit'] ?? 0,
            'net_value' => $itemData['net_value'] ?? 0,
        ]);
    }
});

$po->refresh()->load('items');
echo "PO #{$po->id} reimported: {$po->items->count()} items.\n";
