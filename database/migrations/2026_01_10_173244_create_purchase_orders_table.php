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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique()->comment('PO Number / Date format: 1580057931 / 290925 / PS');
            $table->date('date')->comment('PO Date');
            $table->integer('item_count')->default(0)->comment('Total number of items');
            $table->string('contact_person')->nullable()->comment('Contact Person');
            $table->string('telephone')->nullable()->comment('Telephone');
            $table->date('delivery_date')->nullable()->comment('Delivery Date');
            $table->string('currency', 3)->default('IDR')->comment('Currency code');
            $table->string('company')->nullable()->comment('Company name');
            $table->text('company_address')->nullable()->comment('Company address');
            $table->string('company_number')->nullable()->comment('Company Number With Us');
            $table->text('delivery_to')->nullable()->comment('Please Deliver To address');
            $table->string('term_of_payment')->nullable()->comment('Term Of Payment');
            $table->string('pdf_path')->nullable()->comment('Path to uploaded PDF file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
