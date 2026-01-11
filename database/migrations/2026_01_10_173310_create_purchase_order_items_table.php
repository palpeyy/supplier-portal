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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->string('item_number', 10)->comment('Item number (e.g., 00010, 00020)');
            $table->string('material_code')->nullable()->comment('Material code (e.g., ASSV10-140)');
            $table->string('vendor_material')->nullable()->comment('Vendor Material code');
            $table->text('description')->comment('Item description');
            $table->integer('quantity')->default(0)->comment('Quantity');
            $table->decimal('price_per_unit', 15, 2)->default(0)->comment('Price per unit');
            $table->decimal('net_value', 15, 2)->default(0)->comment('Net value (quantity × price_per_unit)');
            $table->timestamps();
            
            $table->index('purchase_order_id');
            $table->index('item_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
