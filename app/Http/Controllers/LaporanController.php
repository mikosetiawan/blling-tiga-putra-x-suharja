<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Helpdesk;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    // ===================== LAPORAN PELANGGAN =====================
    public function pelanggan(Request $request)
    {
        $query = Pelanggan::query();

        if ($request->status) $query->where('status', $request->status);
        if ($request->paket) $query->where('paket', $request->paket);
        if ($request->dari) $query->whereDate('tanggal_daftar', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('tanggal_daftar', '<=', $request->sampai);

        $pelanggans = $query->orderBy('id_pelanggan')->get();

        $summary = [
            'total' => $pelanggans->count(),
            'aktif' => $pelanggans->where('status', 'aktif')->count(),
            'nonaktif' => $pelanggans->where('status', 'nonaktif')->count(),
            'suspend' => $pelanggans->where('status', 'suspend')->count(),
            'dedicated' => $pelanggans->where('paket', 'dedicated')->count(),
            '100mbps' => $pelanggans->where('paket', '100mbps')->count(),
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.pelanggan', compact('pelanggans', 'summary', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-pelanggan-' . date('Ymd') . '.pdf');
        }

        return view('laporan.pelanggan', compact('pelanggans', 'summary'));
    }

    // ===================== LAPORAN BILLING =====================
    public function billing(Request $request)
    {
        $query = Billing::with('pelanggan')->latest();

        if ($request->status) $query->where('status_bayar', $request->status);
        if ($request->periode) $query->where('periode', 'like', '%'.$request->periode.'%');
        if ($request->dari) $query->whereDate('tanggal_invoice', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('tanggal_invoice', '<=', $request->sampai);

        $billings = $query->get();

        $summary = [
            'total_tagihan' => $billings->sum('jumlah'),
            'total_lunas' => $billings->where('status_bayar', 'lunas')->sum('total_bayar'),
            'total_belum_bayar' => $billings->where('status_bayar', 'belum_bayar')->sum('total_bayar'),
            'count_lunas' => $billings->where('status_bayar', 'lunas')->count(),
            'count_belum' => $billings->where('status_bayar', 'belum_bayar')->count(),
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.billing', compact('billings', 'summary', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-billing-' . date('Ymd') . '.pdf');
        }

        return view('laporan.billing', compact('billings', 'summary'));
    }

    // ===================== LAPORAN HELPDESK =====================
    public function helpdesk(Request $request)
    {
        $query = Helpdesk::with('pelanggan', 'assignedTo')->latest();

        if ($request->status) $query->where('status', $request->status);
        if ($request->prioritas) $query->where('prioritas', $request->prioritas);
        if ($request->dari) $query->whereDate('created_at', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('created_at', '<=', $request->sampai);

        $helpdesks = $query->get();

        $summary = [
            'total' => $helpdesks->count(),
            'open' => $helpdesks->where('status', 'open')->count(),
            'in_progress' => $helpdesks->where('status', 'in_progress')->count(),
            'resolved' => $helpdesks->where('status', 'resolved')->count(),
            'closed' => $helpdesks->where('status', 'closed')->count(),
            'kritis' => $helpdesks->where('prioritas', 'kritis')->count(),
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.helpdesk', compact('helpdesks', 'summary', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-helpdesk-' . date('Ymd') . '.pdf');
        }

        return view('laporan.helpdesk', compact('helpdesks', 'summary'));
    }

    // ===================== DASHBOARD STATISTIK =====================
    public function dashboard(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');

        $stats = [
            'total_pelanggan' => Pelanggan::count(),
            'pelanggan_aktif' => Pelanggan::where('status', 'aktif')->count(),
            'pendapatan_bulan' => Billing::where('status_bayar', 'lunas')
                ->where('periode', 'like', '%'.date('Y').'%')
                ->sum('total_bayar'),
            'tiket_open' => Helpdesk::where('status', 'open')->count(),
            'tagihan_belum_bayar' => Billing::where('status_bayar', 'belum_bayar')->count(),
        ];

        $paketDistribusi = Pelanggan::selectRaw('paket, count(*) as total')
            ->groupBy('paket')->get();

        $billingBulanan = Billing::selectRaw('periode, SUM(total_bayar) as total, COUNT(*) as jumlah')
            ->where('status_bayar', 'lunas')
            ->groupBy('periode')
            ->orderByDesc('tanggal_invoice')
            ->limit(6)->get();

        return view('laporan.dashboard', compact('stats', 'paketDistribusi', 'billingBulanan'));
    }
}