@extends('layouts.app')
@section('title', 'Helpdesk')

@section('topbar-actions')
    <a href="{{ route('helpdesk.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Tiket
    </a>
@endsection

@section('content')
{{-- Status Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $statusMap = [
        'open' => ['Open','text-indigo-400','badge-open'],
        'in_progress' => ['In Progress','text-amber-400','badge-progress'],
        'resolved' => ['Resolved','text-emerald-400','badge-resolved'],
        'closed' => ['Closed','text-slate-400','badge-closed'],
    ];
    @endphp
    @foreach($statusMap as $key => [$label, $color, $badge])
    <div class="stat-card">
        <div class="text-[11px] text-slate-500 uppercase tracking-wider mb-1">{{ $label }}</div>
        <div class="text-3xl font-800 {{ $color }}">{{ $stats[$key] }}</div>
        <div class="text-[12px] text-slate-500 mt-1">tiket</div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="No. tiket, pelanggan, pelapor...">
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            </select>
        </div>
        <div class="w-40">
            <label class="form-label">Prioritas</label>
            <select name="prioritas" class="form-select">
                <option value="">Semua</option>
                <option value="kritis" {{ request('prioritas')=='kritis'?'selected':'' }}>🔴 Kritis</option>
                <option value="tinggi" {{ request('prioritas')=='tinggi'?'selected':'' }}>🟠 Tinggi</option>
                <option value="sedang" {{ request('prioritas')=='sedang'?'selected':'' }}>🟡 Sedang</option>
                <option value="rendah" {{ request('prioritas')=='rendah'?'selected':'' }}>🟢 Rendah</option>
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">Semua Kategori</option>
                <option value="gangguan_koneksi" {{ request('kategori')=='gangguan_koneksi'?'selected':'' }}>Gangguan Koneksi</option>
                <option value="lambat" {{ request('kategori')=='lambat'?'selected':'' }}>Koneksi Lambat</option>
                <option value="putus_nyambung" {{ request('kategori')=='putus_nyambung'?'selected':'' }}>Putus Nyambung</option>
                <option value="tidak_bisa_akses" {{ request('kategori')=='tidak_bisa_akses'?'selected':'' }}>Tidak Bisa Akses</option>
                <option value="ganti_password_wifi" {{ request('kategori')=='ganti_password_wifi'?'selected':'' }}>Ganti Password WiFi</option>
                <option value="relokasi" {{ request('kategori')=='relokasi'?'selected':'' }}>Relokasi</option>
                <option value="upgrade_paket" {{ request('kategori')=='upgrade_paket'?'selected':'' }}>Upgrade Paket</option>
                <option value="downgrade_paket" {{ request('kategori')=='downgrade_paket'?'selected':'' }}>Downgrade Paket</option>
                <option value="pertanyaan_billing" {{ request('kategori')=='pertanyaan_billing'?'selected':'' }}>Pertanyaan Billing</option>
                <option value="lainnya" {{ request('kategori')=='lainnya'?'selected':'' }}>Lainnya</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Filter
        </button>
        @if(request()->hasAny(['search','status','prioritas','kategori']))
        <a href="{{ route('helpdesk.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header flex items-center justify-between">
        <div>
            <div class="section-title">Daftar Tiket Helpdesk</div>
            <div class="section-sub">{{ $helpdesks->total() }} tiket ditemukan</div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Pelanggan</th>
                    <th>Pelapor</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Assign To</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($helpdesks as $h)
                <tr>
                    <td class="font-mono text-[12px] text-purple-400">{{ $h->no_tiket }}</td>
                    <td>
                        <div class="text-[13px] font-600 text-slate-200">{{ $h->pelanggan->nama_perusahaan ?: $h->pelanggan->nama_lengkap }}</div>
                        <div class="text-[11px] font-mono text-slate-500">{{ $h->pelanggan->id_pelanggan }}</div>
                    </td>
                    <td>
                        <div class="text-[13px] text-slate-300">{{ $h->pelapor }}</div>
                        <div class="text-[11px] text-slate-500">{{ $h->no_telepon_pelapor }}</div>
                    </td>
                    <td class="text-[13px] text-slate-400">{{ $h->kategori_label }}</td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            @php $dots = ['rendah'=>'🟢','sedang'=>'🟡','tinggi'=>'🟠','kritis'=>'🔴']; @endphp
                            <span>{{ $dots[$h->prioritas] ?? '' }}</span>
                            <span class="prio-{{ $h->prioritas }} font-600 text-[13px]">{{ ucfirst($h->prioritas) }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $h->status==='in_progress'?'progress':$h->status }}">
                            {{ ucfirst(str_replace('_',' ',$h->status)) }}
                        </span>
                    </td>
                    <td class="text-slate-400 text-[13px]">{{ $h->assignedTo?->name ?? '—' }}</td>
                    <td class="text-slate-400">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex items-center justify-center">
                            <a href="{{ route('helpdesk.show', $h) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-10 text-slate-500">Tidak ada tiket helpdesk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($helpdesks->hasPages())
    <div class="p-4 border-t border-[#2a3347]">{{ $helpdesks->withQueryString()->links() }}</div>
    @endif
</div>
@endsection