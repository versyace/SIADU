@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-slate-800">Dashboard Pengaduan SIADU</h1>
<p class="text-slate-500 text-sm mt-1">Ikhtisar dan manajemen pemeliharaan sarana prasarana sekolah.</p>

<div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:col-span-1">
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

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2" aria-labelledby="chart-title">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <h2 id="chart-title" class="font-semibold text-slate-800">Statistik Pengaduan per Bulan</h2>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                @if ($cari !== '')
                    <input type="hidden" name="cari" value="{{ $cari }}">
                @endif
                @if ($statusAktif !== 'semua')
                    <input type="hidden" name="status" value="{{ $statusAktif }}">
                @endif
                @if ($filterMonth !== null)
                    <input type="hidden" name="filter_bulan" value="{{ $filterMonth }}">
                @endif
                <input type="hidden" name="filter_tahun" value="{{ $filterYear }}">
                <label for="chartYear" class="text-sm text-slate-600">Tahun</label>
                <div class="relative">
                    <select id="chartYear" name="tahun" onchange="this.form.submit()"
                            class="appearance-none rounded-lg border border-slate-300 bg-white py-2 pl-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $chartYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </form>
        </div>
        <div class="relative h-64 p-3 sm:h-72 sm:p-5">
            <canvas id="pengaduanChart" role="img" aria-label="Diagram batang jumlah pengaduan setiap bulan pada tahun {{ $chartYear }}"></canvas>
        </div>
    </section>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mt-6 overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col gap-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-slate-800">Data Pengaduan Masuk</h2>
                <p class="text-xs text-slate-500">Daftar laporan sarana yang masuk dari siswa</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <form method="GET" action="{{ route('admin.dashboard') }}"
                      class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    @if ($cari !== '')
                        <input type="hidden" name="cari" value="{{ $cari }}">
                    @endif
                    @if ($statusAktif !== 'semua')
                        <input type="hidden" name="status" value="{{ $statusAktif }}">
                    @endif
                    <input type="hidden" name="tahun" value="{{ $chartYear }}">
                    <div class="relative w-full sm:w-auto">
                        <select name="filter_bulan" aria-label="Pilih bulan rekap" onchange="this.form.submit()"
                                class="w-full appearance-none rounded-lg border border-slate-300 bg-white py-2 pl-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 sm:w-auto">
                            <option value="">Semua Bulan</option>
                            @foreach ($exportMonths as $monthNumber => $monthName)
                                <option value="{{ $monthNumber }}" {{ $filterMonth === $monthNumber ? 'selected' : '' }}>{{ $monthName }}</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="relative w-full sm:w-auto">
                        <select name="filter_tahun" aria-label="Pilih tahun rekap" onchange="this.form.submit()"
                                class="w-full appearance-none rounded-lg border border-slate-300 bg-white py-2 pl-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 sm:w-auto">
                            <option value="" {{ $filterYear === null ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $filterYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <button type="submit" formaction="{{ route('admin.pengaduan.export') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm font-medium text-green-700 transition-colors hover:bg-green-50 sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v3h14v-3" />
                        </svg>
                        Download Rekap Excel
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.pengaduan.destroyAll') }}"
                      data-confirm-message="PERINGATAN: Seluruh data pengaduan akan dihapus permanen. Tindakan ini tidak dapat dibatalkan. Lanjutkan?"
                      data-confirm-label="Ya, Hapus Semua">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 sm:w-auto">
                        Hapus Semua Data
                    </button>
                </form>
            </div>
        </div>
        <form method="GET" class="flex w-full min-w-0 flex-col gap-2 sm:w-auto sm:flex-row">
            @if ($filterMonth !== null)
                <input type="hidden" name="filter_bulan" value="{{ $filterMonth }}">
            @endif
            <input type="hidden" name="filter_tahun" value="{{ $filterYear }}">
            <input type="hidden" name="tahun" value="{{ $chartYear }}">
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
                    <th class="text-left px-5 py-3">Tanggal Pengaduan</th>
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
                        <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-3 py-1 rounded-full {{ $item->statusColor() }}">
                                {{ $item->statusLabel() }}
                            </span>
                        </td>
                       <td class="px-5 py-3">
    <div class="flex items-center justify-end gap-3">
        <form method="POST"
              action="{{ route('admin.pengaduan.destroy', $item) }}"
              data-confirm-message="Hapus pengaduan #{{ $item->kode_aduan }} beserta foto buktinya?"
              data-confirm-label="Ya, Hapus">
            @csrf
            @method('DELETE')

            <button type="submit"
                    title="Hapus pengaduan"
                    aria-label="Hapus pengaduan #{{ $item->kode_aduan }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 transition-colors hover:bg-red-50 hover:border-red-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 7h12M10 11v6m4-6v6M9 7V4h6v3m-8 0 1 13h8l1-13" />
                </svg>
            </button>
        </form>

        <a href="{{ route('admin.pengaduan.show', $item) }}"
           class="inline-flex h-9 items-center justify-center rounded-lg bg-green-700 px-3 text-sm font-medium text-white transition-colors hover:bg-green-800">
            Detail &rarr;
        </a>
    </div>
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada pengaduan masuk.</td>
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

            <nav aria-label="Navigasi halaman pengaduan" class="flex flex-wrap items-center justify-center gap-1">
                @if ($pengaduans->onFirstPage())
                    <span aria-disabled="true" class="rounded-lg border border-slate-200 px-2 sm:px-3 py-2 text-xs sm:text-sm text-slate-400">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $pengaduans->previousPageUrl() }}" rel="prev"
                       class="rounded-lg border border-slate-300 px-2 sm:px-3 py-2 text-xs sm:text-sm text-slate-600 hover:bg-slate-50">
                        Sebelumnya
                    </a>
                @endif

                @foreach ($pengaduans->getUrlRange(max(1, $pengaduans->currentPage() - 2), min($pengaduans->lastPage(), $pengaduans->currentPage() + 2)) as $page => $url)
                    @if ($page === $pengaduans->currentPage())
                        <span aria-current="page" class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-700 text-sm font-medium text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="Halaman {{ $page }}"
                           class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($pengaduans->hasMorePages())
                    <a href="{{ $pengaduans->nextPageUrl() }}" rel="next"
                       class="rounded-lg border border-slate-300 px-2 sm:px-3 py-2 text-xs sm:text-sm text-slate-600 hover:bg-slate-50">
                        Berikutnya
                    </a>
                @else
                    <span aria-disabled="true" class="rounded-lg border border-slate-200 px-2 sm:px-3 py-2 text-xs sm:text-sm text-slate-400">
                        Berikutnya
                    </span>
                @endif
            </nav>
        </div>
    @endif
</div>

<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-labelledby="deleteConfirmTitle" aria-describedby="deleteConfirmMessage">
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 text-center shadow-xl sm:p-7">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h2 id="deleteConfirmTitle" class="text-lg font-bold text-slate-800">Konfirmasi Penghapusan</h2>
        <p id="deleteConfirmMessage" class="mt-2 text-sm leading-relaxed text-slate-500"></p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" id="cancelDeleteButton"
                    class="w-full rounded-xl border border-slate-300 py-2.5 font-medium text-slate-600 transition-colors hover:bg-slate-50">
                Batal
            </button>
            <button type="button" id="confirmDeleteButton"
                    class="w-full rounded-xl bg-red-600 py-2.5 font-medium text-white transition-colors hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>

@if (session('success') || session('warning') || session('error'))
    @php
        $noticeType = session('error') ? 'error' : (session('warning') ? 'warning' : 'success');
        $noticeStyle = [
            'success' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'button' => 'bg-green-700 hover:bg-green-800'],
            'warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'button' => 'bg-amber-600 hover:bg-amber-700'],
            'error' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'button' => 'bg-red-600 hover:bg-red-700'],
        ][$noticeType];
    @endphp
    <div id="actionNoticeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
         role="dialog" aria-modal="true" aria-labelledby="actionNoticeTitle" aria-describedby="actionNoticeMessage">
        <div class="w-full max-w-sm rounded-2xl bg-white p-5 text-center shadow-xl sm:p-7">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full {{ $noticeStyle['bg'] }} {{ $noticeStyle['text'] }}">
                @if ($noticeType === 'success')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif ($noticeType === 'warning')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>
            <h2 id="actionNoticeTitle" class="text-lg font-bold text-slate-800">
                {{ $noticeType === 'success' ? 'Berhasil' : ($noticeType === 'warning' ? 'Perhatian' : 'Gagal') }}
            </h2>
            <p id="actionNoticeMessage" class="mt-2 text-sm leading-relaxed text-slate-500">
                {{ session($noticeType) }}
            </p>
            <button type="button" onclick="document.getElementById('actionNoticeModal').remove()"
                    class="mt-5 w-full rounded-xl py-2.5 font-medium text-white transition-colors {{ $noticeStyle['button'] }}">
                Oke, Mengerti
            </button>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let pendingDeleteForm = null;
    const confirmModal = document.getElementById('deleteConfirmModal');
    const confirmMessage = document.getElementById('deleteConfirmMessage');
    const confirmButton = document.getElementById('confirmDeleteButton');
    const cancelButton = document.getElementById('cancelDeleteButton');

    function closeDeleteModal() {
        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        pendingDeleteForm = null;
    }

    document.querySelectorAll('form[data-confirm-message]').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            pendingDeleteForm = form;
            confirmMessage.textContent = form.dataset.confirmMessage;
            confirmButton.textContent = form.dataset.confirmLabel || 'Hapus';
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            cancelButton.focus();
        });
    });

    cancelButton.addEventListener('click', closeDeleteModal);
    confirmModal.addEventListener('click', event => {
        if (event.target === confirmModal) {
            closeDeleteModal();
        }
    });
    confirmButton.addEventListener('click', () => {
        if (pendingDeleteForm) {
            pendingDeleteForm.submit();
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });

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

    const chartCanvas = document.getElementById('pengaduanChart');

    if (chartCanvas) {
        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: {{ Illuminate\Support\Js::from($chartLabels) }},
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: {{ Illuminate\Support\Js::from($chartData) }},
                    backgroundColor: 'rgba(21, 128, 61, 0.72)',
                    borderColor: 'rgb(21, 128, 61)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 48,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: reduceMotion ? 0 : 800,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: items => items[0].label,
                            label: context => `${context.parsed.y} pengaduan`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1 },
                        title: { display: true, text: 'Jumlah Pengaduan' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }
});
</script>

@endsection