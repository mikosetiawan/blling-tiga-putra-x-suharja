@extends('layouts.app', ['title' => 'Manajemen User / Akses'])

@section('topbar-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="section-title">Daftar User & Hak Akses</h2>
        <div class="section-sub">Kelola pengguna yang dapat mengakses sistem ini.</div>
    </div>
    <div class="table-wrapper">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role / Akses</th>
                    <th>Tanggal Daftar</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ $user->avatar_initial }}
                            </div>
                            <div class="font-medium">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-open">{{ ucfirst($role->name) }}</span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span class="badge badge-closed">Tidak ada role</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-secondary" title="Edit">
                                Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-slate-500">Belum ada data user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-4 border-t border-[#2a3347]">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
