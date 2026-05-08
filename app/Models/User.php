<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────────

    /**
     * Transaksi yang dimiliki user ini
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Transaksi yang diinput oleh user ini (sebagai created_by)
     */
    public function createdTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /**
     * Hanya user yang aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Hanya user dengan role admin
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    /**
     * Hanya user dengan role petugas
     */
    public function scopePetugas(Builder $query): Builder
    {
        return $query->where('role', 'petugas');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah Petugas
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Label role dalam Bahasa Indonesia
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->role === 'admin' ? 'Administrator' : 'Petugas';
    }
}
