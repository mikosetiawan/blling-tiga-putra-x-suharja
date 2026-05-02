@extends('layouts.app')
@section('title', 'Buat Tiket Helpdesk')

@section('content')
<form method="POST" action="{{ route('helpdesk.store') }}">
@csrf

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="section-title">Buat Tiket Helpdesk Baru</div>
        <div class="section-sub">No. Tiket: <span class="font-mono text-purple-400">{{ $nextTiket }}</span></div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('helpdesk.index') }}" class="btn btn-secondary">← Kembali</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Buat Tiket
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error mb-4">
    <div><div class="font-600">Terdapat kesalahan:</div><ul class="mt-1 text-[13px] list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Pelanggan & Pelapor --}}
    <div class="card">
        <div class="card-header"><div class="text-[15px] font-700 text-slate-200">👤 Pelanggan & Pelapor</div></div>
        <div class="p-5 space-y-4">
            <div>
                <label class="form-label">Pelanggan <span class="text-red-400">*</span></label>
                <select name="pelanggan_id" class="form-select" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($pelanggans as $p)
                    <option value="{{ $p->id }}" {{ old('pelanggan_id', request('pelanggan_id'))==$p->id?'selected':'' }}>
                        {{ $p->id_pelanggan }} — {{ $p->nama_perusahaan ?: $p->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Nama Pelapor <span class="text-red-400">*</span></label>
                <input type="text" name="pelapor" value="{{ old('pelapor') }}" class="form-input" placeholder="Nama orang yang melapor" required>
            </div>
            <div>
                <label class="form-label">No. Telepon Pelapor <span class="text-red-400">*</span></label>
                <input type="text" name="no_telepon_pelapor" value="{{ old('no_telepon_pelapor') }}" class="form-input" placeholder="08xx-xxxx-xxxx" required>
            </div>
        </div>
    </div>

    {{-- Klasifikasi --}}
    <div class="card">
        <div class="card-header"><div class="text-[15px] font-700 text-slate-200">🏷️ Klasifikasi Tiket</div></div>
        <div class="p-5 space-y-4">
            <div>
                <label class="form-label">Kategori Masalah <span class="text-red-400">*</span></label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoriOptions as $val => $label)
                    <option value="{{ $val }}" {{ old('kategori')==$val?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Prioritas <span class="text-red-400">*</span></label>
                <div class="grid grid-cols-4 gap-2 mt-1">
                    @foreach(['rendah'=>['Rendah','border-emerald-700 peer-checked:bg-[#052e16] peer-checked:border-emerald-500 peer-checked:text-emerald-300'],
                               'sedang'=>['Sedang','border-amber-700 peer-checked:bg-[#1c1407] peer-checked:border-amber-500 peer-checked:text-amber-300'],
                               'tinggi'=>['Tinggi','border-orange-700 peer-checked:bg-[#1c0d02] peer-checked:border-orange-500 peer-checked:text-orange-300'],
                               'kritis'=>['Kritis','border-red-700 peer-checked:bg-[#2d1515] peer-checked:border-red-500 peer-checked:text-red-300']] as $val=>[$label,$cls])
                    <label class="cursor-pointer">
                        <input type="radio" name="prioritas" value="{{ $val }}" {{ old('prioritas','sedang')==$val?'checked':'' }} class="sr-only peer" required>
                        <div class="text-center px-2 py-2 rounded-lg border border-[#2a3347] text-[12px] font-700 text-slate-500 {{ $cls }} hover:border-slate-500 transition-all">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="form-label">Ditugaskan Kepada</label>
                <select name="assigned_to" class="form-select">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="card lg:col-span-2">
        <div class="card-header"><div class="text-[15px] font-700 text-slate-200">📝 Deskripsi Masalah</div></div>
        <div class="p-5">
            <textarea name="deskripsi" rows="5" class="form-textarea" placeholder="Jelaskan masalah secara detail: kapan terjadi, gejala yang dialami, sudah dicoba apa..." required>{{ old('deskripsi') }}</textarea>
        </div>
    </div>
</div>

<div class="flex justify-end gap-3 mt-5">
    <a href="{{ route('helpdesk.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Buat Tiket
    </button>
</div>
</form>
@endsection