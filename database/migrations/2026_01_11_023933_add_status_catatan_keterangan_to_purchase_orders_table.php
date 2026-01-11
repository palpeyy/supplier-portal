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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->comment('Status: pending, approved, rejected');
            $table->text('catatan')->nullable()->comment('Catatan internal');
            $table->text('keterangan')->nullable()->comment('Keterangan status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan', 'keterangan']);
        });
    }
};
