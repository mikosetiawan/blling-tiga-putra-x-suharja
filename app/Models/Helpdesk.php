<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helpdesk extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_tiket', 'pelanggan_id', 'pelapor', 'no_telepon_pelapor',
        'kategori', 'prioritas', 'status', 'deskripsi', 'solusi',
        'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function generateTiketNo(): string
    {
        $prefix = 'TKT/' . date('Y') . '/' . date('m') . '/';
        $last = static::where('no_tiket', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->no_tiket, -4) + 1 : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'gangguan_koneksi' => 'Gangguan Koneksi',
            'lambat' => 'Koneksi Lambat',
            'putus_nyambung' => 'Putus Nyambung',
            'tidak_bisa_akses' => 'Tidak Bisa Akses',
            'ganti_password_wifi' => 'Ganti Password WiFi',
            'relokasi' => 'Relokasi',
            'upgrade_paket' => 'Upgrade Paket',
            'downgrade_paket' => 'Downgrade Paket',
            'pertanyaan_billing' => 'Pertanyaan Billing',
            'lainnya' => 'Lainnya',
            default => $this->kategori,
        };
    }

    public function getPrioritasColorAttribute(): string
    {
        return match($this->prioritas) {
            'rendah' => 'text-emerald-400',
            'sedang' => 'text-amber-400',
            'tinggi' => 'text-orange-400',
            'kritis' => 'text-red-400',
            default => 'text-slate-400',
        };
    }
}