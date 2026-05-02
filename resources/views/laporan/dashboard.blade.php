@extends('layouts.app')
@section('title', 'Statistik & Dashboard')

@section('content')
<div class="mb-4">
    <a href="{{ route('laporan.index') }}" class="text-[13px] text-slate-500 hover:text-slate-300">← Kembali ke Laporan</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    {{-- KPI Cards --}}
    <div class="lg:col-span-3 grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php $kpis = [
            ['Total Pelanggan','text-white',$stats['total_pelanggan'],'👥'],
            ['Pelanggan Aktif','text-emerald-400',$stats['pelanggan_aktif'],'✅'],
            ['Pendapatan Tahun Ini','text-blue-400','Rp '.number_format($stats['pendapatan_bulan'],0,',','.'),'💰'],
            ['Invoice Lunas (Tahun Ini)','text-indigo-400',$stats['invoice_lunas_tahun'],'✓'],
            ['Invoice Pending','text-amber-400',$stats['tagihan_belum_bayar'],'📄'],
        ]; @endphp
        @foreach($kpis as [$label,$color,$val,$icon])
        <div class="stat-card text-center">
            <div class="text-2xl mb-1">{{ $icon }}</div>
            <div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">{{ $label }}</div>
            <div class="text-xl font-800 {{ $color }}">{{ $val }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Distribusi Paket --}}
    <div class="card">
        <div class="card-header">
            <div class="section-title text-[15px]">Distribusi Paket Internet</div>
        </div>
        <div class="p-5">
            @php $totalP = $paketDistribusi->sum('total'); @endphp
            @foreach($paketDistribusi as $p)
            @php
                $pct = $totalP > 0 ? round(($p->total / $totalP) * 100) : 0;
                $colors = ['dedicated'=>'#3b82f6','100mbps'=>'#8b5cf6','50mbps'=>'#10b981','30mbps'=>'#f59e0b','20mbps'=>'#ef4444','10mbps'=>'#6b7280'];
                $c = $colors[$p->paket] ?? '#94a3b8';
                $lbl = ['dedicated'=>'Dedicated','100mbps'=>'100 Mbps','50mbps'=>'50 Mbps','30mbps'=>'30 Mbps','20mbps'=>'20 Mbps','10mbps'=>'10 Mbps'][$p->paket] ?? $p->paket;
            @endphp
            <div class="mb-4">
                <div class="flex justify-between text-[13px] mb-1.5">
                    <span class="text-slate-300 font-500">{{ $lbl }}</span>
                    <span class="text-slate-400">{{ $p->total }} pelanggan ({{ $pct }}%)</span>
                </div>
                <div class="h-2.5 bg-[#161b27] rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%;background:{{ $c }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tren Billing --}}
    <div class="card">
        <div class="card-header">
            <div class="section-title text-[15px]">Tren Pendapatan (6 Bulan Terakhir)</div>
        </div>
        <div class="p-5">
            @if($billingBulanan->count() > 0)
            @php $maxVal = $billingBulanan->max('total'); @endphp
            @foreach($billingBulanan as $b)
            @php $pct2 = $maxVal > 0 ? round(($b->total / $maxVal) * 100) : 0; @endphp
            <div class="mb-4">
                <div class="flex justify-between text-[13px] mb-1.5">
                    <span class="text-slate-300">{{ $b->periode }}</span>
                    <span class="text-emerald-400 font-600">Rp {{ number_format($b->total,0,',','.') }}</span>
                </div>
                <div class="h-2.5 bg-[#161b27] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width:{{ $pct2 }}%;"></div>
                </div>
                <div class="text-[11px] text-slate-600 mt-0.5">{{ $b->jumlah }} invoice lunas</div>
            </div>
            @endforeach
            @else
            <div class="text-center text-slate-500 py-10">Belum ada data billing</div>
            @endif
        </div>
    </div>

    {{-- Status Pelanggan --}}
    <div class="card">
        <div class="card-header">
            <div class="section-title text-[15px]">Status Pelanggan</div>
        </div>
        <div class="p-5">
            @php
            $statusData = \App\Models\Pelanggan::selectRaw('status, count(*) as total')->groupBy('status')->get();
            $totalStatus = $statusData->sum('total');
            $statusColors = ['aktif'=>'#10b981','nonaktif'=>'#6b7280','suspend'=>'#ef4444'];
            $statusLabels = ['aktif'=>'Aktif','nonaktif'=>'Non-Aktif','suspend'=>'Suspend'];
            @endphp
            @foreach($statusData as $s)
            @php $pctS = $totalStatus > 0 ? round(($s->total / $totalStatus) * 100) : 0; @endphp
            <div class="mb-4">
                <div class="flex justify-between text-[13px] mb-1.5">
                    <span class="text-slate-300">{{ $statusLabels[$s->status] ?? $s->status }}</span>
                    <span class="font-600" style="color:{{ $statusColors[$s->status] ?? '#94a3b8' }}">{{ $s->total }} ({{ $pctS }}%)</span>
                </div>
                <div class="h-2.5 bg-[#161b27] rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ $pctS }}%;background:{{ $statusColors[$s->status] ?? '#94a3b8' }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Footer Info --}}
<div class="mt-5 text-[12px] text-slate-600 text-center">
    Data statistik per {{ now()->format('d M Y, H:i') }} — PT. Tiga Putra Pandawa
</div>
@endsection