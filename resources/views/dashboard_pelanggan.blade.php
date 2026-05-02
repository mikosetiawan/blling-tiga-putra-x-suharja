@extends('layouts.app', ['title' => 'Dashboard Pelanggan'])

@section('content')
@if(!$myPelanggan)
    <div class="alert alert-error">
        Data pelanggan tidak ditemukan untuk email ini. Silakan hubungi admin.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="stat-card">
            <div class="text-[13px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tagihan Belum Dibayar</div>
            <div class="text-[32px] font-bold text-white">{{ $stats['tagihan_belum_bayar'] }}</div>
        </div>
        <div class="stat-card">
            <div class="text-[13px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Tagihan</div>
            <div class="text-[32px] font-bold text-white">{{ $stats['total_tagihan'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Tagihan Terbaru -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <div>
                    <h2 class="section-title">Tagihan Terbaru</h2>
                    <div class="section-sub">5 tagihan terakhir Anda</div>
                </div>
                <a href="{{ route('billing.index') }}" class="text-xs text-blue-500 hover:text-blue-400 font-medium">Lihat Semua</a>
            </div>
            <div class="table-wrapper">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Periode</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBillings as $b)
                        <tr>
                            <td class="font-medium text-white">{{ $b->no_invoice }}</td>
                            <td>{{ $b->periode }}</td>
                            <td>Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</td>
                            <td>{!! $b->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-slate-500">Belum ada tagihan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
