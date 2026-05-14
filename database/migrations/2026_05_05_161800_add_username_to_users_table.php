<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        $users = DB::table('users')->select('id', 'name', 'email')->get();

        foreach ($users as $user) {
            $base = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', strstr($user->email ?? '', '@', true) ?: $user->name ?: 'user'));
            $base = $base ?: 'user';
            $username = $base . $user->id;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
