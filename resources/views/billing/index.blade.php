@extends('layouts.app')
@section('title', 'Tagihan & Invoice')

@section('topbar-actions')
    @if(auth()->user()->canManageBillingInvoices())
    <a href="{{ route('billing.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Invoice
    </a>
    @endif
@endsection

@section('content')
{{-- Summary Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card"><div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">Total Invoice</div><div class="text-2xl font-800 text-white">{{ $billingStats['total'] }}</div></div>
    <div class="stat-card"><div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">Lunas</div><div class="text-2xl font-800 text-emerald-400">{{ $billingStats['lunas'] }}</div></div>
    <div class="stat-card"><div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">Belum Bayar</div><div class="text-2xl font-800 text-red-400">{{ $billingStats['belum_bayar'] }}</div></div>
    <div class="stat-card"><div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">Nilai Pending</div><div class="text-xl font-800 text-amber-400">Rp {{ number_format($billingStats['nilai_pending'],0,',','.') }}</div></div>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="w-48">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="belum_bayar" {{ request('status')=='belum_bayar'?'selected':'' }}>Belum Bayar</option>
                <option value="lunas" {{ request('status')=='lunas'?'selected':'' }}>Lunas</option>
                <option value="sebagian" {{ request('status')=='sebagian'?'selected':'' }}>Sebagian</option>
            </select>
        </div>
        <div class="w-44">
            <label class="form-label">Periode</label>
            <input type="text" name="periode" value="{{ request('periode') }}" class="form-input" placeholder="Cth: April 2026">
        </div>
        @if($pelanggans->isNotEmpty())
        <div class="w-48">
            <label class="form-label">Pelanggan</label>
            <select name="pelanggan_id" class="form-select">
                <option value="">Semua Pelanggan</option>
                @foreach($pelanggans as $p)
                <option value="{{ $p->id }}" {{ request('pelanggan_id')==$p->id?'selected':'' }}>
                    {{ $p->nama_perusahaan ?: $p->nama_lengkap }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Filter
        </button>
        @if(request()->hasAny(['status','periode','pelanggan_id']))
        <a href="{{ route('billing.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header flex items-center justify-between">
        <div>
            <div class="section-title">Daftar Invoice</div>
            <div class="section-sub">{{ $billings->total() }} invoice ditemukan</div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Pelanggan</th>
                    <th>Periode</th>
                    <th>Tgl. Invoice</th>
                    <th>Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($billings as $b)
                <tr>
                    <td class="font-mono text-[12px] text-blue-400">{{ $b->no_invoice }}</td>
                    <td>
                        <div class="text-[13px] font-600 text-slate-200">{{ $b->pelanggan->nama_perusahaan ?: $b->pelanggan->nama_lengkap }}</div>
                        <div class="text-[11px] font-mono text-slate-500">{{ $b->pelanggan->id_pelanggan }}</div>
                    </td>
                    <td class="text-slate-400 text-[13px]">{{ $b->periode }}</td>
                    <td class="text-slate-400">{{ $b->tanggal_invoice?->format('d/m/Y') }}</td>
                    <td class="text-slate-400">{{ $b->jatuh_tempo?->format('d/m/Y') }}</td>
                    <td class="text-slate-200">Rp {{ number_format($b->jumlah,0,',','.') }}</td>
                    <td class="font-700 text-white">Rp {{ number_format($b->total_bayar,0,',','.') }}</td>
                    <td>
                        @php $sb = $b->status_bayar; @endphp
                        <span class="badge badge-{{ $sb==='belum_bayar'?'belum':($sb==='lunas'?'lunas':'sebagian') }}">
                            {{ $b->status_label }}
                        </span>
                    </td>
                    <td>
                        @if($b->bukti_bayar)
                            <a href="{{ Storage::url($b->bukti_bayar) }}" target="_blank" class="text-emerald-400 text-[12px] hover:underline">✓ Ada</a>
                        @else
                            <span class="text-slate-600 text-[12px]">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('billing.show', $b) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if($b->status_bayar === 'belum_bayar' && auth()->user()->canManageBillingInvoices())
                            <form method="POST" action="{{ route('billing.destroy', $b) }}" onsubmit="return confirm('Hapus invoice ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-10 text-slate-500">Belum ada invoice</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($billings->hasPages())
    <div class="p-4 border-t border-[#2a3347]">{{ $billings->withQueryString()->links() }}</div>
    @endif
</div>
@endsection