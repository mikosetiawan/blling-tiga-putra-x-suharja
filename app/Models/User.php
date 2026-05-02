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
    public function bilingsVerified()
    {
        return $this->hasMany(Billing::class, 'verified_by');
    }

    public function helpdesksAssigned()
    {
        return $this->hasMany(Helpdesk::class, 'assigned_to');
    }

    // ── Helper ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTeknisi(): bool
    {
        return $this->hasRole('teknisi');
    }

    public function getAvatarInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}