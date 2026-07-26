<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['invoice_file', 'faktur_pajak_file'] as $column) {
            $invoices = DB::table('invoices')->whereNotNull($column)->get();

            foreach ($invoices as $invoice) {
                $value = $invoice->{$column};

                if ($value && ! str_starts_with(trim($value), '[')) {
                    DB::table('invoices')
                        ->where('id', $invoice->id)
                        ->update([$column => json_encode([$value])]);
                }
            }
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->json('invoice_file')->nullable()->change();
            $table->json('faktur_pajak_file')->nullable()->change();
        });
    }

    public function down(): void
    {
        foreach (['invoice_file', 'faktur_pajak_file'] as $column) {
            $invoices = DB::table('invoices')->whereNotNull($column)->get();

            foreach ($invoices as $invoice) {
                $decoded = json_decode($invoice->{$column}, true);

                if (is_array($decoded) && count($decoded) > 0) {
                    DB::table('invoices')
                        ->where('id', $invoice->id)
                        ->update([$column => $decoded[0]]);
                }
            }
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_file')->nullable()->change();
            $table->string('faktur_pajak_file')->nullable()->change();
        });
    }
};
