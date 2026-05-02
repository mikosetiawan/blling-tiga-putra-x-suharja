@extends('layouts.app')
@section('title', 'Detail Invoice — ' . $billing->no_invoice)

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }
    body {
        background: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Hilangkan sidebar, topbar, & kolom aksi */
    .sidebar, .topbar, .space-y-4 { display: none !important; }
    
    /* Lebar penuh untuk margin kiri sidebar */
    div.flex-1[style] { margin-left: 0 !important; }
    
    /* Tampilan grid khusus cetak full 1 kolom */
    .lg\:col-span-2 { width: 100% !important; }
    
    /* Matikan background card invoice */
    #invoice-print { 
        border: none !important; 
        background: transparent !important;
        padding: 0 !important;
    }

    /* Ubah wana text untuk di atas kertas putih */
    #invoice-print .text-white { color: #000 !important; }
    #invoice-print .text-slate-300, 
    #invoice-print .text-slate-400, 
    #invoice-print .text-slate-500 { color: #334155 !important; }
    #invoice-print .border-\[\#2a3347\] { border-color: #cbd5e1 !important; }
    
    /* Warna aksen text khusus print */
    #invoice-print .text-blue-400 { color: #2563eb !important; }
    #invoice-print .text-emerald-400 { color: #059669 !important; }
    #invoice-print .text-red-400 { color: #dc2626 !important; }

    /* Modifikasi tabel invoice untuk print */
    #invoice-print table.tbl {
        background: white !important;
        border: 1px solid #cbd5e1 !important;
    }
    #invoice-print table.tbl th {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }
    #invoice-print table.tbl td {
        color: #000 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    
    /* Badge status untuk cetak */
    .badge {
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
    }
}
</style>
@endpush

@section('topbar-actions')
    <button onclick="window.print()" class="btn btn-secondary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Invoice
    </button>
@endsection

@section('content')
<div class="mb-5 no-print">
    <a href="{{ route('billing.index') }}" class="text-[13px] text-slate-500 hover:text-slate-300">← Kembali ke Daftar Invoice</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Invoice Preview --}}
    <div class="lg:col-span-2">
        <div class="card" id="invoice-print">
            <div class="p-6">
                {{-- Header Invoice --}}
                <div class="flex items-start justify-between mb-6 pb-6 border-b border-[#2a3347]">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('images/logo-3pp.jpeg') }}" alt="Logo 3PP" class="w-14 h-14 object-contain rounded-xl" />
                            <div>
                                <div class="text-[16px] font-800 text-white">PT. Tiga Putra Pandawa</div>
                                <div class="text-[11px] text-slate-500">Satu Solusi Tiga Layanan Andal</div>
                            </div>
                        </div>
                        <div class="text-[12px] text-slate-500">Jl. KH. Yasin Beiji, Krakatau Junction Ruko No. 21 & 23</div>
                        <div class="text-[12px] text-slate-500">Cilegon, Banten</div>
                        <div class="text-[12px] text-slate-500">Tel: +62-818-693-617 | support@3pp.co.id</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] text-slate-500 uppercase tracking-wider">Invoice</div>
                        <div class="text-[18px] font-800 text-blue-400 font-mono">{{ $billing->no_invoice }}</div>
                        <div class="mt-2 text-[12px] text-slate-500">Tgl: {{ $billing->tanggal_invoice?->format('d M Y') }}</div>
                        <div class="text-[12px] text-slate-500">Jatuh Tempo: {{ $billing->jatuh_tempo?->format('d M Y') }}</div>
                    </div>
                </div>

                {{-- Kepada --}}
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="text-[11px] font-700 text-slate-500 uppercase tracking-wider mb-2">Kepada</div>
                        <div class="text-[15px] font-700 text-white">{{ $billing->pelanggan->nama_perusahaan ?: $billing->pelanggan->nama_lengkap }}</div>
                        @if($billing->pelanggan->nama_perusahaan)
                        <div class="text-[13px] text-slate-400">{{ $billing->pelanggan->nama_lengkap }}</div>
                        @endif
                        <div class="text-[12px] text-slate-500 mt-1">{{ $billing->pelanggan->alamat_lengkap }}</div>
                        <div class="text-[12px] text-slate-500">{{ $billing->pelanggan->no_telepon }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-700 text-slate-500 uppercase tracking-wider mb-2">Keterangan</div>
                        <div class="text-[13px] text-slate-300">Penagihan Internet</div>
                        <div class="text-[12px] text-slate-500 mt-1">Periode: {{ $billing->periode }}</div>
                        <div class="text-[12px] text-slate-500">ID: {{ $billing->pelanggan->id_pelanggan }}</div>
                    </div>
                </div>

                {{-- Table --}}
                <table class="tbl mb-6" style="background:#161b27;border-radius:10px;overflow:hidden;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Paket Internet</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{ $billing->keterangan ?: 'Tagihan Internet Bulan ' . $billing->periode }}</td>
                            <td>{{ $billing->pelanggan->paket_label }}</td>
                            <td class="text-right font-700 text-white">Rp {{ number_format($billing->jumlah,0,',','.') }}</td>
                        </tr>
                        @if($billing->denda > 0)
                        <tr>
                            <td>2</td>
                            <td>Denda Keterlambatan</td>
                            <td>—</td>
                            <td class="text-right text-red-400">Rp {{ number_format($billing->denda,0,',','.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-700 text-slate-400 text-[13px]">Total</td>
                            <td class="text-right font-800 text-emerald-400 text-[16px]">Rp {{ number_format($billing->total_bayar,0,',','.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Status Bayar --}}
                <div class="flex items-center justify-between pt-4 border-t border-[#2a3347]">
                    <div class="text-[12px] text-slate-500 italic">Tanggal {{ $billing->tanggal_invoice?->format('d.m.Y') }}</div>
                    <div class="flex items-center gap-3">
                        @php $sb = $billing->status_bayar; @endphp
                        <span class="badge badge-{{ $sb==='belum_bayar'?'belum':($sb==='lunas'?'lunas':'sebagian') }} text-[13px] px-4 py-1.5">
                            {{ $billing->status_label }}
                        </span>
                        @if($billing->tanggal_bayar)
                        <div class="text-[12px] text-slate-500">Dibayar: {{ $billing->tanggal_bayar->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Actions --}}
    <div class="space-y-4">
        @php
            $isOwnInvoice = $billing->pelanggan && $billing->pelanggan->email === auth()->user()->email;
            $canUploadBukti = $billing->status_bayar !== 'lunas' && (
                (auth()->user()->isPelanggan() && $isOwnInvoice)
                || auth()->user()->canManageBillingInvoices()
            );
        @endphp
        {{-- Upload Bukti --}}
        @if($canUploadBukti)
        <div class="card">
            <div class="card-header">
                <div class="text-[14px] font-700 text-slate-300">📎 Upload Bukti Pembayaran</div>
            </div>
            <form method="POST" action="{{ route('billing.upload-bukti', $billing) }}" enctype="multipart/form-data" class="p-4 space-y-3">
                @csrf
                <div>
                    <label class="form-label">Metode Pembayaran <span class="text-red-400">*</span></label>
                    <select name="metode_bayar" class="form-select" required>
                        <option value="">Pilih Metode</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Cash">Cash / Tunai</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Bayar <span class="text-red-400">*</span></label>
                    <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Bukti Pembayaran <span class="text-red-400">*</span></label>
                    <div class="mt-1 border-2 border-dashed border-[#2a3347] rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition-colors" onclick="document.getElementById('buktiFile').click()">
                        <svg class="w-8 h-8 text-slate-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="text-[12px] text-slate-500" id="fileLabel">JPG, PNG, atau PDF (maks. 5MB)</div>
                        <input type="file" name="bukti_bayar" id="buktiFile" class="hidden" accept=".jpg,.jpeg,.png,.pdf" required
                            onchange="document.getElementById('fileLabel').textContent = this.files[0]?.name || 'Pilih file...'">
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-full justify-center">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Konfirmasi Pembayaran
                </button>
            </form>
        </div>
        @elseif($billing->status_bayar !== 'lunas')
        <div class="card">
            <div class="card-header">
                <div class="text-[14px] font-700 text-slate-300">📎 Pembayaran</div>
            </div>
            <div class="p-4 text-[13px] text-slate-400">
                Invoice belum lunas. Konfirmasi pembayaran dengan bukti dapat dilakukan oleh pelanggan terkait atau admin.
            </div>
        </div>
        @else
        {{-- Bukti sudah ada --}}
        <div class="card">
            <div class="card-header">
                <div class="text-[14px] font-700 text-emerald-400">✅ Pembayaran Terverifikasi</div>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex justify-between text-[13px]"><span class="text-slate-500">Metode</span><span class="text-slate-300">{{ $billing->metode_bayar }}</span></div>
                <div class="flex justify-between text-[13px]"><span class="text-slate-500">Tgl. Bayar</span><span class="text-slate-300">{{ $billing->tanggal_bayar?->format('d M Y') }}</span></div>
                @if($billing->verifiedBy)
                <div class="flex justify-between text-[13px]"><span class="text-slate-500">Diverifikasi</span><span class="text-slate-300">{{ $billing->verifiedBy->name }}</span></div>
                @endif
                @if($billing->bukti_bayar)
                <a href="{{ Storage::url($billing->bukti_bayar) }}" target="_blank" class="btn btn-secondary w-full justify-center mt-2">
                    📄 Lihat Bukti Bayar
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Info Pelanggan --}}
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">👤 Info Pelanggan</div></div>
            <div class="p-4 space-y-2">
                @if(auth()->user()->canViewPelanggan())
                <a href="{{ route('pelanggan.show', $billing->pelanggan) }}" class="font-600 text-blue-400 hover:underline text-[14px]">
                    {{ $billing->pelanggan->nama_perusahaan ?: $billing->pelanggan->nama_lengkap }}
                </a>
                @else
                <span class="font-600 text-slate-200 text-[14px]">
                    {{ $billing->pelanggan->nama_perusahaan ?: $billing->pelanggan->nama_lengkap }}
                </span>
                @endif
                <div class="text-[12px] font-mono text-slate-500">{{ $billing->pelanggan->id_pelanggan }}</div>
                <div class="flex justify-between text-[12px] mt-2"><span class="text-slate-500">Paket</span><span class="text-blue-400">{{ $billing->pelanggan->paket_label }}</span></div>
                <div class="flex justify-between text-[12px]"><span class="text-slate-500">Harga</span><span class="text-emerald-400">Rp {{ number_format($billing->pelanggan->harga_paket,0,',','.') }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection