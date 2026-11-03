<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PengaduanExport;
use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    // Halaman dashboard: statistik + tabel daftar pengaduan
    public function index(Request $request)
    {
        $stats = [
            'total' => Pengaduan::count(),
            'dilaporkan' => Pengaduan::where('status', 'dilaporkan')->count(),
            'proses' => Pengaduan::where('status', 'proses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
        ];

        $bulanAwal = now()->startOfMonth()->subMonths(5);
        $chartLabels = [];
        $chartData = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $bulan = $bulanAwal->copy()->addMonths($offset);
            $chartLabels[] = $bulan->locale('id')->translatedFormat('F Y');
            $chartData[] = Pengaduan::query()
                ->where('created_at', '>=', $bulan->copy()->startOfMonth())
                ->where('created_at', '<', $bulan->copy()->addMonth()->startOfMonth())
                ->count();
        }

        $pengaduans = Pengaduan::query()
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->get('cari');
                $q->where(function ($sub) use ($cari) {
                    $sub->where('kode_aduan', 'like', "%{$cari}%")
                        ->orWhere('nama', 'like', "%{$cari}%")
                        ->orWhere('lokasi', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('status') && $request->get('status') !== 'semua', function ($q) use ($request) {
                $q->where('status', $request->get('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.dashboard', [
            'stats' => $stats,
            'pengaduans' => $pengaduans,
            'cari' => $request->get('cari', ''),
            'statusAktif' => $request->get('status', 'semua'),
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }

    public function export()
    {
        return Excel::download(
            new PengaduanExport,
            'rekap-pengaduan-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    // Halaman detail satu pengaduan
    public function show(Pengaduan $pengaduan)
    {
        return view('admin.detail', [
            'pengaduan' => $pengaduan,
        ]);
    }

    // Ubah status + catatan admin
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:dilaporkan,proses,selesai'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $pengaduan->update($validated);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }
}
