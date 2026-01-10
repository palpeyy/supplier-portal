<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default roles
        $adminRole = \App\Models\Role::create([
            'name' => 'Admin',
            'description' => 'Administrator - Full Access',
        ]);

        $supplierRole = \App\Models\Role::create([
            'name' => 'Supplier',
            'description' => 'Supplier - Limited Access',
        ]);

        $staffRole = \App\Models\Role::create([
            'name' => 'Staff',
            'description' => 'Staff - View Only',
        ]);

        // Create a test admin user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        // Create a test supplier user
        \App\Models\User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $supplierRole->id,
        ]);

        // Create a test staff user
        \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole->id,
        ]);
    }
}
