<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Billing::with('pelanggan')->latest();

        // Jika user yang login adalah pelanggan, hanya tampilkan tagihannya sendiri
        if (auth()->user()->hasRole('pelanggan')) {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('email', auth()->user()->email);
            });
        }

        if ($request->status) {
            $query->where('status_bayar', $request->status);
        }
        if ($request->periode) {
            $query->where('periode', 'like', '%'.$request->periode.'%');
        }
        if ($request->pelanggan_id) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }

        $billings = $query->paginate(15)->withQueryString();
        
        // Hanya admin/teknisi yang bisa melihat semua pelanggan untuk filter
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('teknisi') || auth()->user()->email === 'admin@3pp.co.id') {
            $pelanggans = Pelanggan::orderBy('nama_lengkap')->get();
        } else {
            $pelanggans = collect();
        }

        return view('billing.index', compact('billings', 'pelanggans'));
    }

    public function create(Request $request)
    {
        $pelanggans = Pelanggan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $selectedPelanggan = $request->pelanggan_id ? Pelanggan::find($request->pelanggan_id) : null;
        $nextInvoice = Billing::generateInvoiceNo();
        return view('billing.create', compact('pelanggans', 'selectedPelanggan', 'nextInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal_invoice' => 'required|date',
            'jatuh_tempo' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'periode' => 'required|string',
        ]);

        $validated['no_invoice'] = Billing::generateInvoiceNo();
        $validated['denda'] = $validated['denda'] ?? 0;
        $validated['total_bayar'] = $validated['jumlah'] + $validated['denda'];
        $validated['status_bayar'] = 'belum_bayar';

        $billing = Billing::create($validated);

        return redirect()->route('billing.show', $billing)
            ->with('success', "Invoice {$billing->no_invoice} berhasil dibuat.");
    }

    public function show(Billing $billing)
    {
        $billing->load('pelanggan', 'verifiedBy');
        return view('billing.show', compact('billing'));
    }

    public function uploadBukti(Request $request, Billing $billing)
    {
        $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|string',
        ]);

        if ($billing->bukti_bayar) {
            Storage::disk('public')->delete($billing->bukti_bayar);
        }

        $path = $request->file('bukti_bayar')->store('bukti-bayar', 'public');

        $billing->update([
            'bukti_bayar' => $path,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_bayar' => $request->metode_bayar,
            'status_bayar' => 'lunas',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('billing.show', $billing)
            ->with('success', 'Bukti bayar berhasil diupload. Status diubah ke Lunas.');
    }

    public function destroy(Billing $billing)
    {
        if ($billing->bukti_bayar) {
            Storage::disk('public')->delete($billing->bukti_bayar);
        }
        $billing->delete();
        return redirect()->route('billing.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }
}