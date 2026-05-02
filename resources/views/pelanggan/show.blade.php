@extends('layouts.app')
@section('title', 'Detail Pelanggan — ' . $pelanggan->id_pelanggan)

@section('topbar-actions')
    @if(auth()->user()->canManagePelangganData())
    <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn btn-secondary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
    @endif
    @if(auth()->user()->canManageBillingInvoices() && $pelanggan->user_id)
    <a href="{{ route('billing.create', ['pelanggan_id' => $pelanggan->id]) }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Invoice
    </a>
    @endif
@endsection

@section('content')
<div class="mb-5">
    <a href="{{ route('pelanggan.index') }}" class="text-[13px] text-slate-500 hover:text-slate-300">← Kembali ke Daftar Pelanggan</a>
</div>

@if($pelanggan->user)
<div class="card mb-5 p-4 border border-sky-900/50 bg-sky-950/20">
    <div class="text-[12px] font-700 text-sky-300 uppercase tracking-wide mb-2">Akun portal (login &amp; tagihan)</div>
    <div class="flex flex-wrap items-center gap-4 text-[13px]">
        <span class="text-slate-200 font-600">{{ $pelanggan->user->name }}</span>
        <span class="text-slate-400 font-mono">{{ $pelanggan->user->email }}</span>
    </div>
</div>
@else
<div class="alert alert-error mb-5">
    Belum ada akun portal yang terhubung. Tagihan hanya dapat dibuat setelah pelanggan dihubungkan ke user di form Data Pelanggan (data lama perlu diperbaiki oleh admin).
</div>
@endif

{{-- Header Card --}}
<div class="card mb-5 p-5">
    <div class="flex items-start gap-5">
        <div class="w-16 h-16 rounded-2xl bg-blue-900/50 border border-blue-700/30 flex items-center justify-center text-2xl font-800 text-blue-300 flex-shrink-0">
            {{ strtoupper(substr($pelanggan->nama_perusahaan ?: $pelanggan->nama_lengkap, 0, 2)) }}
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-xl font-800 text-white">{{ $pelanggan->nama_perusahaan ?: $pelanggan->nama_lengkap }}</h2>
                <span class="badge badge-{{ $pelanggan->status }}">{{ ucfirst($pelanggan->status) }}</span>
                <span class="badge" style="background:#1e3a5f;color:#60a5fa;border:1px solid #1d4ed8;">{{ $pelanggan->paket_label }}</span>
            </div>
            @if($pelanggan->nama_perusahaan)
            <div class="text-[14px] text-slate-400 mt-1">{{ $pelanggan->nama_lengkap }}</div>
            @endif
            <div class="flex flex-wrap gap-4 mt-3 text-[13px] text-slate-400">
                <span class="font-mono text-blue-400 font-600">{{ $pelanggan->id_pelanggan }}</span>
                <span>📞 {{ $pelanggan->no_telepon }}</span>
                @if($pelanggan->email)<span>✉️ {{ $pelanggan->email }}</span>@endif
                <span>📅 Daftar {{ $pelanggan->tanggal_daftar?->format('d M Y') }}</span>
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <div class="text-[12px] text-slate-500">Harga Paket</div>
            <div class="text-2xl font-800 text-emerald-400">Rp {{ number_format($pelanggan->harga_paket, 0, ',', '.') }}</div>
            <div class="text-[12px] text-slate-500">/bulan</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Info Pribadi --}}
    <div class="card">
        <div class="card-header">
            <div class="text-[14px] font-700 text-slate-300">📋 Data Pribadi</div>
        </div>
        <div class="p-4 space-y-3">
            @php
            $rows = [
                'No. KTP/NIB' => $pelanggan->no_ktp_nib,
                'NPWP' => $pelanggan->npwp,
                'No. WhatsApp' => $pelanggan->no_whatsapp,
                'Email' => $pelanggan->email,
            ];
            @endphp
            @foreach($rows as $label => $val)
            @if($val)
            <div class="flex justify-between gap-3">
                <span class="text-[12px] text-slate-500">{{ $label }}</span>
                <span class="text-[13px] text-slate-300 text-right font-500">{{ $val }}</span>
            </div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Alamat --}}
    <div class="card">
        <div class="card-header">
            <div class="text-[14px] font-700 text-slate-300">📍 Alamat Pemasangan</div>
        </div>
        <div class="p-4">
            <div class="text-[13px] text-slate-300 leading-relaxed">{{ $pelanggan->alamat_lengkap }}</div>
            <div class="mt-2 text-[12px] text-slate-500">
                @if($pelanggan->rt) RT {{ $pelanggan->rt }}/{{ $pelanggan->rw }}, @endif
                @if($pelanggan->kelurahan) Kel. {{ $pelanggan->kelurahan }}, @endif
                @if($pelanggan->kecamatan) Kec. {{ $pelanggan->kecamatan }}<br>@endif
                @if($pelanggan->kota) {{ $pelanggan->kota }}, @endif
                @if($pelanggan->provinsi) {{ $pelanggan->provinsi }} @endif
                @if($pelanggan->kode_pos) {{ $pelanggan->kode_pos }} @endif
            </div>
            @if($pelanggan->patokan)
            <div class="mt-2 text-[12px] text-amber-400/80">📌 {{ $pelanggan->patokan }}</div>
            @endif
        </div>
    </div>

    {{-- Paket --}}
    <div class="card">
        <div class="card-header">
            <div class="text-[14px] font-700 text-slate-300">📡 Detail Paket</div>
        </div>
        <div class="p-4 space-y-3">
            @php
            $paketRows = [
                'Paket' => $pelanggan->paket_label,
                'Jenis Koneksi' => ucfirst(str_replace('_', ' ', $pelanggan->jenis_koneksi)),
                'Kode Paket' => $pelanggan->kode_paket,
                'Mulai Berlangganan' => $pelanggan->tgl_mulai?->format('d M Y'),
                'Jatuh Tempo' => $pelanggan->tgl_jatuh_tempo?->format('d') ? 'Tanggal ' . $pelanggan->tgl_jatuh_tempo->format('d') . ' tiap bulan' : null,
            ];
            @endphp
            @foreach($paketRows as $label => $val)
            @if($val)
            <div class="flex justify-between gap-3">
                <span class="text-[12px] text-slate-500">{{ $label }}</span>
                <span class="text-[13px] text-slate-300 text-right font-500">{{ $val }}</span>
            </div>
            @endif
            @endforeach
            @if($pelanggan->catatan_paket)
            <div class="mt-2 p-3 bg-[#161b27] rounded-lg text-[12px] text-slate-400">
                {{ $pelanggan->catatan_paket }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Riwayat Tagihan --}}
<div class="card mb-5">
    <div class="card-header flex items-center justify-between">
        <div class="text-[15px] font-700 text-slate-200">💰 Riwayat Tagihan</div>
        @if(auth()->user()->canManageBillingInvoices())
        <a href="{{ route('billing.create', ['pelanggan_id' => $pelanggan->id]) }}" class="btn btn-primary btn-sm">+ Invoice Baru</a>
        @endif
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Periode</th>
                    <th>Tgl. Invoice</th>
                    <th>Jumlah</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Tgl. Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggan->billings as $b)
                <tr>
                    <td class="font-mono text-[12px] text-blue-400">{{ $b->no_invoice }}</td>
                    <td class="text-slate-400">{{ $b->periode }}</td>
                    <td class="text-slate-400">{{ $b->tanggal_invoice?->format('d/m/Y') }}</td>
                    <td class="text-slate-200">Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                    <td class="font-700 text-white">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</td>
                    <td>
                        @php $sb = $b->status_bayar; @endphp
                        <span class="badge badge-{{ $sb === 'belum_bayar' ? 'belum' : ($sb === 'lunas' ? 'lunas' : 'sebagian') }}">
                            {{ $b->status_label }}
                        </span>
                    </td>
                    <td class="text-slate-400">{{ $b->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                    <td><a href="{{ route('billing.show', $b) }}" class="btn btn-secondary btn-sm">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-slate-500 py-8">Belum ada tagihan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection