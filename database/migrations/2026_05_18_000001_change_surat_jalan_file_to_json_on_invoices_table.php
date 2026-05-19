<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $invoices = DB::table('invoices')->whereNotNull('surat_jalan_file')->get();

        foreach ($invoices as $invoice) {
            $value = $invoice->surat_jalan_file;

            if ($value && ! str_starts_with(trim($value), '[')) {
                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update(['surat_jalan_file' => json_encode([$value])]);
            }
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->json('surat_jalan_file')->nullable()->change();
        });
    }

    public function down(): void
    {
        $invoices = DB::table('invoices')->whereNotNull('surat_jalan_file')->get();

        foreach ($invoices as $invoice) {
            $decoded = json_decode($invoice->surat_jalan_file, true);

            if (is_array($decoded) && count($decoded) > 0) {
                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update(['surat_jalan_file' => $decoded[0]]);
            }
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('surat_jalan_file')->nullable()->change();
        });
    }
};
