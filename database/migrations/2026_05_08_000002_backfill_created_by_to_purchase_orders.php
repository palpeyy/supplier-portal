<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill created_by untuk PO yang sudah ada
        // Gunakan user pertama dengan role Admin atau Dept. Head
        $adminUser = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['Admin', 'Dept. Head']);
        })->first();

        if ($adminUser) {
            DB::table('purchase_orders')
                ->whereNull('created_by')
                ->update(['created_by' => $adminUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('purchase_orders')->update(['created_by' => null]);
    }
};
