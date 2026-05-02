@extends('layouts.app', ['title' => 'Edit User'])

@section('topbar-actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
@endsection

@section('content')
<div class="card max-w-2xl mx-auto">
    <div class="card-header">
        <h2 class="section-title">Form Edit User</h2>
        <div class="section-sub">Perbarui data atau hak akses pengguna.</div>
    </div>
    
    <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')
        
        <div>
            <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="form-label">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="form-label">Password Baru <span class="text-slate-500 font-normal">(Opsional)</span></label>
            <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror" placeholder="Kosongkan jika tidak ingin mengubah password">
            @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password baru">
        </div>

        <div>
            <label class="form-label">Role / Hak Akses <span class="text-red-500">*</span></label>
            <div class="flex flex-wrap gap-4 mt-2">
                @foreach($roles as $role)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="w-4 h-4 text-blue-600 bg-[#0f1117] border-[#2a3347] rounded focus:ring-blue-500 focus:ring-2" {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                    <span class="text-sm text-slate-300">{{ ucfirst($role->name) }}</span>
                </label>
                @endforeach
            </div>
            @error('roles') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            @if($roles->isEmpty())
                <div class="text-xs text-yellow-500 mt-2">Belum ada role di database. Anda mungkin perlu menjalankan seeder role terlebih dahulu.</div>
            @endif
        </div>

        <div class="pt-4 border-t border-[#2a3347] flex justify-end">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
