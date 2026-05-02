<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{
    private function billingBelongsToPelangganUser(Billing $billing): bool
    {
        if (! $billing->pelanggan) {
            return false;
        }

        if ($billing->pelanggan->user_id) {
            return (int) $billing->pelanggan->user_id === (int) auth()->id();
        }

        return (bool) $billing->pelanggan->email
            && $billing->pelanggan->email === auth()->user()->email;
    }

    public function index(Request $request)
    {
        $query = Billing::with('pelanggan')->latest();

        if (auth()->user()->isPelanggan()) {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('user_id')
                            ->where('pelanggans.email', auth()->user()->email);
                    });
            });
        }

        if ($request->status) {
            $query->where('status_bayar', $request->status);
        }
        if ($request->periode) {
            $query->where('periode', 'like', '%'.$request->periode.'%');
        }
        if ($request->pelanggan_id && auth()->user()->canManageBillingInvoices()) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }

        $billings = $query->paginate(15)->withQueryString();

        $statsBase = Billing::query();
        if (auth()->user()->isPelanggan()) {
            $statsBase->whereHas('pelanggan', function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('user_id')
                            ->where('pelanggans.email', auth()->user()->email);
                    });
            });
        }
        $billingStats = [
            'total' => (clone $statsBase)->count(),
            'lunas' => (clone $statsBase)->where('status_bayar', 'lunas')->count(),
            'belum_bayar' => (clone $statsBase)->where('status_bayar', 'belum_bayar')->count(),
            'nilai_pending' => (clone $statsBase)->where('status_bayar', 'belum_bayar')->sum('total_bayar'),
        ];

        $pelanggans = auth()->user()->canManageBillingInvoices()
            ? Pelanggan::whereNotNull('user_id')->orderBy('nama_lengkap')->get()
            : collect();

        return view('billing.index', compact('billings', 'pelanggans', 'billingStats'));
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->canManageBillingInvoices(), 403);

        $pelanggans = Pelanggan::where('status', 'aktif')
            ->whereNotNull('user_id')
            ->with('user')
            ->orderBy('nama_lengkap')
            ->get();

        $selectedPelanggan = $request->pelanggan_id ? Pelanggan::find($request->pelanggan_id) : null;
        if ($selectedPelanggan && ! $selectedPelanggan->user_id) {
            $selectedPelanggan = null;
        }

        $nextInvoice = Billing::generateInvoiceNo();

        return view('billing.create', compact('pelanggans', 'selectedPelanggan', 'nextInvoice'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageBillingInvoices(), 403);

        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal_invoice' => 'required|date',
            'jatuh_tempo' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'periode' => 'required|string',
        ]);

        $pelanggan = Pelanggan::whereKey($validated['pelanggan_id'])->whereNotNull('user_id')->first();
        if (! $pelanggan) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pelanggan_id' => 'Pelanggan harus memiliki akun portal (terhubung ke user).']);
        }

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

        if (auth()->user()->isPelanggan()) {
            abort_unless($this->billingBelongsToPelangganUser($billing), 403);
        }

        return view('billing.show', compact('billing'));
    }

    public function uploadBukti(Request $request, Billing $billing)
    {
        $canPelanggan = auth()->user()->isPelanggan() && $this->billingBelongsToPelangganUser($billing);
        $canAdmin = auth()->user()->canManageBillingInvoices();
        abort_unless($canPelanggan || $canAdmin, 403);

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
        abort_unless(auth()->user()->canManageBillingInvoices(), 403);

        if ($billing->bukti_bayar) {
            Storage::disk('public')->delete($billing->bukti_bayar);
        }
        $billing->delete();
        return redirect()->route('billing.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }
}
