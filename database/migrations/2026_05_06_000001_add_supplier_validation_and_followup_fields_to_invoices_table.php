<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('supplier_validation_date')->nullable()->after('payment_proof_file');
            $table->text('supplier_validation_notes')->nullable()->after('supplier_validation_date');

            $table->string('supplier_reject_type')->nullable()->after('supplier_validation_notes')
                ->comment('overpaid | underpaid');
            $table->string('supplier_reject_proof_file')->nullable()->after('supplier_reject_type')
                ->comment('Bukti pengembalian (lebih bayar) / bukti kekurangan (kurang bayar)');
            $table->text('supplier_reject_notes')->nullable()->after('supplier_reject_proof_file');

            $table->date('finance_followup_date')->nullable()->after('supplier_reject_notes');
            $table->text('finance_followup_notes')->nullable()->after('finance_followup_date');
            $table->string('finance_followup_proof_file')->nullable()->after('finance_followup_notes')
                ->comment('Bukti tindak lanjut Finance (pembayaran tambahan / lainnya)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'supplier_validation_date',
                'supplier_validation_notes',
                'supplier_reject_type',
                'supplier_reject_proof_file',
                'supplier_reject_notes',
                'finance_followup_date',
                'finance_followup_notes',
                'finance_followup_proof_file',
            ]);
        });
    }
};

