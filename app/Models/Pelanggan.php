<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'id_pelanggan', 'nama_lengkap', 'nama_perusahaan', 'no_ktp_nib', 'npwp',
        'no_telepon', 'no_whatsapp', 'email',
        'alamat_lengkap', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'patokan',
        'paket', 'jenis_koneksi', 'kode_paket', 'harga_paket', 'tgl_mulai', 'tgl_jatuh_tempo', 'catatan_paket',
        'status', 'tanggal_daftar',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_jatuh_tempo' => 'date',
        'tanggal_daftar' => 'date',
        'harga_paket' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function helpdesks()
    {
        return $this->hasMany(Helpdesk::class);
    }

    public function getPaketLabelAttribute(): string
    {
        return match($this->paket) {
            '10mbps' => '10 Mbps',
            '20mbps' => '20 Mbps',
            '30mbps' => '30 Mbps',
            '50mbps' => '50 Mbps',
            '100mbps' => '100 Mbps',
            'dedicated' => 'Dedicated',
            default => ucfirst($this->paket),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'aktif' => '<span class="badge-aktif">Aktif</span>',
            'nonaktif' => '<span class="badge-nonaktif">Non-Aktif</span>',
            'suspend' => '<span class="badge-suspend">Suspend</span>',
            default => $this->status,
        };
    }

    public static function generateId(): string
    {
        $year = date('Y');
        $last = static::where('id_pelanggan', 'like', "TPP{$year}-%")->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->id_pelanggan, -3) + 1 : 1;
        return "TPP{$year}-" . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}