@extends('layouts.app')
@section('title', 'Buat Invoice Baru')

@section('content')
<form method="POST" action="{{ route('billing.store') }}">
@csrf

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="section-title">Buat Invoice Baru</div>
        <div class="section-sub">Penagihan Internet — PT. Tiga Putra Pandawa</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('billing.index') }}" class="btn btn-secondary">← Kembali</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Buat Invoice
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error mb-4">
    <div><div class="font-600">Terdapat kesalahan:</div><ul class="mt-1 text-[13px] list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
</div>
@endif

@if($pelanggans->isEmpty())
<div class="alert alert-error mb-4">
    Belum ada pelanggan aktif dengan <strong class="text-slate-300">akun portal</strong> (terhubung ke user). Tambahkan data pelanggan di menu Data Pelanggan dan pilih akun pengguna yang belum punya data pelanggan.
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="card">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">📄 Informasi Invoice</div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">No. Invoice (otomatis)</label>
                    <input type="text" class="form-input bg-[#161b27] text-slate-400 font-mono" value="{{ $nextInvoice }}" readonly>
                </div>
                <div>
                    <label class="form-label">Periode <span class="text-red-400">*</span></label>
                    <input type="text" name="periode" value="{{ old('periode', now()->isoFormat('MMMM Y')) }}" class="form-input" placeholder="Cth: April 2026" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Invoice <span class="text-red-400">*</span></label>
                    <input type="date" name="tanggal_invoice" value="{{ old('tanggal_invoice', date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Jatuh Tempo <span class="text-red-400">*</span></label>
                    <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', date('Y-m-', strtotime('+14 days')) . date('d', strtotime('+14 days'))) }}" class="form-input" required>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">👤 Pelanggan (Data Master + Portal)</div>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-[12px] text-slate-500 leading-relaxed">
                    Hanya pelanggan <strong class="text-slate-400">aktif</strong> yang sudah punya <strong class="text-slate-400">akun portal</strong> (user terhubung) dapat ditagih. Akun portal dibuat lewat menu Data Pelanggan.
                </p>
                <div>
                    <label class="form-label">Pelanggan <span class="text-red-400">*</span></label>
                    <select name="pelanggan_id" class="form-select" id="billingPelangganSelect" required {{ $pelanggans->isEmpty() ? 'disabled' : '' }}>
                        <option value="">-- Pilih pelanggan --</option>
                        @foreach($pelanggans as $p)
                        <option value="{{ $p->id }}"
                            data-nama="{{ $p->nama_perusahaan ?: $p->nama_lengkap }}"
                            data-akun="{{ $p->user?->name }} — {{ $p->user?->email }}"
                            data-harga="{{ $p->harga_paket ?? 0 }}"
                            data-paket="{{ $p->paket_label }}"
                            data-idpel="{{ $p->id_pelanggan }}"
                            {{ (int) old('pelanggan_id', $selectedPelanggan?->id ?? 0) === (int) $p->id ? 'selected' : '' }}>
                            {{ $p->id_pelanggan }} — {{ $p->nama_perusahaan ?: $p->nama_lengkap }} ({{ $p->user?->email }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="pelangganInfo" class="mt-1 p-3 bg-[#161b27] rounded-lg hidden">
                    <div class="text-[12px] text-slate-400">Akun portal: <span id="infoAkun" class="text-slate-200 font-500"></span></div>
                    <div class="text-[12px] text-slate-400 mt-1">ID Pelanggan: <span id="infoIdPel" class="text-slate-200 font-mono font-600"></span></div>
                    <div class="text-[12px] text-slate-400 mt-1">Paket: <span id="infoPacket" class="text-blue-400 font-600"></span></div>
                    <div class="text-[12px] text-slate-400 mt-1">Harga paket: <span id="infoHarga" class="text-emerald-400 font-600"></span></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">💰 Detail Tagihan</div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jumlah Tagihan (Rp) <span class="text-red-400">*</span></label>
                    @php
                        $defaultJumlah = old('jumlah');
                        if ($defaultJumlah === null && $selectedPelanggan) {
                            $defaultJumlah = $selectedPelanggan->harga_paket ?? 0;
                        }
                        $defaultJumlah = $defaultJumlah ?? 0;
                    @endphp
                    <input type="number" name="jumlah" id="jumlahInput" value="{{ $defaultJumlah }}" class="form-input" required min="0" step="1000">
                </div>
                <div>
                    <label class="form-label">Denda / Biaya Tambahan (Rp)</label>
                    <input type="number" name="denda" id="dendaInput" value="{{ old('denda', 0) }}" class="form-input" min="0" step="1000">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-input" placeholder="Cth: Tagihan internet bulan April 2026">
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="card sticky top-20">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">👁 Preview Invoice</div>
            </div>
            <div class="p-5">
                <div class="border border-[#2a3347] rounded-lg p-4 mb-4" style="background:#161b27;">
                    <div class="text-[11px] font-800 text-slate-300 mb-1">PT. TIGA PUTRA PANDAWA</div>
                    <div class="text-[10px] text-slate-500">Jl. KH. Yasin Beiji, Krakatau Junction Ruko No. 21 & 23</div>
                    <div class="text-[10px] text-slate-500">Tel: +62-818-693-617 | www.3pp.co.id</div>
                </div>
                <div class="space-y-2 text-[13px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Invoice</span>
                        <span class="font-mono text-blue-400 text-[12px]">{{ $nextInvoice }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kepada</span>
                        <span class="text-slate-300 text-right max-w-[60%]" id="previewPelanggan">—</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paket</span>
                        <span class="text-blue-400" id="previewPaket">—</span>
                    </div>
                    <div class="border-t border-[#2a3347] my-3"></div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tagihan</span>
                        <span class="text-slate-300" id="previewJumlah">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Denda</span>
                        <span class="text-slate-300" id="previewDenda">Rp 0</span>
                    </div>
                    <div class="border-t border-[#2a3347] my-3"></div>
                    <div class="flex justify-between text-[15px] font-800">
                        <span class="text-slate-300">Total</span>
                        <span class="text-emerald-400" id="previewTotal">Rp 0</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-[#2a3347] text-center text-[10px] text-slate-600 italic">
                    Satu Solusi Tiga Layanan Andal
                </div>
            </div>
        </div>
    </div>
</div>

</form>

@push('scripts')
<script>
const fmt = n => 'Rp ' + parseInt(n||0).toLocaleString('id-ID');

function updatePreview() {
    const sel = document.getElementById('billingPelangganSelect');
    const opt = sel.options[sel.selectedIndex];
    const jumlah = parseFloat(document.getElementById('jumlahInput').value) || 0;
    const denda = parseFloat(document.getElementById('dendaInput').value) || 0;

    if (sel.value) {
        document.getElementById('infoAkun').textContent = opt.dataset.akun || '—';
        document.getElementById('infoIdPel').textContent = opt.dataset.idpel || '—';
        document.getElementById('infoPacket').textContent = opt.dataset.paket;
        document.getElementById('infoHarga').textContent = fmt(opt.dataset.harga);
        document.getElementById('pelangganInfo').classList.remove('hidden');
        document.getElementById('previewPelanggan').textContent = opt.dataset.nama || '';
        document.getElementById('previewPaket').textContent = opt.dataset.paket;
    } else {
        document.getElementById('pelangganInfo').classList.add('hidden');
        document.getElementById('previewPelanggan').textContent = '—';
        document.getElementById('previewPaket').textContent = '—';
    }

    document.getElementById('previewJumlah').textContent = fmt(jumlah);
    document.getElementById('previewDenda').textContent = fmt(denda);
    document.getElementById('previewTotal').textContent = fmt(jumlah + denda);
}

document.getElementById('billingPelangganSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.value && opt.dataset.harga !== undefined) {
        document.getElementById('jumlahInput').value = opt.dataset.harga;
    }
    updatePreview();
});
document.getElementById('jumlahInput').addEventListener('input', updatePreview);
document.getElementById('dendaInput').addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
@endsection
