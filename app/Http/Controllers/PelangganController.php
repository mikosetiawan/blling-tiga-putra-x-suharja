<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        $pelanggans = $query->with('user')->latest()->paginate(15)->withQueryString();

        return view('pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        $this->authorizePelangganManage();

        $nextId = Pelanggan::generateId();
        $eligibleUsers = User::whereDoesntHave('pelanggan')->orderBy('name')->get();

        return view('pelanggan.create', compact('nextId', 'eligibleUsers'));
    }

    public function store(Request $request)
    {
        $this->authorizePelangganManage();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_lengkap' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'no_ktp_nib' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:30',
            'no_telepon' => 'required|string|max:20',
            'no_whatsapp' => 'nullable|string|max:20',
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

        $user = User::findOrFail($validated['user_id']);
        if ($user->pelanggan()->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Akun ini sudah terhubung ke data pelanggan lain.']);
        }

        $validated['id_pelanggan'] = Pelanggan::generateId();
        $validated['status'] = 'aktif';
        $validated['email'] = $user->email;

        unset($validated['user_id']);
        $validated['user_id'] = $user->id;

        $pelanggan = Pelanggan::create($validated);

        if (! $user->hasRole('pelanggan')) {
            $user->assignRole('pelanggan');
        }

        return redirect()->route('pelanggan.show', $pelanggan)
            ->with('success', "Pelanggan {$pelanggan->id_pelanggan} berhasil ditambahkan.");
    }

    public function show(Pelanggan $pelanggan)
    {
        $this->authorizePelangganAccess();

        $pelanggan->load(['billings' => fn ($q) => $q->latest(), 'user']);
        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        $pelanggan->load('user');

        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        $emailRules = ['required', 'email', 'max:255'];
        if ($pelanggan->user_id) {
            $emailRules[] = Rule::unique('users', 'email')->ignore($pelanggan->user_id);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'no_ktp_nib' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:30',
            'no_telepon' => 'required|string|max:20',
            'no_whatsapp' => 'nullable|string|max:20',
            'email' => $emailRules,
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

        if ($pelanggan->user_id && $pelanggan->user) {
            $pelanggan->user->update([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('pelanggan.show', $pelanggan)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $this->authorizePelangganManage();

        $linkedUser = $pelanggan->user;
        $pelanggan->forceFill(['user_id' => null])->saveQuietly();

        if ($linkedUser && $linkedUser->hasRole('pelanggan') && ! $linkedUser->isAdmin()) {
            $linkedUser->removeRole('pelanggan');
        }

        $pelanggan->delete();
        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}