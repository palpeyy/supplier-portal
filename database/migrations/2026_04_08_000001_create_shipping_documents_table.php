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
        Schema::create('shipping_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->string('no_surat_jalan')->comment('Shipping document number');
            $table->date('date')->comment('Date of shipping document');
            $table->string('status')->default('draft')->comment('Status: draft, confirmed, received');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('no_surat_jalan');
            $table->unique(['purchase_order_id', 'no_surat_jalan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_documents');
    }
};
