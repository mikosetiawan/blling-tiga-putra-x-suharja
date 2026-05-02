@extends('layouts.app')
@section('title', 'Data Pelanggan')

@section('topbar-actions')
    @if(auth()->user()->canManagePelangganData())
    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pelanggan
    </a>
    @endif
@endsection

@section('content')
{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="form-label">Cari Pelanggan</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama, ID, telepon...">
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Non-Aktif</option>
                <option value="suspend" {{ request('status')=='suspend'?'selected':'' }}>Suspend</option>
            </select>
        </div>
        <div class="w-44">
            <label class="form-label">Paket</label>
            <select name="paket" class="form-select">
                <option value="">Semua Paket</option>
                <option value="dedicated" {{ request('paket')=='dedicated'?'selected':'' }}>Dedicated</option>
                <option value="100mbps" {{ request('paket')=='100mbps'?'selected':'' }}>100 Mbps</option>
                <option value="50mbps" {{ request('paket')=='50mbps'?'selected':'' }}>50 Mbps</option>
                <option value="30mbps" {{ request('paket')=='30mbps'?'selected':'' }}>30 Mbps</option>
                <option value="20mbps" {{ request('paket')=='20mbps'?'selected':'' }}>20 Mbps</option>
                <option value="10mbps" {{ request('paket')=='10mbps'?'selected':'' }}>10 Mbps</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari
        </button>
        @if(request()->hasAny(['search','status','paket']))
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header flex items-center justify-between">
        <div>
            <div class="section-title">Daftar Pelanggan</div>
            <div class="section-sub">Total {{ $pelanggans->total() }} pelanggan terdaftar</div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>ID Pelanggan</th>
                    <th>Nama / Perusahaan</th>
                    <th>Akun portal</th>
                    <th>No. Telepon</th>
                    <th>Paket</th>
                    <th>Tgl. Daftar</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggans as $p)
                <tr>
                    <td>
                        <span class="font-mono text-[12px] text-blue-400">{{ $p->id_pelanggan }}</span>
                    </td>
                    <td>
                        <div class="font-600 text-slate-200">{{ $p->nama_perusahaan ?: $p->nama_lengkap }}</div>
                        @if($p->nama_perusahaan)
                        <div class="text-[12px] text-slate-500">{{ $p->nama_lengkap }}</div>
                        @endif
                    </td>
                    <td class="text-[12px]">
                        @if($p->user)
                            <div class="text-slate-200 font-500">{{ $p->user->name }}</div>
                            <div class="text-slate-500 font-mono">{{ $p->user->email }}</div>
                        @else
                            <span class="text-amber-500/90">Belum terhubung</span>
                        @endif
                    </td>
                    <td class="text-slate-400">{{ $p->no_telepon }}</td>
                    <td>
                        <span class="badge" style="background:#1e3a5f;color:#60a5fa;border:1px solid #1d4ed8;">
                            {{ $p->paket_label }}
                        </span>
                    </td>
                    <td class="text-slate-400">{{ $p->tanggal_daftar?->format('d/m/Y') }}</td>
                    <td class="text-slate-400">{{ $p->tgl_jatuh_tempo?->format('d') ?? '-' }} tiap bulan</td>
                    <td>
                        <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('pelanggan.show', $p) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if(auth()->user()->canManagePelangganData())
                            <a href="{{ route('pelanggan.edit', $p) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('pelanggan.destroy', $p) }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-10 text-slate-500">
                        Belum ada data pelanggan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pelanggans->hasPages())
    <div class="p-4 border-t border-[#2a3347]">
        {{ $pelanggans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection