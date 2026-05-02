@extends('layouts.app')
@section('title', 'Laporan Helpdesk')

@push('styles')
<style>
@media print {
    @page { size: A4 landscape; margin: 1cm; }
    body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .sidebar, .topbar { display: none !important; }
    div.flex-1[style] { margin-left: 0 !important; }
    .card { border: none !important; background: transparent !important; box-shadow: none !important; padding: 0 !important; }
    .grid { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; margin-bottom: 2rem !important; }
    .grid > div { flex: 1 1 0px !important; border: 1px solid #cbd5e1 !important; padding: 10px !important; margin: 0 !important; border-radius: 8px;}
    
    /* Warna Text untuk kertas putih */
    .text-white { color: #000 !important; }
    .text-emerald-400 { color: #059669 !important; }
    .text-blue-400 { color: #2563eb !important; }
    .text-red-400 { color: #dc2626 !important; }
    .text-indigo-400 { color: #4338ca !important; }
    .text-purple-400 { color: #7e22ce !important; }
    .text-amber-400 { color: #d97706 !important; }
    .text-slate-200, .text-slate-300, .text-slate-400, .text-slate-500, .text-slate-600 { color: #334155 !important; }
    
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
    <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-primary no-print">
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
        <div class="w-40"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            </select>
        </div>
        <div class="w-40"><label class="form-label">Prioritas</label>
            <select name="prioritas" class="form-select">
                <option value="">Semua</option>
                <option value="kritis" {{ request('prioritas')=='kritis'?'selected':'' }}>Kritis</option>
                <option value="tinggi" {{ request('prioritas')=='tinggi'?'selected':'' }}>Tinggi</option>
                <option value="sedang" {{ request('prioritas')=='sedang'?'selected':'' }}>Sedang</option>
                <option value="rendah" {{ request('prioritas')=='rendah'?'selected':'' }}>Rendah</option>
            </select>
        </div>
        <div><label class="form-label">Dari</label><input type="date" name="dari" value="{{ request('dari') }}" class="form-input w-40"></div>
        <div><label class="form-label">Sampai</label><input type="date" name="sampai" value="{{ request('sampai') }}" class="form-input w-40"></div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['status','prioritas','dari','sampai']))
        <a href="{{ route('laporan.helpdesk') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    @php $sumItems=[['Total','text-white',$summary['total']],['Open','text-indigo-400',$summary['open']],['In Progress','text-amber-400',$summary['in_progress']],['Resolved','text-emerald-400',$summary['resolved']],['Closed','text-slate-400',$summary['closed']],['Kritis','text-red-400',$summary['kritis']]]; @endphp
    @foreach($sumItems as [$l,$c,$v])
    <div class="card p-4 text-center"><div class="text-[11px] text-slate-500">{{ $l }}</div><div class="text-2xl font-800 {{ $c }}">{{ $v }}</div></div>
    @endforeach
</div>

{{-- Report Header (for print) --}}
<div class="hidden print:block mb-6 pb-4 border-b border-gray-300">
    <div class="text-[18px] font-bold text-center text-black">LAPORAN DATA TIKET HELPDESK</div>
    <div class="text-[14px] text-center text-black">PT. Tiga Putra Pandawa</div>
    <div class="text-[12px] text-center text-gray-500">Periode: {{ now()->isoFormat('MMMM Y') }} — Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header no-print">
        <div class="section-title">Laporan Tiket Helpdesk</div>
        <div class="section-sub">{{ $helpdesks->count() }} tiket ditemukan</div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Tiket</th>
                    <th>Pelanggan</th>
                    <th>Pelapor</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Teknisi</th>
                    <th>Dibuat</th>
                    <th>Selesai</th>
                    <th>Durasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($helpdesks as $i => $h)
                <tr>
                    <td class="text-slate-500">{{ $i+1 }}</td>
                    <td class="font-mono text-[12px] text-purple-400">{{ $h->no_tiket }}</td>
                    <td>
                        <div class="font-600 text-slate-200 text-[13px]">{{ $h->pelanggan->nama_perusahaan ?: $h->pelanggan->nama_lengkap }}</div>
                        <div class="font-mono text-[11px] text-slate-500">{{ $h->pelanggan->id_pelanggan }}</div>
                    </td>
                    <td class="text-slate-400 text-[13px]">{{ $h->pelapor }}</td>
                    <td class="text-slate-400 text-[12px]">{{ $h->kategori_label }}</td>
                    <td>
                        @php $dots=['rendah'=>'🟢','sedang'=>'🟡','tinggi'=>'🟠','kritis'=>'🔴']; @endphp
                        <span class="prio-{{ $h->prioritas }} font-600 text-[13px]">{{ $dots[$h->prioritas]??'' }} {{ ucfirst($h->prioritas) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $h->status==='in_progress'?'progress':$h->status }}">{{ ucfirst(str_replace('_',' ',$h->status)) }}</span>
                    </td>
                    <td class="text-slate-400 text-[12px]">{{ $h->assignedTo?->name ?? '—' }}</td>
                    <td class="text-slate-400">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-slate-400">{{ $h->resolved_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-slate-400 text-[12px]">
                        @if($h->resolved_at)
                            {{ $h->created_at->diffInHours($h->resolved_at) }}j
                        @else
                            <span class="text-amber-400">Aktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center py-10 text-slate-500">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-[#2a3347] text-[12px] text-slate-500 flex justify-between no-print">
        <span>Total {{ $helpdesks->count() }} tiket</span>
        <span>Laporan dibuat: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}</span>
    </div>
</div>
@endsection