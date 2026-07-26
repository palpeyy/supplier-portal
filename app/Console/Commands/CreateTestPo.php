<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Console\Command;

class CreateTestPo extends Command
{
    protected $signature = 'po:create-test';
    protected $description = 'Create test PO data untuk supplier';

    public function handle()
    {
        $this->line('=== CREATE TEST PURCHASE ORDER ===\n');

        // Get supplier PT Supplier Utama
        $supplier = Supplier::where('nama', 'PT Supplier Utama')->first();
        if (!$supplier) {
            $this->error('❌ Supplier PT Supplier Utama tidak ditemukan!');
            return 1;
        }

        $this->info('✅ Supplier ditemukan: ' . $supplier->nama . ' (ID: ' . $supplier->id . ')');
        $this->line('');

        // Create test PO with status 'pending' (menunggu approval dari Dept Head)
        $po = PurchaseOrder::create([
            'po_number' => 'TEST-PO-' . now()->format('YmdHis'),
            'date' => now(),
            'delivery_date' => now()->addDays(30),
            'currency' => 'IDR',
            'company_address' => 'Jl. Test No. 123',
            'pdf_path' => null,
            'status' => 'pending', // 👈 PENDING - menunggu Dept Head approve
            'keterangan' => 'Menunggu Approval Dept. Head',
            'supplier_id' => $supplier->id, // 👈 ASSIGNED ke supplier
            'item_count' => 2,
        ]);

        $this->info('✅ Test PO created:');
        $this->line('  PO Number: ' . $po->po_number);
        $this->line('  Status: ' . $po->status . ' (menunggu approval dari Dept Head)');
        $this->line('  Supplier: ' . $supplier->nama);
        $this->line('  ID: ' . $po->id);
        $this->line('');

        // Create items
        $items = [
            [
                'item_number' => '00010',
                'material_code' => 'MAT-001',
                'vendor_material' => 'VM-001',
                'description' => 'Motor Socket A7.5',
                'quantity' => 100,
                'price_per_unit' => 50000,
                'net_value' => 5000000,
            ],
            [
                'item_number' => '00020',
                'material_code' => 'MAT-002',
                'vendor_material' => 'VM-002',
                'description' => 'Kapasitor Keramik 10uF',
                'quantity' => 500,
                'price_per_unit' => 5000,
                'net_value' => 2500000,
            ],
        ];

        foreach ($items as $itemData) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'item_number' => $itemData['item_number'],
                'material_code' => $itemData['material_code'],
                'vendor_material' => $itemData['vendor_material'],
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price_per_unit' => $itemData['price_per_unit'],
                'net_value' => $itemData['net_value'],
            ]);
        }

        $this->info('✅ Items created:');
        foreach ($po->items as $item) {
            $this->line('  - ' . $item->material_code . ' | ' . $item->description . ' | Qty: ' . $item->quantity);
        }

        $this->line('');
        $this->info('=== TEST DATA READY - FULL WORKFLOW ===\n');

        $this->line('Flow untuk Test:');
        $this->line('');
        $this->line('STEP 1️⃣ - Dept Head Approve (depthead@example.com / password123):');
        $this->line('  1. Login ke portal');
        $this->line('  2. Go to "Purchase Orders"');
        $this->line('  3. Tab "Menunggu Approval" - akan ada PO: ' . $po->po_number);
        $this->line('  4. Klik tombol "Approve" (✓) untuk approve');
        $this->line('  5. PO akan pindah ke tab "Sedang Diproses"');
        $this->line('');
        $this->line('STEP 2️⃣ - Supplier Confirm (supplier@example.com / password123):');
        $this->line('  1. Login ke portal (sebagai supplier)');
        $this->line('  2. Go to "Purchase Orders"');
        $this->line('  3. Tab "Sedang Diproses" - lihat PO: ' . $po->po_number);
        $this->line('  4. Klik tombol "Show Detail" untuk lihat detail');
        $this->line('  5. Klik "Approve" dan input ETD, ETA, & No Surat Jalan');
        $this->line('  6. Supplier sekarang bisa upload Surat Jalan/Shipping Documents');
        $this->line('');

        return 0;
    }
}
