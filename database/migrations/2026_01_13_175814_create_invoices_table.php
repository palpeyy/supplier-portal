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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->string('invoice_file')->nullable()->comment('Path to uploaded invoice file');
            $table->string('surat_jalan_file')->nullable()->comment('Path to uploaded surat jalan/ASN file');
            $table->string('faktur_pajak_file')->nullable()->comment('Path to uploaded faktur pajak file');
            $table->enum('status', ['pending', 'revised', 'approved', 'rejected'])->default('pending')->comment('Status: pending, revised, approved, rejected');
            $table->text('catatan_revisi')->nullable()->comment('Catatan revisi dari admin');
            $table->timestamps();
            
            $table->index('purchase_order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
