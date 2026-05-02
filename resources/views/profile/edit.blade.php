@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Update Nama & Email --}}
    <div class="card">
        <div class="card-header">
            <div class="section-title text-[15px]">Informasi Profil</div>
            <div class="section-sub">Perbarui nama dan alamat email akun Anda</div>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="p-5 space-y-4">
            @csrf @method('PATCH')

            @if(session('status') === 'profile-updated')
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Profil berhasil diperbarui.
            </div>
            @endif

            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Role</label>
                <div class="form-input bg-[#161b27] text-slate-400">
                    {{ $user->getRoleNames()->implode(', ') ?: '—' }}
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="card">
        <div class="card-header">
            <div class="section-title text-[15px]">Ubah Password</div>
            <div class="section-sub">Gunakan password yang kuat dan unik</div>
        </div>
        <form method="POST" action="{{ route('profile.password') }}" class="p-5 space-y-4">
            @csrf @method('PATCH')

            @if(session('status') === 'password-updated')
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Password berhasil diubah.
            </div>
            @endif

            <div>
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-input" required autocomplete="current-password">
                @error('current_password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-input" required autocomplete="new-password">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Ubah Password</button>
            </div>
        </form>
    </div>

    {{-- Hapus Akun --}}
    <div class="card border-red-900/50">
        <div class="card-header" style="border-bottom-color: #7f1d1d33;">
            <div class="section-title text-[15px] text-red-400">Hapus Akun</div>
            <div class="section-sub">Tindakan ini tidak dapat dibatalkan</div>
        </div>
        <div class="p-5">
            <p class="text-[13px] text-slate-400 mb-4">
                Setelah akun dihapus, semua data dan resource akan dihapus permanen.
                Masukkan password Anda untuk konfirmasi.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('Yakin ingin menghapus akun ini secara permanen?')">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <input type="password" name="password" class="form-input flex-1"
                           placeholder="Masukkan password untuk konfirmasi" required>
                    <button type="submit" class="btn btn-danger flex-shrink-0">Hapus Akun</button>
                </div>
                @error('password', 'userDeletion')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </form>
        </div>
    </div>

</div>
@endsection