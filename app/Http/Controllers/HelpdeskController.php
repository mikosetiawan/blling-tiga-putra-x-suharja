<?php

namespace App\Http\Controllers;

use App\Models\Helpdesk;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;

class HelpdeskController extends Controller
{
    public function index(Request $request)
    {
        $query = Helpdesk::with('pelanggan', 'assignedTo')->latest();

        // Jika user yang login adalah pelanggan, hanya tampilkan tiketnya sendiri
        if (auth()->user()->hasRole('pelanggan')) {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('email', auth()->user()->email);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('no_tiket', 'like', '%'.$request->search.'%')
                  ->orWhere('pelapor', 'like', '%'.$request->search.'%')
                  ->orWhereHas('pelanggan', fn($p) => $p->where('nama_lengkap', 'like', '%'.$request->search.'%')
                      ->orWhere('nama_perusahaan', 'like', '%'.$request->search.'%'));
            });
        }

        $helpdesks = $query->paginate(15)->withQueryString();
        
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('teknisi') || auth()->user()->email === 'admin@3pp.co.id') {
            $pelanggans = Pelanggan::orderBy('nama_lengkap')->get();
            $stats = [
                'open' => Helpdesk::where('status', 'open')->count(),
                'in_progress' => Helpdesk::where('status', 'in_progress')->count(),
                'resolved' => Helpdesk::where('status', 'resolved')->count(),
                'closed' => Helpdesk::where('status', 'closed')->count(),
            ];
        } else {
            $pelanggans = collect();
            $myPelanggan = Pelanggan::where('email', auth()->user()->email)->first();
            $stats = [
                'open' => $myPelanggan ? Helpdesk::where('pelanggan_id', $myPelanggan->id)->where('status', 'open')->count() : 0,
                'in_progress' => $myPelanggan ? Helpdesk::where('pelanggan_id', $myPelanggan->id)->where('status', 'in_progress')->count() : 0,
                'resolved' => $myPelanggan ? Helpdesk::where('pelanggan_id', $myPelanggan->id)->where('status', 'resolved')->count() : 0,
                'closed' => $myPelanggan ? Helpdesk::where('pelanggan_id', $myPelanggan->id)->where('status', 'closed')->count() : 0,
            ];
        }
        
        $users = User::orderBy('name')->get();

        return view('helpdesk.index', compact('helpdesks', 'pelanggans', 'users', 'stats'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $users = User::orderBy('name')->get();
        $nextTiket = Helpdesk::generateTiketNo();
        $kategoriOptions = $this->getKategoriOptions();
        return view('helpdesk.create', compact('pelanggans', 'users', 'nextTiket', 'kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'pelapor' => 'required|string|max:255',
            'no_telepon_pelapor' => 'required|string|max:20',
            'kategori' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi,kritis',
            'deskripsi' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['no_tiket'] = Helpdesk::generateTiketNo();
        $validated['status'] = 'open';

        $helpdesk = Helpdesk::create($validated);

        return redirect()->route('helpdesk.show', $helpdesk)
            ->with('success', "Tiket {$helpdesk->no_tiket} berhasil dibuat.");
    }

    public function show(Helpdesk $helpdesk)
    {
        $helpdesk->load('pelanggan', 'assignedTo');
        $users = User::orderBy('name')->get();
        $kategoriOptions = $this->getKategoriOptions();
        return view('helpdesk.show', compact('helpdesk', 'users', 'kategoriOptions'));
    }

    public function update(Request $request, Helpdesk $helpdesk)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'prioritas' => 'required|in:rendah,sedang,tinggi,kritis',
            'assigned_to' => 'nullable|exists:users,id',
            'solusi' => 'nullable|string',
        ]);

        if ($validated['status'] === 'resolved' && !$helpdesk->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $helpdesk->update($validated);

        return redirect()->route('helpdesk.show', $helpdesk)
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    private function getKategoriOptions(): array
    {
        return [
            'gangguan_koneksi' => 'Gangguan Koneksi',
            'lambat' => 'Koneksi Lambat',
            'putus_nyambung' => 'Putus Nyambung',
            'tidak_bisa_akses' => 'Tidak Bisa Akses',
            'ganti_password_wifi' => 'Ganti Password WiFi',
            'relokasi' => 'Relokasi',
            'upgrade_paket' => 'Upgrade Paket',
            'downgrade_paket' => 'Downgrade Paket',
            'pertanyaan_billing' => 'Pertanyaan Billing',
            'lainnya' => 'Lainnya',
        ];
    }
}