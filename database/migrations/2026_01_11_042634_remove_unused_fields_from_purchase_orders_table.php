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
            $table->dropColumn([
                'telephone',
                'contact_person',
                'company_number',
                'delivery_to',
                'term_of_payment',
                'catatan',
                'company',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('telephone')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('company_number')->nullable();
            $table->text('delivery_to')->nullable();
            $table->string('term_of_payment')->nullable();
            $table->text('catatan')->nullable();
            $table->string('company')->nullable();
        });
    }
};
