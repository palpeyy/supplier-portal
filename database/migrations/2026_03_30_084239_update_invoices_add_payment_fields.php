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
            $table->date('planned_payment_date')->nullable()->comment('Jadwal pembayaran yang direncanakan oleh Finance');
            $table->date('mark_as_paid_date')->nullable()->comment('Tanggal invoice ditandai sebagai paid');
            $table->text('payment_notes')->nullable()->comment('Catatan pembayaran dari Finance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['planned_payment_date', 'mark_as_paid_date', 'payment_notes']);
        });
    }
};
