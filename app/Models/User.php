<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class);
    }

    public function bilingsVerified()
    {
        return $this->hasMany(Billing::class, 'verified_by');
    }

    // ── Helper ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPelanggan(): bool
    {
        return $this->hasRole('pelanggan');
    }

    public function canManageUsers(): bool
    {
        return $this->hasRole('admin');
    }

    public function canViewReports(): bool
    {
        return $this->hasRole('admin');
    }

    /** CRUD data pelanggan (tambah / edit / hapus). */
    public function canManagePelangganData(): bool
    {
        return $this->hasRole('admin');
    }

    /** Lihat daftar & detail pelanggan. */
    public function canViewPelanggan(): bool
    {
        return $this->hasRole('admin');
    }

    /** Buat / hapus invoice, filter semua pelanggan. */
    public function canManageBillingInvoices(): bool
    {
        return $this->hasRole('admin');
    }

    public function getAvatarInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}