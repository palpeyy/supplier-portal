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
            $table->dropColumn([
                'planned_payment_date',
                'mark_as_paid_date',
                'payment_notes',
                'payment_proof_file',
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('planned_payment_date')->nullable();
            $table->date('mark_as_paid_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->string('payment_proof_file')->nullable();
            $table->date('supplier_validation_date')->nullable();
            $table->text('supplier_validation_notes')->nullable();
            $table->string('supplier_reject_type')->nullable();
            $table->string('supplier_reject_proof_file')->nullable();
            $table->text('supplier_reject_notes')->nullable();
            $table->date('finance_followup_date')->nullable();
            $table->text('finance_followup_notes')->nullable();
            $table->string('finance_followup_proof_file')->nullable();
        });
    }
};

