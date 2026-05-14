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

        $deptHeadRole = \App\Models\Role::create([
            'name' => 'Dept. Head',
            'description' => 'Department Head - Can approve PO',
        ]);

        // Create a test supplier
        $supplier = \App\Models\Supplier::create([
            'nama' => 'PT Supplier Utama',
            'alamat' => 'Jl. Industri No. 123, Jakarta',
            'pic' => 'John Doe',
            'telephone' => '021-1234567',
            'contact_person' => 'Bagian Purchasing',
        ]);

        // Create a test admin user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        // Create a test supplier user (linked with supplier)
        \App\Models\User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $supplierRole->id,
            'supplier_id' => $supplier->id,
        ]);

        // Create a test staff user
        \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole->id,
        ]);

        // Create a test dept head user
        \App\Models\User::create([
            'name' => 'Dept. Head User',
            'email' => 'depthead@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $deptHeadRole->id,
        ]);
    }
}
