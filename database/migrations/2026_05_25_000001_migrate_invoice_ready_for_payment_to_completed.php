<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('invoices')
            ->where('status', 'ready_for_payment')
            ->update(['status' => 'completed']);
    }

    public function down(): void
    {
        // Tidak mengembalikan status lama secara otomatis.
    }
};
