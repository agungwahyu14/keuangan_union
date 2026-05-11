<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'reference_number',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // ─── Relasi ───────────────────────────────────────────────────

    /**
     * Transaksi milik seorang user (pemilik transaksi)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaksi dibuat oleh user tertentu (petugas/admin yang input)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Transaksi masuk dalam kategori tertentu
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────────

    /**
     * Filter berdasarkan rentang tanggal
     * Digunakan untuk laporan Arus Kas dan Laba Rugi
     */
    public function scopeFilterByDateRange(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    /**
     * Filter berdasarkan tipe transaksi (pemasukan / pengeluaran)
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Hanya transaksi kategori HPP (Harga Pokok Penjualan / Pembelian Stok)
     * Digunakan untuk kalkulasi Laba Rugi Sederhana
     */
    public function scopeHPP(Builder $query): Builder
    {
        return $query->whereHas('category', fn ($q) => $q->where('is_hpp', true));
    }

    /**
    * Pengeluaran non-HPP (biaya operasional)
    */
    public function scopeBiayaOperasional(Builder $query): Builder
    {
        return $query->where('type', 'pengeluaran')
                     ->whereHas('category', fn ($q) => $q->where('is_hpp', false));
    }

    /**
     * Hanya transaksi pemasukan
     */
    public function scopePemasukan(Builder $query): Builder
    {
        return $query->where('type', 'pemasukan');
    }

    /**
     * Hanya transaksi pengeluaran
     */
    public function scopePengeluaran(Builder $query): Builder
    {
        return $query->where('type', 'pengeluaran');
    }

    // ─── Helper Kalkulasi Laporan ─────────────────────────────────

    /**
     * Format amount ke Rupiah
     */
    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Label tipe dalam Bahasa Indonesia
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
    }
}
