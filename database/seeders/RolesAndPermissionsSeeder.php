<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles dan permissions untuk sistem manajemen keuangan.
     *
     * ADMIN      — akses penuh ke semua fitur
     * PETUGAS    — hanya input & lihat transaksi milik sendiri
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Daftar Permissions ────────────────────────────────────────────
        $permissions = [
            'manage-users',          // Tambah, edit, nonaktifkan user
            'manage-transactions',   // Tambah & update transaksi
            'delete-transactions',   // Hapus transaksi (soft delete)
            'manage-categories',     // CRUD kategori transaksi
            'view-reports',          // Akses laporan Arus Kas & Laba Rugi
            'export-reports',        // Export laporan ke Excel & PDF
            'view-dashboard-full',   // Dashboard lengkap dengan ringkasan keuangan
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Role: Admin — semua permission ───────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        // ── Role: Petugas — hanya input transaksi ────────────────────────
        $petugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $petugas->syncPermissions([
            'manage-transactions',  // Bisa create & update transaksi sendiri
        ]);

        $this->command->info('');
        $this->command->info('✅ Roles & Permissions berhasil dikonfigurasi.');
        $this->command->table(
            ['Role', 'Permissions'],
            [
                ['admin',   implode(', ', $permissions)],
                ['petugas', 'manage-transactions'],
            ]
        );
    }
}
