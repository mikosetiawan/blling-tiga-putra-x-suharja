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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Form --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Info Invoice --}}
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

        {{-- Pelanggan --}}
        <div class="card">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">👤 Pilih Pelanggan</div>
            </div>
            <div class="p-5">
                <label class="form-label">Pelanggan <span class="text-red-400">*</span></label>
                <select name="pelanggan_id" class="form-select" id="pelangganSelect" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($pelanggans as $p)
                    <option value="{{ $p->id }}"
                        data-harga="{{ $p->harga_paket }}"
                        data-paket="{{ $p->paket_label }}"
                        {{ (old('pelanggan_id', $selectedPelanggan?->id) == $p->id) ? 'selected' : '' }}>
                        {{ $p->id_pelanggan }} — {{ $p->nama_perusahaan ?: $p->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
                <div id="pelangganInfo" class="mt-3 p-3 bg-[#161b27] rounded-lg hidden">
                    <div class="text-[12px] text-slate-400">Paket: <span id="infoPacket" class="text-blue-400 font-600"></span></div>
                    <div class="text-[12px] text-slate-400 mt-1">Harga: <span id="infoHarga" class="text-emerald-400 font-600"></span></div>
                </div>
            </div>
        </div>

        {{-- Tagihan --}}
        <div class="card">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">💰 Detail Tagihan</div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jumlah Tagihan (Rp) <span class="text-red-400">*</span></label>
                    <input type="number" name="jumlah" id="jumlahInput" value="{{ old('jumlah', $selectedPelanggan?->harga_paket ?? 0) }}" class="form-input" required min="0" step="1000">
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

    {{-- Preview Invoice --}}
    <div class="lg:col-span-1">
        <div class="card sticky top-20">
            <div class="card-header">
                <div class="text-[15px] font-700 text-slate-200">👁 Preview Invoice</div>
            </div>
            <div class="p-5">
                {{-- Logo header --}}
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
                        <span class="text-slate-300" id="previewPelanggan">—</span>
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
    const sel = document.getElementById('pelangganSelect');
    const opt = sel.options[sel.selectedIndex];
    const jumlah = parseFloat(document.getElementById('jumlahInput').value) || 0;
    const denda = parseFloat(document.getElementById('dendaInput').value) || 0;

    if (sel.value) {
        document.getElementById('infoPacket').textContent = opt.dataset.paket;
        document.getElementById('infoHarga').textContent = fmt(opt.dataset.harga);
        document.getElementById('pelangganInfo').classList.remove('hidden');
        document.getElementById('previewPelanggan').textContent = opt.text.split('—')[1]?.trim() || '';
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

document.getElementById('pelangganSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.dataset.harga) document.getElementById('jumlahInput').value = opt.dataset.harga;
    updatePreview();
});
document.getElementById('jumlahInput').addEventListener('input', updatePreview);
document.getElementById('dendaInput').addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
@endsection