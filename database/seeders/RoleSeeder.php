<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 2 role: admin (akses penuh) dan petugas (input transaksi saja)
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar permission untuk sistem manajemen keuangan
        $permissions = [
            // Transaksi
            'transaksi.view',
            'transaksi.create',
            'transaksi.edit',
            'transaksi.delete',

            // Laporan
            'laporan.view',
            'laporan.export',

            // Anggaran
            'anggaran.view',
            'anggaran.create',
            'anggaran.edit',
            'anggaran.delete',

            // Kategori
            'kategori.view',
            'kategori.create',
            'kategori.edit',
            'kategori.delete',

            // Manajemen User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Role: Admin — akses penuh ke semua fitur
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        // Role: Petugas — hanya bisa input transaksi
        $petugasRole = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $petugasRole->syncPermissions([
            'transaksi.view',
            'transaksi.create',
            'dashboard.view',
        ]);

        $this->command->info('✅ Role Admin dan Petugas berhasil dibuat.');
        $this->command->table(
            ['Role', 'Permissions'],
            [
                ['admin', 'Semua (' . count($permissions) . ' permissions)'],
                ['petugas', 'transaksi.view, transaksi.create, dashboard.view'],
            ]
        );
    }
}
