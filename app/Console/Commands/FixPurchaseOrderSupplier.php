<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Console\Command;

class FixPurchaseOrderSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:fix-supplier {--supplier-id= : Supplier ID to assign to PO with NULL supplier_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Purchase Orders dengan supplier_id NULL dengan assign supplier';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all POs with NULL supplier_id
        $posWithNullSupplier = PurchaseOrder::where('supplier_id', null)->get();

        if ($posWithNullSupplier->isEmpty()) {
            $this->info('✅ Semua PO sudah memiliki supplier_id. Tidak ada yang perlu di-fix.');
            return;
        }

        $this->warn('⚠️  Ditemukan ' . $posWithNullSupplier->count() . ' PO dengan supplier_id NULL:');
        $this->line('');

        foreach ($posWithNullSupplier as $po) {
            $this->line('  - PO: ' . $po->po_number . ' (ID: ' . $po->id . ')');
        }

        $this->line('');

        if ($this->option('supplier-id')) {
            // Auto-assign dengan supplier_id yang diberikan
            $supplierId = $this->option('supplier-id');
            $supplier = Supplier::find($supplierId);

            if (!$supplier) {
                $this->error('❌ Supplier dengan ID ' . $supplierId . ' tidak ditemukan!');
                return 1;
            }

            // Update semua PO dengan supplier_id ini
            $updated = PurchaseOrder::where('supplier_id', null)->update(['supplier_id' => $supplierId]);

            $this->info('✅ ' . $updated . ' PO berhasil di-assign ke supplier: ' . $supplier->nama);
            return 0;
        }

        // Show list suppliers
        $this->line('📋 Pilih supplier untuk assign ke PO-PO di atas:');
        $this->line('');

        $suppliers = Supplier::all();
        foreach ($suppliers as $index => $supplier) {
            $this->line('  [' . ($index + 1) . '] ' . $supplier->nama . ' (ID: ' . $supplier->id . ')');
        }

        $this->line('');
        $choice = $this->ask('Pilih nomor supplier (1-' . $suppliers->count() . ')');

        if (!is_numeric($choice) || $choice < 1 || $choice > $suppliers->count()) {
            $this->error('❌ Pilihan tidak valid!');
            return 1;
        }

        $selectedSupplier = $suppliers[$choice - 1];

        // Confirm
        if (!$this->confirm('Assign ' . $posWithNullSupplier->count() . ' PO ke supplier "' . $selectedSupplier->nama . '"?')) {
            $this->line('❌ Dibatalkan');
            return 0;
        }

        // Update
        $updated = PurchaseOrder::where('supplier_id', null)->update(['supplier_id' => $selectedSupplier->id]);

        $this->info('✅ ' . $updated . ' PO berhasil di-assign ke supplier: ' . $selectedSupplier->nama);

        return 0;
    }
}
