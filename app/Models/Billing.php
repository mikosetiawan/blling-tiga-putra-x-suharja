<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice', 'pelanggan_id', 'tanggal_invoice', 'jatuh_tempo',
        'tanggal_bayar', 'jumlah', 'denda', 'total_bayar',
        'status_bayar', 'metode_bayar', 'bukti_bayar', 'keterangan',
        'periode', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'verified_at' => 'datetime',
        'jumlah' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateInvoiceNo(): string
    {
        $prefix = 'INV/' . date('Y') . '/' . date('m') . '/';
        $last = static::where('no_invoice', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->no_invoice, -4) + 1 : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_bayar) {
            'belum_bayar' => 'Belum Bayar',
            'lunas' => 'Lunas',
            'sebagian' => 'Sebagian',
            default => $this->status_bayar,
        };
    }
}