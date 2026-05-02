@extends('layouts.app')
@section('title', 'Edit Pelanggan — ' . $pelanggan->id_pelanggan)

@section('content')
<form method="POST" action="{{ route('pelanggan.update', $pelanggan) }}">
@csrf @method('PUT')

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="section-title">Edit Data Pelanggan</div>
        <div class="section-sub font-mono text-blue-400">{{ $pelanggan->id_pelanggan }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('pelanggan.show', $pelanggan) }}" class="btn btn-secondary">← Kembali</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Perubahan
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error mb-4">
    <div>
        <div class="font-600">Terdapat kesalahan:</div>
        <ul class="mt-1 text-[13px] list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

@if($pelanggan->user)
<div class="card mb-5 p-4 border border-sky-900/40">
    <div class="text-[12px] font-700 text-sky-300 uppercase tracking-wide mb-2">Akun portal terhubung</div>
    <div class="text-[13px] text-slate-300">{{ $pelanggan->user->name }} <span class="text-slate-500">—</span> <span class="font-mono text-slate-400">{{ $pelanggan->user->email }}</span></div>
    <p class="text-[11px] text-slate-500 mt-2">Email di bawah disinkronkan ke akun login saat Anda menyimpan.</p>
</div>
@endif

{{-- Status --}}
<div class="card mb-5 p-4">
    <div class="flex items-center gap-6">
        <div class="text-[13px] font-600 text-slate-400">Status Pelanggan:</div>
        @foreach(['aktif' => ['Aktif','badge-aktif'], 'nonaktif' => ['Non-Aktif','badge-nonaktif'], 'suspend' => ['Suspend','badge-suspend']] as $val => [$label, $cls])
        <label class="cursor-pointer flex items-center gap-2">
            <input type="radio" name="status" value="{{ $val }}" {{ old('status', $pelanggan->status)==$val?'checked':'' }} class="sr-only peer">
            <div class="px-4 py-2 rounded-lg border border-[#2a3347] text-[13px] font-600 text-slate-400 peer-checked:border-blue-500 peer-checked:bg-[#1e2535] peer-checked:text-white transition-all">
                {{ $label }}
            </div>
        </label>
        @endforeach
    </div>
</div>

{{-- A. DATA PRIBADI --}}
<div class="card mb-5">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-[11px] font-bold text-white">A</div>
            <div class="section-title text-[15px]">Data Pribadi Pelanggan</div>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label class="form-label">ID Pelanggan</label>
            <input type="text" class="form-input bg-[#161b27] text-slate-400" value="{{ $pelanggan->id_pelanggan }}" readonly>
        </div>
        <div>
            <label class="form-label">Tanggal Daftar</label>
            <input type="text" class="form-input bg-[#161b27] text-slate-400" value="{{ $pelanggan->tanggal_daftar?->format('d/m/Y') }}" readonly>
        </div>
        <div>
            <label class="form-label">Nama Lengkap <span class="text-red-400">*</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pelanggan->nama_lengkap) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Nama Perusahaan</label>
            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $pelanggan->nama_perusahaan) }}" class="form-input">
        </div>
        <div>
            <label class="form-label">No. KTP / NIB</label>
            <input type="text" name="no_ktp_nib" value="{{ old('no_ktp_nib', $pelanggan->no_ktp_nib) }}" class="form-input">
        </div>
        <div>
            <label class="form-label">NPWP</label>
            <input type="text" name="npwp" value="{{ old('npwp', $pelanggan->npwp) }}" class="form-input">
        </div>
        <div>
            <label class="form-label">No. Telepon <span class="text-red-400">*</span></label>
            <input type="text" name="no_telepon" value="{{ old('no_telepon', $pelanggan->no_telepon) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">No. WhatsApp</label>
            <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp', $pelanggan->no_whatsapp) }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Email <span class="text-slate-500">(akun portal)</span></label>
            <input type="email" name="email" value="{{ old('email', $pelanggan->email) }}" class="form-input" @if($pelanggan->user) required @endif>
        </div>
    </div>
</div>

{{-- B. ALAMAT --}}
<div class="card mb-5">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-[11px] font-bold text-white">B</div>
            <div class="section-title text-[15px]">Alamat Pemasangan</div>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 gap-4">
        <div>
            <label class="form-label">Alamat Lengkap <span class="text-red-400">*</span></label>
            <textarea name="alamat_lengkap" rows="3" class="form-textarea" required>{{ old('alamat_lengkap', $pelanggan->alamat_lengkap) }}</textarea>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div><label class="form-label">RT</label><input type="text" name="rt" value="{{ old('rt', $pelanggan->rt) }}" class="form-input"></div>
            <div><label class="form-label">RW</label><input type="text" name="rw" value="{{ old('rw', $pelanggan->rw) }}" class="form-input"></div>
            <div class="col-span-2"><label class="form-label">Kelurahan / Desa</label><input type="text" name="kelurahan" value="{{ old('kelurahan', $pelanggan->kelurahan) }}" class="form-input"></div>
            <div class="col-span-2"><label class="form-label">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan', $pelanggan->kecamatan) }}" class="form-input"></div>
            <div class="col-span-2"><label class="form-label">Kota / Kabupaten</label><input type="text" name="kota" value="{{ old('kota', $pelanggan->kota) }}" class="form-input"></div>
            <div class="col-span-2"><label class="form-label">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi', $pelanggan->provinsi) }}" class="form-input"></div>
            <div><label class="form-label">Kode Pos</label><input type="text" name="kode_pos" value="{{ old('kode_pos', $pelanggan->kode_pos) }}" class="form-input"></div>
        </div>
        <div><label class="form-label">Patokan / Landmark</label><input type="text" name="patokan" value="{{ old('patokan', $pelanggan->patokan) }}" class="form-input"></div>
    </div>
</div>

{{-- C. PAKET --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-purple-600 flex items-center justify-center text-[11px] font-bold text-white">C</div>
            <div class="section-title text-[15px]">Paket Internet</div>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 gap-5">
        <div>
            <label class="form-label">Paket yang Dipilih <span class="text-red-400">*</span></label>
            <div class="flex flex-wrap gap-3 mt-1">
                @foreach(['10mbps'=>'10 Mbps','20mbps'=>'20 Mbps','30mbps'=>'30 Mbps','50mbps'=>'50 Mbps','100mbps'=>'100 Mbps','dedicated'=>'Dedicated'] as $val=>$label)
                <label class="cursor-pointer">
                    <input type="radio" name="paket" value="{{ $val }}" {{ old('paket',$pelanggan->paket)==$val?'checked':'' }} class="sr-only peer" required>
                    <div class="px-4 py-2 rounded-lg border border-[#2a3347] text-[13px] font-600 text-slate-400 peer-checked:border-blue-500 peer-checked:bg-[#1e3a5f] peer-checked:text-blue-300 hover:border-slate-500 transition-all">{{ $label }}</div>
                </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="form-label">Jenis Koneksi <span class="text-red-400">*</span></label>
            <div class="flex gap-3 mt-1">
                @foreach(['fiber_optik'=>'Fiber Optik','wireless'=>'Wireless','lainnya'=>'Lainnya'] as $val=>$label)
                <label class="cursor-pointer">
                    <input type="radio" name="jenis_koneksi" value="{{ $val }}" {{ old('jenis_koneksi',$pelanggan->jenis_koneksi)==$val?'checked':'' }} class="sr-only peer" required>
                    <div class="px-4 py-2 rounded-lg border border-[#2a3347] text-[13px] font-600 text-slate-400 peer-checked:border-emerald-500 peer-checked:bg-[#052e16] peer-checked:text-emerald-300 hover:border-slate-500 transition-all">{{ $label }}</div>
                </label>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div><label class="form-label">Kode / Nama Paket</label><input type="text" name="kode_paket" value="{{ old('kode_paket',$pelanggan->kode_paket) }}" class="form-input"></div>
            <div><label class="form-label">Harga Paket (Rp) <span class="text-red-400">*</span></label><input type="number" name="harga_paket" value="{{ old('harga_paket',$pelanggan->harga_paket) }}" class="form-input" required min="0"></div>
            <div><label class="form-label">Tgl. Mulai</label><input type="date" name="tgl_mulai" value="{{ old('tgl_mulai',$pelanggan->tgl_mulai?->format('Y-m-d')) }}" class="form-input"></div>
            <div><label class="form-label">Tgl. Jatuh Tempo</label><input type="date" name="tgl_jatuh_tempo" value="{{ old('tgl_jatuh_tempo',$pelanggan->tgl_jatuh_tempo?->format('Y-m-d')) }}" class="form-input"></div>
        </div>
        <div><label class="form-label">Catatan Khusus</label><input type="text" name="catatan_paket" value="{{ old('catatan_paket',$pelanggan->catatan_paket) }}" class="form-input"></div>
    </div>
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('pelanggan.show', $pelanggan) }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Simpan Perubahan
    </button>
</div>
</form>
@endsection