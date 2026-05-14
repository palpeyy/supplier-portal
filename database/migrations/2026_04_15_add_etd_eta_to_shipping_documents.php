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
        Schema::table('shipping_documents', function (Blueprint $table) {
            $table->date('etd')->nullable()->after('date')->comment('Estimated Time Departure');
            $table->date('eta')->nullable()->after('etd')->comment('Estimated Time Arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_documents', function (Blueprint $table) {
            $table->dropColumn(['etd', 'eta']);
        });
    }
};
