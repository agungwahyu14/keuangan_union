<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Urutan penting: Roles → Categories → Users
     */
    public function run(): void
    {
        // 1. Buat roles & permissions spesifik sistem keuangan
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Buat kategori keuangan default
        $this->call(CategorySeeder::class);

        // 3. Buat user default (Admin & Petugas)
        $this->call(UserSeeder::class);

        $this->command->info('');
        $this->command->info('🎉 Union Authentic siap digunakan!');
        $this->command->info('   Akses: http://127.0.0.1:8000/login');
    }
}
