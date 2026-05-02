<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    private function authorizePelangganAccess(): void
    {
        abort_unless(auth()->user()->canViewPelanggan(), 403);
    }

    private function authorizePelangganManage(): void
    {
        abort_unless(auth()->user()->canManagePelangganData(), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePelangganAccess();

        $query = Pelanggan::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%'.$request->search.'%')
                  ->orWhere('nama_perusahaan', 'like', '%'.$request->search.'%')
                  ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%')
                  ->orWhere('no_telepon', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->paket) {
            $query->where('paket', $request->paket);
        }

        $pelanggans = $query->latest()->paginate(15)->withQueryString();

        return view('pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        $this->authorizePelangganManage();

        $nextId = Pelanggan::generateId();
        return view('pelanggan.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $this->authorizePelangganManage();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'no_ktp_nib' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:30',
            'no_telepon' => 'required|string|max:20',
            'no_whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat_lengkap' => 'required|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'patokan' => 'nullable|string|max:255',
            'paket' => 'required|in:10mbps,20mbps,30mbps,50mbps,100mbps,dedicated',
            'jenis_koneksi' => 'required|in:fiber_optik,wireless,lainnya',
            'kode_paket' => 'nullable|string|max:50',
            'harga_paket' => 'required|numeric|min:0',
            'tgl_mulai' => 'nullable|date',
            'tgl_jatuh_tempo' => 'nullable|date',
            'catatan_paket' => 'nullable|string',
            'tanggal_daftar' => 'required|date',
        ]);

        $validated['id_pelanggan'] = Pelanggan::generateId();
        $validated['status'] = 'aktif';

        $pelanggan = Pelanggan::create($validated);

        return redirect()->route('pelanggan.show', $pelanggan)
            ->with('success', "Pelanggan {$pelanggan->id_pelanggan} berhasil ditambahkan.");
    }

    public function show(Pelanggan $pelanggan)
    {
        $this->authorizePelangganAccess();

        $pelanggan->load(['billings' => fn ($q) => $q->latest()]);
        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'no_ktp_nib' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:30',
            'no_telepon' => 'required|string|max:20',
            'no_whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat_lengkap' => 'required|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'patokan' => 'nullable|string|max:255',
            'paket' => 'required|in:10mbps,20mbps,30mbps,50mbps,100mbps,dedicated',
            'jenis_koneksi' => 'required|in:fiber_optik,wireless,lainnya',
            'kode_paket' => 'nullable|string|max:50',
            'harga_paket' => 'required|numeric|min:0',
            'tgl_mulai' => 'nullable|date',
            'tgl_jatuh_tempo' => 'nullable|date',
            'catatan_paket' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,suspend',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.show', $pelanggan)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        $pelanggan->delete();
        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}