@extends('layouts.app')
@section('title', 'Tambah Pelanggan Baru')

@section('content')
<form method="POST" action="{{ route('pelanggan.store') }}">
@csrf

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="section-title">Form Data Pelanggan</div>
        <div class="section-sub">Layanan Internet — PT. Tiga Putra Pandawa</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">← Kembali</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Pelanggan
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error mb-4">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <div class="font-600">Terdapat kesalahan:</div>
        <ul class="mt-1 text-[13px] list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

{{-- A. DATA PRIBADI PELANGGAN --}}
<div class="card mb-5">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-[11px] font-bold text-white">A</div>
            <div class="section-title text-[15px]">Data Pribadi Pelanggan</div>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label class="form-label">ID Pelanggan <span class="text-slate-500">(otomatis)</span></label>
            <input type="text" class="form-input bg-[#161b27] text-slate-400" value="{{ $nextId }}" readonly>
        </div>
        <div>
            <label class="form-label">Tanggal Daftar <span class="text-red-400">*</span></label>
            <input type="date" name="tanggal_daftar" value="{{ old('tanggal_daftar', date('Y-m-d')) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Nama Lengkap <span class="text-red-400">*</span> <span class="text-slate-500">(sesuai KTP)</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input" placeholder="Nama sesuai KTP" required>
        </div>
        <div>
            <label class="form-label">Nama Perusahaan <span class="text-slate-500">(jika atas nama perusahaan)</span></label>
            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" class="form-input" placeholder="PT. / CV. ...">
        </div>
        <div>
            <label class="form-label">No. KTP / NIB</label>
            <input type="text" name="no_ktp_nib" value="{{ old('no_ktp_nib') }}" class="form-input" placeholder="Nomor identitas">
        </div>
        <div>
            <label class="form-label">NPWP <span class="text-slate-500">(opsional)</span></label>
            <input type="text" name="npwp" value="{{ old('npwp') }}" class="form-input" placeholder="xx.xxx.xxx.x-xxx.xxx">
        </div>
        <div>
            <label class="form-label">No. Telepon <span class="text-red-400">*</span></label>
            <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" class="form-input" placeholder="08xx-xxxx-xxxx" required>
        </div>
        <div>
            <label class="form-label">No. WhatsApp <span class="text-slate-500">(jika berbeda)</span></label>
            <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp') }}" class="form-input" placeholder="08xx-xxxx-xxxx">
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="contoh@email.com">
        </div>
    </div>
</div>

{{-- B. ALAMAT PEMASANGAN --}}
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
            <textarea name="alamat_lengkap" rows="3" class="form-textarea" placeholder="Jalan, nomor rumah, gang..." required>{{ old('alamat_lengkap') }}</textarea>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="form-label">RT</label>
                <input type="text" name="rt" value="{{ old('rt') }}" class="form-input" placeholder="001">
            </div>
            <div>
                <label class="form-label">RW</label>
                <input type="text" name="rw" value="{{ old('rw') }}" class="form-input" placeholder="002">
            </div>
            <div class="col-span-2">
                <label class="form-label">Kelurahan / Desa</label>
                <input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="form-input">
            </div>
            <div class="col-span-2">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="form-input">
            </div>
            <div class="col-span-2">
                <label class="form-label">Kota / Kabupaten</label>
                <input type="text" name="kota" value="{{ old('kota') }}" class="form-input" placeholder="Cilegon">
            </div>
            <div class="col-span-2">
                <label class="form-label">Provinsi</label>
                <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="form-input" placeholder="Banten">
            </div>
            <div>
                <label class="form-label">Kode Pos</label>
                <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" class="form-input" placeholder="42435">
            </div>
        </div>
        <div>
            <label class="form-label">Patokan / Landmark</label>
            <input type="text" name="patokan" value="{{ old('patokan') }}" class="form-input" placeholder="Cth: dekat masjid, sebelah minimarket...">
        </div>
    </div>
</div>

{{-- C. PAKET INTERNET --}}
<div class="card mb-5">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-purple-600 flex items-center justify-center text-[11px] font-bold text-white">C</div>
            <div class="section-title text-[15px]">Paket Internet</div>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 gap-5">
        {{-- Paket pilihan --}}
        <div>
            <label class="form-label">Paket yang Dipilih <span class="text-red-400">*</span></label>
            <div class="flex flex-wrap gap-3 mt-1">
                @foreach(['10mbps' => '10 Mbps', '20mbps' => '20 Mbps', '30mbps' => '30 Mbps', '50mbps' => '50 Mbps', '100mbps' => '100 Mbps', 'dedicated' => 'Dedicated'] as $val => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="paket" value="{{ $val }}" {{ old('paket')==$val?'checked':'' }} class="sr-only peer" required>
                    <div class="px-4 py-2 rounded-lg border border-[#2a3347] text-[13px] font-600 text-slate-400
                                peer-checked:border-blue-500 peer-checked:bg-[#1e3a5f] peer-checked:text-blue-300
                                hover:border-slate-500 hover:text-slate-300 transition-all">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Jenis Koneksi --}}
        <div>
            <label class="form-label">Jenis Koneksi <span class="text-red-400">*</span></label>
            <div class="flex gap-3 mt-1">
                @foreach(['fiber_optik' => 'Fiber Optik', 'wireless' => 'Wireless', 'lainnya' => 'Lainnya'] as $val => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="jenis_koneksi" value="{{ $val }}" {{ old('jenis_koneksi')==$val?'checked':'' }} class="sr-only peer" required>
                    <div class="px-4 py-2 rounded-lg border border-[#2a3347] text-[13px] font-600 text-slate-400
                                peer-checked:border-emerald-500 peer-checked:bg-[#052e16] peer-checked:text-emerald-300
                                hover:border-slate-500 hover:text-slate-300 transition-all">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Kode / Nama Paket</label>
                <input type="text" name="kode_paket" value="{{ old('kode_paket') }}" class="form-input" placeholder="Cth: FO-DED-01">
            </div>
            <div>
                <label class="form-label">Harga Paket (Rp) <span class="text-red-400">*</span></label>
                <input type="number" name="harga_paket" value="{{ old('harga_paket', 0) }}" class="form-input" placeholder="0" required min="0">
            </div>
            <div>
                <label class="form-label">Tgl. Mulai Berlangganan</label>
                <input type="date" name="tgl_mulai" value="{{ old('tgl_mulai') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tgl. Jatuh Tempo (tgl)</label>
                <input type="date" name="tgl_jatuh_tempo" value="{{ old('tgl_jatuh_tempo') }}" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Catatan Khusus Paket</label>
            <input type="text" name="catatan_paket" value="{{ old('catatan_paket') }}" class="form-input" placeholder="Cth: IP statis, bandwidth garansi, dll.">
        </div>
    </div>
</div>

{{-- D. PERSETUJUAN --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-amber-600 flex items-center justify-center text-[11px] font-bold text-white">D</div>
            <div class="section-title text-[15px]">Persetujuan & Tanda Tangan</div>
        </div>
    </div>
    <div class="p-5">
        <p class="text-[13px] text-slate-400 italic mb-4">
            Dengan menyimpan formulir ini, data yang diberikan dinyatakan benar dan bersedia mematuhi ketentuan layanan PT. Tiga Putra Pandawa.
        </p>
        <div class="grid grid-cols-2 gap-8">
            <div class="border border-dashed border-[#2a3347] rounded-lg p-4 text-center">
                <div class="text-[13px] font-600 text-slate-400 mb-6">Pelanggan</div>
                <div class="border-t border-[#2a3347] pt-2">
                    <div class="text-[11px] text-slate-500">Nama & Tanda Tangan</div>
                </div>
            </div>
            <div class="border border-dashed border-[#2a3347] rounded-lg p-4 text-center">
                <div class="text-[13px] font-600 text-slate-400 mb-6">Admin PT. Tiga Putra Pandawa</div>
                <div class="border-t border-[#2a3347] pt-2">
                    <div class="text-[11px] text-slate-500">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Simpan Pelanggan
    </button>
</div>

</form>
@endsection