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
        Schema::create('shipping_document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_document_id')->constrained('shipping_documents')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->onDelete('cascade');
            $table->integer('quantity_shipped')->comment('Quantity shipped in this document');
            $table->timestamps();

            $table->index('shipping_document_id');
            $table->index('purchase_order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_document_items');
    }
};
