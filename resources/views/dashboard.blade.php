@extends('layouts.app')
@section('title', 'Dashboard')

@section('topbar-actions')
    @if(auth()->user()->canManagePelangganData())
    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pelanggan
    </a>
    @endif
@endsection

@section('content')
{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    <div class="stat-card col-span-1" style="--c: #3b82f6;">
        <div class="text-[11px] font-600 text-slate-500 uppercase tracking-wider mb-2">Total Pelanggan</div>
        <div class="text-3xl font-800 text-white">{{ number_format($stats['total_pelanggan']) }}</div>
        <div class="text-[12px] text-blue-400 mt-1">{{ $stats['pelanggan_aktif'] }} aktif</div>
    </div>
    <div class="stat-card col-span-1">
        <div class="text-[11px] font-600 text-slate-500 uppercase tracking-wider mb-2">Pendapatan Bulan Ini</div>
        <div class="text-2xl font-800 text-emerald-400">Rp {{ number_format($stats['pendapatan_bulan'], 0, ',', '.') }}</div>
        <div class="text-[12px] text-slate-500 mt-1">{{ now()->isoFormat('MMMM Y') }}</div>
    </div>
    <div class="stat-card col-span-1">
        <div class="text-[11px] font-600 text-slate-500 uppercase tracking-wider mb-2">Belum Bayar</div>
        <div class="text-3xl font-800 text-amber-400">{{ $stats['tagihan_belum_bayar'] }}</div>
        <div class="text-[12px] text-slate-500 mt-1">invoice pending</div>
    </div>
    <div class="stat-card col-span-1">
        <div class="text-[11px] font-600 text-slate-500 uppercase tracking-wider mb-2">Invoice Lunas (Bulan Ini)</div>
        <div class="text-3xl font-800 text-indigo-400">{{ $stats['invoice_lunas_bulan'] }}</div>
        <div class="text-[12px] text-slate-500 mt-1">pembayaran terverifikasi</div>
    </div>
    <div class="stat-card col-span-1">
        <div class="text-[11px] font-600 text-slate-500 uppercase tracking-wider mb-2">Distribusi Paket</div>
        @foreach($paketDistribusi->take(3) as $p)
        <div class="flex justify-between text-[12px] mt-1">
            <span class="text-slate-400">{{ ucfirst(str_replace('mbps',' Mbps',$p->paket)) }}</span>
            <span class="text-white font-600">{{ $p->total }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    {{-- Recent Pelanggan --}}
    <div class="card xl:col-span-1">
        <div class="card-header flex items-center justify-between">
            <div>
                <div class="section-title text-[15px]">Pelanggan Terbaru</div>
            </div>
            <a href="{{ route('pelanggan.index') }}" class="text-[12px] text-blue-400 hover:text-blue-300">Lihat semua →</a>
        </div>
        <div class="divide-y divide-[#2a3347]">
            @forelse($recentPelanggans as $p)
            <div class="flex items-center gap-3 p-4 hover:bg-[#1a2030] transition-colors">
                <div class="w-9 h-9 rounded-xl bg-blue-900/40 flex items-center justify-center text-blue-400 text-[12px] font-700 flex-shrink-0">
                    {{ strtoupper(substr($p->nama_perusahaan ?: $p->nama_lengkap, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-600 text-slate-200 truncate">{{ $p->nama_perusahaan ?: $p->nama_lengkap }}</div>
                    <div class="text-[11px] text-slate-500 font-mono">{{ $p->id_pelanggan }}</div>
                </div>
                <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
            </div>
            @empty
            <div class="p-6 text-center text-slate-500 text-[13px]">Belum ada pelanggan</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Billing --}}
    <div class="card xl:col-span-1">
        <div class="card-header flex items-center justify-between">
            <div class="section-title text-[15px]">Invoice Terbaru</div>
            <a href="{{ route('billing.index') }}" class="text-[12px] text-blue-400 hover:text-blue-300">Lihat semua →</a>
        </div>
        <div class="divide-y divide-[#2a3347]">
            @forelse($recentBillings as $b)
            <div class="flex items-center gap-3 p-4 hover:bg-[#1a2030] transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-600 text-slate-200 truncate">{{ $b->pelanggan->nama_perusahaan ?: $b->pelanggan->nama_lengkap }}</div>
                    <div class="text-[11px] text-slate-500 font-mono">{{ $b->no_invoice }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-[12px] font-700 text-white">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</div>
                    <span class="badge badge-{{ $b->status_bayar === 'belum_bayar' ? 'belum' : ($b->status_bayar === 'lunas' ? 'lunas' : 'sebagian') }} text-[10px]">
                        {{ $b->status_label }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-slate-500 text-[13px]">Belum ada invoice</div>
            @endforelse
        </div>
    </div>

</div>
@endsection