<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_hpp',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_hpp'    => 'boolean',
        'is_active' => 'boolean',
    ];

    // ─── Relasi ───────────────────────────────────────────────────

    /**
     * Kategori memiliki banyak transaksi
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────────

    /**
     * Hanya kategori yang aktif
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter kategori berdasarkan tipe
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Hanya kategori HPP (Harga Pokok Penjualan)
     */
    public function scopeHPP(Builder $query): Builder
    {
        return $query->where('is_hpp', true);
    }

    // ─── Helper ───────────────────────────────────────────────────

    /**
     * Label warna berdasarkan tipe transaksi
     */
    public function getBadgeColorAttribute(): string
    {
        return $this->type === 'pemasukan' ? 'green' : 'red';
    }

    /**
     * Label tipe dalam Bahasa Indonesia
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
    }
}
