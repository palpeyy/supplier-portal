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
        // Add fulfillment_status to purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'fulfillment_status')) {
                $table->enum('fulfillment_status', ['pending', 'partial', 'complete', 'cancelled'])->default('pending')->after('status')->comment('Fulfillment progress: pending, partial, complete, cancelled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'fulfillment_status')) {
                $table->dropColumn('fulfillment_status');
            }
        });
    }
};
