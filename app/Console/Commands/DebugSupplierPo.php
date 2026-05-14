<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;

class DebugSupplierPo extends Command
{
    protected $signature = 'po:debug-supplier';
    protected $description = 'Debug: Show PO untuk supplier user';

    public function handle()
    {
        $this->line('=== DEBUGGING PO UNTUK SUPPLIER USER ===\n');

        // Get supplier user
        $supplierUser = User::where('email', 'supplier@example.com')->first();

        if (!$supplierUser) {
            $this->error('❌ User supplier@example.com tidak ditemukan!');
            return 1;
        }

        $this->info('✅ Supplier User ditemukan:');
        $this->line('  Email: ' . $supplierUser->email);
        $this->line('  Nama: ' . $supplierUser->name);
        $this->line('  Supplier ID: ' . ($supplierUser->supplier_id ?? 'NULL'));
        $this->line('');

        if (!$supplierUser->supplier_id) {
            $this->error('❌ Supplier user belum terhubung dengan supplier!');
            return 1;
        }

        $supplier = Supplier::find($supplierUser->supplier_id);
        if ($supplier) {
            $this->info('✅ Supplier ditemukan: ' . $supplier->nama);
        } else {
            $this->error('❌ Supplier dengan ID ' . $supplierUser->supplier_id . ' tidak ditemukan!');
            return 1;
        }

        $this->line('');
        $this->line('=== PO UNTUK SUPPLIER INI ===\n');

        // Get all PO for this supplier
        $allPos = PurchaseOrder::where('supplier_id', $supplierUser->supplier_id)->get();

        if ($allPos->isEmpty()) {
            $this->warn('⚠️  Tidak ada PO untuk supplier ini');
        } else {
            $this->info('Total PO: ' . $allPos->count());
            $this->line('');

            foreach ($allPos as $po) {
                $statusColor = match ($po->status) {
                    'pending' => 'yellow',
                    'approved' => 'green',
                    'on_progress' => 'blue',
                    'received' => 'cyan',
                    'supplier_rejected' => 'red',
                    default => 'white'
                };

                $this->line('PO #' . $po->id . ':');
                $this->line('  PO Number: ' . $po->po_number);
                $this->line('  Status: <fg=' . $statusColor . '>' . strtoupper($po->status) . '</>');
                $this->line('  Date: ' . $po->date->format('d/m/Y'));
                $this->line('  Keterangan: ' . $po->keterangan);
                $this->line('');
            }
        }

        // Show what supplier should see in index
        $this->line('=== QUERY HASIL UNTUK SUPPLIER ===\n');

        $ongoingPos = PurchaseOrder::where('supplier_id', $supplierUser->supplier_id)
            ->whereIn('status', ['approved', 'supplier_rejected'])
            ->get();

        $completedPos = PurchaseOrder::where('supplier_id', $supplierUser->supplier_id)
            ->whereIn('status', ['on_progress', 'received'])
            ->get();

        $this->line('Tab "Ongoing" (approved, supplier_rejected):');
        if ($ongoingPos->isEmpty()) {
            $this->warn('  (Kosong)');
        } else {
            foreach ($ongoingPos as $po) {
                $this->line('  - ' . $po->po_number . ' (' . $po->status . ')');
            }
        }

        $this->line('');
        $this->line('Tab "Completed" (on_progress, received):');
        if ($completedPos->isEmpty()) {
            $this->warn('  (Kosong)');
        } else {
            foreach ($completedPos as $po) {
                $this->line('  - ' . $po->po_number . ' (' . $po->status . ')');
            }
        }

        $this->line('');
        $this->info('=== DEBUG COMPLETE ===');

        return 0;
    }
}
