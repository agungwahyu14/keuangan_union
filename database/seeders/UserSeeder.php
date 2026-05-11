<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed user default sistem manajemen keuangan.
     * Pastikan RolesAndPermissionsSeeder sudah dijalankan sebelumnya.
     */
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@unionauthentic.com'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('Admin@123'),
                'role'      => 'admin',
                'is_active' => true,
                'phone'     => null,
            ]
        );
        $admin->syncRoles('admin');

        // ── Petugas contoh ────────────────────────────────────────────────
        $petugas = User::firstOrCreate(
            ['email' => 'petugas@unionauthentic.com'],
            [
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('Petugas@123'),
                'role'      => 'petugas',
                'is_active' => true,
                'phone'     => null,
            ]
        );
        $petugas->syncRoles('petugas');

        $this->command->info('');
        $this->command->info('User default berhasil dibuat.');
        $this->command->table(
            ['Nama', 'Email', 'Password', 'Role'],
            [
                ['Administrator', 'admin@unionauthentic.com',   'Admin@123',   'admin'],
                ['Budi Santoso',  'petugas@unionauthentic.com', 'Petugas@123', 'petugas'],
            ]
        );
    }
}
