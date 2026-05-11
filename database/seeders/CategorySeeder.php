<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori transaksi keuangan default.
     * PENTING: Tidak ada kategori produk/barang — hanya kategori transaksi keuangan.
     */
    public function run(): void
    {
        $categories = [
            // ── PEMASUKAN ────────────────────────────────────────────
            [
                'name'        => 'Penjualan',
                'type'        => 'pemasukan',
                'is_hpp'      => false,
                'description' => 'Pendapatan dari hasil penjualan produk/jasa',
                'is_active'   => true,
            ],
            [
                'name'        => 'Pendapatan Lain-lain',
                'type'        => 'pemasukan',
                'is_hpp'      => false,
                'description' => 'Pendapatan di luar penjualan utama (bunga, bonus, dll)',
                'is_active'   => true,
            ],

            // ── PENGELUARAN — HPP ─────────────────────────────────────
            [
                'name'        => 'Pembelian Stok / HPP',
                'type'        => 'pengeluaran',
                'is_hpp'      => true,   // ← HPP: harga pokok penjualan
                'description' => 'Pembelian stok/bahan baku yang langsung menjadi HPP (Harga Pokok Penjualan)',
                'is_active'   => true,
            ],

            // ── PENGELUARAN — Biaya Operasional ───────────────────────
            [
                'name'        => 'Biaya Gaji',
                'type'        => 'pengeluaran',
                'is_hpp'      => false,
                'description' => 'Pengeluaran untuk gaji karyawan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Biaya Listrik',
                'type'        => 'pengeluaran',
                'is_hpp'      => false,
                'description' => 'Tagihan listrik bulanan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Biaya Sewa',
                'type'        => 'pengeluaran',
                'is_hpp'      => false,
                'description' => 'Biaya sewa tempat usaha',
                'is_active'   => true,
            ],
            [
                'name'        => 'Biaya Transportasi',
                'type'        => 'pengeluaran',
                'is_hpp'      => false,
                'description' => 'Biaya transportasi dan pengiriman',
                'is_active'   => true,
            ],
            [
                'name'        => 'Biaya Operasional Lain',
                'type'        => 'pengeluaran',
                'is_hpp'      => false,
                'description' => 'Pengeluaran operasional yang tidak termasuk kategori di atas',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Kategori keuangan berhasil dibuat.');
        $this->command->table(
            ['Nama', 'Tipe', 'Is HPP'],
            collect($categories)->map(fn ($c) => [
                $c['name'],
                strtoupper($c['type']),
                $c['is_hpp'] ? '✓ Ya' : '—',
            ])->toArray()
        );
    }
}
