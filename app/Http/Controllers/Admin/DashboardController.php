<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PengaduanExport;
use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

        $currentYear = now()->year;
        $earliestCreatedAt = Pengaduan::min('created_at');
        $firstYear = $earliestCreatedAt
            ? min(Carbon::parse($earliestCreatedAt)->year, $currentYear)
            : $currentYear;
        $years = range($currentYear, $firstYear);
        $exportMonths = [];

        for ($month = 1; $month <= 12; $month++) {
            $exportMonths[$month] = Carbon::create($currentYear, $month, 1)
                ->locale('id')
                ->translatedFormat('F');
        }

        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:'.$firstYear.','.$currentYear],
            'filter_bulan' => ['nullable', 'integer', 'between:1,12'],
            'filter_tahun' => ['nullable', 'integer', 'between:'.$firstYear.','.$currentYear],
        ]);
        $chartYear = (int) ($validated['tahun'] ?? $currentYear);
        $filterMonth = isset($validated['filter_bulan']) ? (int) $validated['filter_bulan'] : null;
        $filterYear = isset($validated['filter_tahun']) ? (int) $validated['filter_tahun'] : null;
        $chartLabels = [];
        $chartData = [];

        for ($month = 1; $month <= 12; $month++) {
            $bulan = Carbon::create($chartYear, $month, 1);
            $chartLabels[] = $bulan->locale('id')->translatedFormat('F');
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
            ->when($filterMonth !== null && $filterYear !== null, function ($q) use ($filterMonth, $filterYear) {
                $start = Carbon::create($filterYear, $filterMonth, 1)->startOfDay();
                $q->where('created_at', '>=', $start)
                    ->where('created_at', '<', $start->copy()->addMonth());
            })
            ->when($filterMonth !== null && $filterYear === null, function ($q) use ($filterMonth) {
                $q->whereMonth('created_at', $filterMonth);
            })
            ->when($filterMonth === null && $filterYear !== null, function ($q) use ($filterYear) {
                $start = Carbon::create($filterYear, 1, 1)->startOfDay();
                $q->where('created_at', '>=', $start)
                    ->where('created_at', '<', $start->copy()->addYear());
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
            'chartYear' => $chartYear,
            'years' => $years,
            'exportMonths' => $exportMonths,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
        ]);
    }

    public function export(Request $request)
    {
        $currentYear = now()->year;
        $earliestCreatedAt = Pengaduan::min('created_at');
        $firstYear = $earliestCreatedAt
            ? min(Carbon::parse($earliestCreatedAt)->year, $currentYear)
            : $currentYear;
        $validated = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:'.$firstYear.','.$currentYear],
            'filter_bulan' => ['nullable', 'integer', 'between:1,12'],
            'filter_tahun' => ['nullable', 'integer', 'between:'.$firstYear.','.$currentYear],
        ]);

        $monthValue = array_key_exists('filter_bulan', $validated)
            ? $validated['filter_bulan']
            : ($validated['bulan'] ?? null);
        $yearValue = array_key_exists('filter_tahun', $validated)
            ? $validated['filter_tahun']
            : ($validated['tahun'] ?? null);
        $month = $monthValue !== null ? (int) $monthValue : null;
        $year = $yearValue !== null ? (int) $yearValue : null;
        $filename = 'rekap-pengaduan-'.now()->format('Y-m-d').'.xlsx';

        if ($month !== null) {
            $monthName = strtolower(Carbon::create($year ?? $currentYear, $month, 1)->locale('id')->translatedFormat('F'));
            $period = $year !== null ? "{$monthName}-{$year}" : $monthName;
            $filename = "rekap-pengaduan-{$period}-".now()->format('Y-m-d').'.xlsx';
        } elseif ($year !== null) {
            $filename = "rekap-pengaduan-{$year}-".now()->format('Y-m-d').'.xlsx';
        }

        return Excel::download(
            new PengaduanExport($month, $year),
            $filename
        );
    }

    public function destroy(Pengaduan $pengaduan)
    {
        $foto = $pengaduan->foto;

        try {
            $pengaduan->delete();
        } catch (QueryException $exception) {
            Log::error('Gagal menghapus pengaduan.', [
                'pengaduan_id' => $pengaduan->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Pengaduan gagal dihapus. Silakan coba lagi.');
        }

        if ($foto && ! Storage::disk('public')->delete($foto)) {
            Log::warning('Foto bukti pengaduan gagal dihapus dari penyimpanan.', [
                'pengaduan_id' => $pengaduan->id,
                'foto' => $foto,
            ]);

            return back()->with('warning', 'Pengaduan berhasil dihapus, tetapi foto buktinya gagal dibersihkan.');
        }

        return back()->with('success', 'Pengaduan berhasil dihapus.');
    }

    public function destroyAll()
    {
        $fotoPaths = Pengaduan::query()
            ->whereNotNull('foto')
            ->pluck('foto')
            ->unique()
            ->values()
            ->all();

        try {
            $deletedCount = Pengaduan::query()->delete();
        } catch (QueryException $exception) {
            Log::error('Gagal menghapus seluruh data pengaduan.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Seluruh data pengaduan gagal dihapus. Silakan coba lagi.');
        }

        if ($fotoPaths && ! Storage::disk('public')->delete($fotoPaths)) {
            Log::warning('Sebagian foto bukti gagal dibersihkan setelah penghapusan seluruh pengaduan.', [
                'jumlah_foto' => count($fotoPaths),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('warning', 'Data pengaduan berhasil dihapus, tetapi sebagian foto bukti gagal dibersihkan.');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', $deletedCount
                ? 'Seluruh data pengaduan berhasil dihapus.'
                : 'Tidak ada data pengaduan untuk dihapus.');
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
