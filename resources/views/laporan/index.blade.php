@extends('layouts.app')
@section('title', 'Laporan & Ekspor')

@section('content')
<div class="section-title mb-1">Laporan & Ekspor Data</div>
<div class="section-sub mb-6">Pilih jenis laporan yang ingin ditampilkan atau diekspor</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
    {{-- Laporan Pelanggan --}}
    <a href="{{ route('laporan.pelanggan') }}" class="card p-6 hover:border-blue-600 transition-colors group cursor-pointer block">
        <div class="w-12 h-12 rounded-2xl bg-blue-900/40 flex items-center justify-center mb-4 group-hover:bg-blue-900/70 transition-colors">
            <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="text-[16px] font-700 text-white mb-1">Laporan Pelanggan</div>
        <div class="text-[13px] text-slate-500 mb-4">Data seluruh pelanggan aktif dan non-aktif beserta detail paket</div>
        <div class="flex gap-2">
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Tampil</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">PDF</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Print</span>
        </div>
    </a>

    {{-- Laporan Billing --}}
    <a href="{{ route('laporan.billing') }}" class="card p-6 hover:border-emerald-600 transition-colors group cursor-pointer block">
        <div class="w-12 h-12 rounded-2xl bg-emerald-900/40 flex items-center justify-center mb-4 group-hover:bg-emerald-900/70 transition-colors">
            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
        </div>
        <div class="text-[16px] font-700 text-white mb-1">Laporan Billing</div>
        <div class="text-[13px] text-slate-500 mb-4">Rekapitulasi tagihan, pembayaran, dan piutang per periode</div>
        <div class="flex gap-2">
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Tampil</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">PDF</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Print</span>
        </div>
    </a>

    {{-- Laporan Helpdesk --}}
    <a href="{{ route('laporan.helpdesk') }}" class="card p-6 hover:border-purple-600 transition-colors group cursor-pointer block">
        <div class="w-12 h-12 rounded-2xl bg-purple-900/40 flex items-center justify-center mb-4 group-hover:bg-purple-900/70 transition-colors">
            <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="text-[16px] font-700 text-white mb-1">Laporan Helpdesk</div>
        <div class="text-[13px] text-slate-500 mb-4">Rekap tiket, kategori gangguan, dan waktu penyelesaian</div>
        <div class="flex gap-2">
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Tampil</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">PDF</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Print</span>
        </div>
    </a>

    {{-- Statistik --}}
    <a href="{{ route('laporan.statistik') }}" class="card p-6 hover:border-amber-600 transition-colors group cursor-pointer block">
        <div class="w-12 h-12 rounded-2xl bg-amber-900/40 flex items-center justify-center mb-4 group-hover:bg-amber-900/70 transition-colors">
            <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="text-[16px] font-700 text-white mb-1">Statistik & Dashboard</div>
        <div class="text-[13px] text-slate-500 mb-4">Grafik distribusi paket, tren pendapatan, dan ringkasan eksekutif</div>
        <div class="flex gap-2">
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Grafik</span>
            <span class="badge" style="background:#1e2535;color:#94a3b8;border:1px solid #2a3347;font-size:11px;">Ringkasan</span>
        </div>
    </a>
</div>

{{-- Quick Stats --}}
@php
$qStats = [
    'Total Pelanggan' => \App\Models\Pelanggan::count(),
    'Pelanggan Aktif' => \App\Models\Pelanggan::where('status','aktif')->count(),
    'Invoice Bulan Ini' => \App\Models\Billing::whereMonth('tanggal_invoice', now()->month)->count(),
    'Total Pendapatan' => 'Rp ' . number_format(\App\Models\Billing::where('status_bayar','lunas')->sum('total_bayar'),0,',','.'),
    'Tiket Open' => \App\Models\Helpdesk::where('status','open')->count(),
    'Tiket Kritis' => \App\Models\Helpdesk::where('prioritas','kritis')->whereNotIn('status',['closed'])->count(),
];
@endphp
<div class="mt-6">
    <div class="text-[13px] font-700 text-slate-500 uppercase tracking-wider mb-3">Ringkasan Saat Ini</div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($qStats as $label => $val)
        <div class="card p-4 text-center">
            <div class="text-[11px] text-slate-500 mb-1">{{ $label }}</div>
            <div class="text-[18px] font-800 text-white">{{ $val }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection