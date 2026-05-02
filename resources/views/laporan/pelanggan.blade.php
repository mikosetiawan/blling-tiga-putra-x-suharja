@extends('layouts.app')
@section('title', 'Laporan Data Pelanggan')

@push('styles')
<style>
@media print {
    @page { size: A4 landscape; margin: 1cm; }
    body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .sidebar, .topbar { display: none !important; }
    div.flex-1[style] { margin-left: 0 !important; }
    .card { border: none !important; background: transparent !important; box-shadow: none !important; padding: 0 !important; }
    .grid { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; margin-bottom: 2rem !important; }
    .grid > div { flex: 1 1 0px !important; border: 1px solid #cbd5e1 !important; padding: 10px !important; margin: 0 !important; }
    
    /* Warna Text untuk kertas putih */
    .text-white { color: #000 !important; }
    .text-emerald-400 { color: #059669 !important; }
    .text-blue-400 { color: #2563eb !important; }
    .text-red-400 { color: #dc2626 !important; }
    .text-purple-400 { color: #7e22ce !important; }
    .text-amber-400 { color: #d97706 !important; }
    .text-slate-200, .text-slate-300, .text-slate-400, .text-slate-500 { color: #334155 !important; }
    
    /* Custom Tabel */
    table.tbl { background: white !important; border: 1px solid #cbd5e1 !important; border-collapse: collapse !important;}
    table.tbl th { background: #f1f5f9 !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important; padding: 8px !important; font-size: 11px !important;}
    table.tbl td { color: #000 !important; border: 1px solid #cbd5e1 !important; padding: 8px !important; font-size: 11px !important;}
    .badge { background: transparent !important; color: #000 !important; border: none !important; padding:0 !important; font-weight:600 !important;}
}
</style>
@endpush

@section('topbar-actions')
    <button onclick="window.print()" class="btn btn-secondary no-print">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
    </button>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-primary no-print">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export PDF
    </a>
@endsection

@section('content')
<div class="mb-4 no-print">
    <a href="{{ route('laporan.index') }}" class="text-[13px] text-slate-500 hover:text-slate-300">← Kembali ke Laporan</a>
</div>

{{-- Filter --}}
<div class="card mb-5 no-print">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Non-Aktif</option>
                <option value="suspend" {{ request('status')=='suspend'?'selected':'' }}>Suspend</option>
            </select>
        </div>
        <div class="w-44">
            <label class="form-label">Paket</label>
            <select name="paket" class="form-select">
                <option value="">Semua Paket</option>
                @foreach(['dedicated'=>'Dedicated','100mbps'=>'100 Mbps','50mbps'=>'50 Mbps','30mbps'=>'30 Mbps','20mbps'=>'20 Mbps','10mbps'=>'10 Mbps'] as $v=>$l)
                <option value="{{ $v }}" {{ request('paket')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-input w-40">
        </div>
        <div>
            <label class="form-label">Sampai</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-input w-40">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['status','paket','dari','sampai']))
        <a href="{{ route('laporan.pelanggan') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    @php $sumItems = [
        ['Total','text-white',$summary['total']],
        ['Aktif','text-emerald-400',$summary['aktif']],
        ['Non-Aktif','text-slate-400',$summary['nonaktif']],
        ['Suspend','text-red-400',$summary['suspend']],
        ['Dedicated','text-blue-400',$summary['dedicated']],
        ['100 Mbps','text-purple-400',$summary['100mbps']],
    ]; @endphp
    @foreach($sumItems as [$label, $color, $val])
    <div class="card p-4 text-center">
        <div class="text-[11px] text-slate-500">{{ $label }}</div>
        <div class="text-[22px] font-800 {{ $color }}">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Report Header (for print) --}}
<div class="hidden print:block mb-6 pb-4 border-b border-gray-300">
    <div class="text-[18px] font-bold text-center">LAPORAN DATA PELANGGAN LAYANAN INTERNET</div>
    <div class="text-[14px] text-center">PT. Tiga Putra Pandawa</div>
    <div class="text-[12px] text-center text-gray-500">Periode: {{ now()->isoFormat('MMMM Y') }} — Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header flex items-center justify-between no-print">
        <div>
            <div class="section-title">Laporan Data Pelanggan</div>
            <div class="section-sub">Periode: {{ now()->isoFormat('MMMM Y') }} — {{ $pelanggans->count() }} data</div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pelanggan</th>
                    <th>Nama / Perusahaan</th>
                    <th>No. Telepon</th>
                    <th>Paket</th>
                    <th>Jenis Koneksi</th>
                    <th>Harga</th>
                    <th>Tgl. Daftar</th>
                    <th>Jatuh Tempo</th>
                    <th>Status Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggans as $i => $p)
                <tr>
                    <td class="text-slate-500">{{ $i + 1 }}</td>
                    <td class="font-mono text-[12px] text-blue-400">{{ $p->id_pelanggan }}</td>
                    <td>
                        <div class="font-600 text-slate-200">{{ $p->nama_perusahaan ?: $p->nama_lengkap }}</div>
                        @if($p->nama_perusahaan)<div class="text-[11px] text-slate-500">{{ $p->nama_lengkap }}</div>@endif
                    </td>
                    <td class="text-slate-400">{{ $p->no_telepon }}</td>
                    <td><span class="badge" style="background:#1e3a5f;color:#60a5fa;border:1px solid #1d4ed8;">{{ $p->paket_label }}</span></td>
                    <td class="text-slate-400 text-[12px]">{{ ucfirst(str_replace('_',' ',$p->jenis_koneksi)) }}</td>
                    <td class="text-slate-200">Rp {{ number_format($p->harga_paket,0,',','.') }}</td>
                    <td class="text-slate-400">{{ $p->tanggal_daftar?->format('d/m/Y') }}</td>
                    <td class="text-slate-400">{{ $p->tgl_jatuh_tempo?->format('d') ?? '-' }}</td>
                    <td>
                        @php $lastBilling = $p->billings()->latest()->first(); @endphp
                        @if($lastBilling)
                        <span class="badge badge-{{ $lastBilling->status_bayar==='belum_bayar'?'belum':($lastBilling->status_bayar==='lunas'?'lunas':'sebagian') }}">
                            {{ $lastBilling->status_label }}
                        </span>
                        @else<span class="text-slate-600">—</span>@endif
                    </td>
                    <td><span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center py-10 text-slate-500">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-[#2a3347] text-[12px] text-slate-500 flex justify-between no-print">
        <span>Total {{ $pelanggans->count() }} pelanggan</span>
        <span>Laporan dibuat: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}</span>
    </div>
</div>
@endsection