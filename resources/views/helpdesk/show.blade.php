@extends('layouts.app')
@section('title', 'Tiket ' . $helpdesk->no_tiket)

@section('content')
<div class="mb-5">
    <a href="{{ route('helpdesk.index') }}" class="text-[13px] text-slate-500 hover:text-slate-300">← Kembali ke Helpdesk</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Detail Tiket --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Header --}}
        <div class="card p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <span class="font-mono text-purple-400 font-600 text-[15px]">{{ $helpdesk->no_tiket }}</span>
                        <span class="badge badge-{{ $helpdesk->status==='in_progress'?'progress':$helpdesk->status }}">
                            {{ ucfirst(str_replace('_',' ',$helpdesk->status)) }}
                        </span>
                        <span class="prio-{{ $helpdesk->prioritas }} font-700">
                            @php $dots=['rendah'=>'🟢','sedang'=>'🟡','tinggi'=>'🟠','kritis'=>'🔴']; @endphp
                            {{ $dots[$helpdesk->prioritas] }} {{ ucfirst($helpdesk->prioritas) }}
                        </span>
                    </div>
                    <h2 class="text-[18px] font-800 text-white">{{ $helpdesk->kategori_label }}</h2>
                    <div class="text-[13px] text-slate-500 mt-1">
                        Dibuat {{ $helpdesk->created_at->diffForHumans() }} · {{ $helpdesk->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
                @if($helpdesk->resolved_at)
                <div class="text-right text-[12px]">
                    <div class="text-emerald-400 font-600">✅ Selesai</div>
                    <div class="text-slate-500">{{ $helpdesk->resolved_at->format('d M Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">📝 Deskripsi Masalah</div></div>
            <div class="p-5">
                <p class="text-[14px] text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $helpdesk->deskripsi }}</p>
            </div>
        </div>

        {{-- Solusi / Update --}}
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">🔧 Solusi & Update Status</div></div>
            <form method="POST" action="{{ route('helpdesk.update', $helpdesk) }}" class="p-5 space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Status Tiket</label>
                        <select name="status" class="form-select">
                            <option value="open" {{ $helpdesk->status=='open'?'selected':'' }}>Open</option>
                            <option value="in_progress" {{ $helpdesk->status=='in_progress'?'selected':'' }}>In Progress</option>
                            <option value="resolved" {{ $helpdesk->status=='resolved'?'selected':'' }}>Resolved</option>
                            <option value="closed" {{ $helpdesk->status=='closed'?'selected':'' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Prioritas</label>
                        <select name="prioritas" class="form-select">
                            <option value="rendah" {{ $helpdesk->prioritas=='rendah'?'selected':'' }}>🟢 Rendah</option>
                            <option value="sedang" {{ $helpdesk->prioritas=='sedang'?'selected':'' }}>🟡 Sedang</option>
                            <option value="tinggi" {{ $helpdesk->prioritas=='tinggi'?'selected':'' }}>🟠 Tinggi</option>
                            <option value="kritis" {{ $helpdesk->prioritas=='kritis'?'selected':'' }}>🔴 Kritis</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Ditugaskan Kepada</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $helpdesk->assigned_to==$u->id?'selected':'' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Solusi / Catatan Teknis</label>
                    <textarea name="solusi" rows="4" class="form-textarea" placeholder="Tuliskan solusi atau tindakan yang dilakukan...">{{ old('solusi', $helpdesk->solusi) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Info Pelanggan --}}
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">👤 Pelanggan</div></div>
            <div class="p-4 space-y-2">
                <a href="{{ route('pelanggan.show', $helpdesk->pelanggan) }}" class="font-600 text-blue-400 hover:underline text-[14px] block">
                    {{ $helpdesk->pelanggan->nama_perusahaan ?: $helpdesk->pelanggan->nama_lengkap }}
                </a>
                <div class="text-[12px] font-mono text-slate-500">{{ $helpdesk->pelanggan->id_pelanggan }}</div>
                <div class="border-t border-[#2a3347] pt-2 mt-2 space-y-1.5">
                    <div class="flex justify-between text-[12px]"><span class="text-slate-500">Paket</span><span class="text-blue-400">{{ $helpdesk->pelanggan->paket_label }}</span></div>
                    <div class="flex justify-between text-[12px]"><span class="text-slate-500">Telepon</span><span class="text-slate-300">{{ $helpdesk->pelanggan->no_telepon }}</span></div>
                    <div class="flex justify-between text-[12px]"><span class="text-slate-500">Status</span><span class="badge badge-{{ $helpdesk->pelanggan->status }}">{{ ucfirst($helpdesk->pelanggan->status) }}</span></div>
                </div>
            </div>
        </div>

        {{-- Info Pelapor --}}
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">📞 Pelapor</div></div>
            <div class="p-4 space-y-2">
                <div class="text-[14px] font-600 text-slate-200">{{ $helpdesk->pelapor }}</div>
                <div class="text-[13px] text-slate-400">{{ $helpdesk->no_telepon_pelapor }}</div>
            </div>
        </div>

        {{-- Assigned --}}
        @if($helpdesk->assignedTo)
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-slate-300">🔧 Teknisi</div></div>
            <div class="p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center justify-center text-white font-700 text-[13px]">
                        {{ strtoupper(substr($helpdesk->assignedTo->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-[14px] font-600 text-slate-200">{{ $helpdesk->assignedTo->name }}</div>
                        <div class="text-[12px] text-slate-500">{{ $helpdesk->assignedTo->email }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Solusi --}}
        @if($helpdesk->solusi)
        <div class="card">
            <div class="card-header"><div class="text-[14px] font-700 text-emerald-400">✅ Solusi Terdokumentasi</div></div>
            <div class="p-4">
                <p class="text-[13px] text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $helpdesk->solusi }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection