@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-slate-800">Dashboard Pengaduan Sarpras</h1>
<p class="text-slate-500 text-sm mt-1">Ikhtisar dan manajemen pemeliharaan sarana prasarana sekolah.</p>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-6">
    <div class="min-w-0 bg-white border border-slate-200 rounded-2xl shadow-sm p-3 sm:p-5">
        <p class="text-xs font-medium text-slate-500 uppercase">Total Aduan</p>
        <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1" data-count="{{ $stats['total'] }}">{{ $stats['total'] }}</p>
    </div>
    <div class="min-w-0 bg-blue-50 border border-blue-100 rounded-2xl p-3 sm:p-5">
        <p class="text-xs font-medium text-blue-600 uppercase">Dilaporkan</p>
        <p class="text-2xl sm:text-3xl font-bold text-blue-700 mt-1" data-count="{{ $stats['dilaporkan'] }}">{{ $stats['dilaporkan'] }}</p>
    </div>
    <div class="min-w-0 bg-amber-50 border border-amber-100 rounded-2xl p-3 sm:p-5">
        <p class="text-xs font-medium text-amber-600 uppercase">Proses</p>
        <p class="text-2xl sm:text-3xl font-bold text-amber-700 mt-1" data-count="{{ $stats['proses'] }}">{{ $stats['proses'] }}</p>
    </div>
    <div class="min-w-0 bg-green-50 border border-green-100 rounded-2xl p-3 sm:p-5">
        <p class="text-xs font-medium text-green-600 uppercase">Selesai</p>
        <p class="text-2xl sm:text-3xl font-bold text-green-700 mt-1" data-count="{{ $stats['selesai'] }}">{{ $stats['selesai'] }}</p>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mt-6 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-semibold text-slate-800">Data Pengaduan Masuk</h2>
            <p class="text-xs text-slate-500">Daftar laporan sarana yang masuk dari siswa</p>
        </div>
        <form method="GET" class="flex w-full min-w-0 flex-col gap-2 sm:w-auto sm:flex-row">
            <div class="flex min-w-0 gap-2 sm:flex-1">
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari kode, nama, lokasi..."
                       class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <button type="submit" class="shrink-0 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4">
                    Cari
                </button>
            </div>
            <div class="relative w-full sm:w-auto">
                <select name="status" onchange="this.form.submit()"
                        class="w-full appearance-none rounded-lg border border-slate-300 bg-white pl-3 pr-9 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 sm:w-auto">
                    <option value="semua" {{ $statusAktif === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="dilaporkan" {{ $statusAktif === 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                    <option value="proses" {{ $statusAktif === 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="selesai" {{ $statusAktif === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="text-left px-5 py-3">Kode</th>
                    <th class="text-left px-5 py-3">Nama</th>
                    <th class="text-left px-5 py-3">Lokasi</th>
                    <th class="text-left px-5 py-3">Fasilitas</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pengaduans as $item)
                    <tr>
                        <td class="px-5 py-3 font-medium text-slate-700">#{{ $item->kode_aduan }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->nama }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->lokasi }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->fasilitas }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-3 py-1 rounded-full {{ $item->statusColor() }}">
                                {{ $item->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.pengaduan.show', $item) }}"
                               class="text-green-700 font-medium hover:underline">Detail &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada pengaduan masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($pengaduans->hasPages())
        <div class="p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Menampilkan {{ $pengaduans->firstItem() }}&ndash;{{ $pengaduans->lastItem() }} dari {{ $pengaduans->total() }} laporan
            </p>

            <div class="flex items-center gap-1.5">
                <a href="{{ $pengaduans->previousPageUrl() ?? '#' }}"
                   class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-300 text-slate-500 {{ $pengaduans->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50 hover:border-slate-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>

                @foreach ($pengaduans->getUrlRange(max(1, $pengaduans->currentPage() - 2), min($pengaduans->lastPage(), $pengaduans->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-colors {{ $page === $pengaduans->currentPage() ? 'bg-green-700 text-white' : 'border border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                        {{ $page }}
                    </a>
                @endforeach

                <a href="{{ $pengaduans->nextPageUrl() ?? '#' }}"
                   class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-300 text-slate-500 {{ $pengaduans->hasMorePages() ? 'hover:bg-slate-50 hover:border-slate-400' : 'opacity-40 pointer-events-none' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10) || 0;
        const duration = 1000; // ms

        if (reduceMotion || target === 0) {
            el.textContent = target.toLocaleString('id-ID');
            return;
        }

        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // melambat di akhir
            el.textContent = Math.round(eased * target).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(tick);
        }

        el.textContent = '0';
        requestAnimationFrame(tick);
    });
});
</script>

@endsection