<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('pelanggan')) {
            $myPelanggan = Pelanggan::where('email', auth()->user()->email)->first();
            
            if ($myPelanggan) {
                $stats = [
                    'tagihan_belum_bayar' => Billing::where('pelanggan_id', $myPelanggan->id)->where('status_bayar', 'belum_bayar')->count(),
                    'total_tagihan' => Billing::where('pelanggan_id', $myPelanggan->id)->count(),
                ];
                $recentBillings = Billing::where('pelanggan_id', $myPelanggan->id)->latest()->limit(5)->get();
            } else {
                $stats = ['tagihan_belum_bayar' => 0, 'total_tagihan' => 0];
                $recentBillings = collect();
            }

            return view('dashboard_pelanggan', compact('stats', 'recentBillings', 'myPelanggan'));
        }

        $stats = [
            'total_pelanggan' => Pelanggan::count(),
            'pelanggan_aktif' => Pelanggan::where('status', 'aktif')->count(),
            'pendapatan_bulan' => Billing::where('status_bayar', 'lunas')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('total_bayar'),
            'tagihan_belum_bayar' => Billing::where('status_bayar', 'belum_bayar')->count(),
            'invoice_lunas_bulan' => Billing::where('status_bayar', 'lunas')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->count(),
        ];

        $recentPelanggans = Pelanggan::latest()->limit(5)->get();
        $recentBillings = Billing::with('pelanggan')->latest()->limit(5)->get();

        $paketDistribusi = Pelanggan::selectRaw('paket, count(*) as total')
            ->groupBy('paket')
            ->orderByDesc('total')
            ->get();

        return view('dashboard', compact('stats', 'recentPelanggans', 'recentBillings', 'paketDistribusi'));
    }
}