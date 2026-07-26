<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * quantity_shipped belongs on shipping_document_items (per shipment).
     * etd/eta belong on shipping_documents (per surat jalan).
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('quantity_shipped');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['etd', 'eta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('quantity_shipped')->default(0)->after('quantity')->comment('Total quantity shipped');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->date('etd')->nullable()->after('delivery_date')->comment('Estimated Time Delivery');
            $table->date('eta')->nullable()->after('etd')->comment('Estimated Time Arrive');
        });
    }
};
