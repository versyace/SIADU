@extends('layouts.app')

@section('title', 'Lacak Pengaduan')

@section('content')
<div class="mx-auto w-full max-w-2xl">
    <h1 class="text-2xl font-bold text-slate-800">Lacak Pengaduan</h1>
    <p class="text-slate-500 mt-1 text-sm">
        Masukkan kode pengaduan untuk memantau tahapan perbaikan sarana sekolah.
    </p>
    <p id="riwayatStatus" role="status" aria-live="polite" class="hidden mt-4 rounded-lg border px-3 py-2 text-sm"></p>

    {{-- Form Pencarian --}}
    <form action="{{ route('pengaduan.track.form') }}" method="GET" class="mt-6 flex w-full gap-2">
        <input
            type="text"
            name="q"
            value="{{ $query }}"
            placeholder="Contoh: K7M2-QX9P"
            maxlength="12"
            autocomplete="off"
            class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-green-600"
        >

        <button
            type="submit"
            class="shrink-0 rounded-lg bg-green-700 px-4 sm:px-5 text-sm sm:text-base font-medium text-white hover:bg-green-800"
        >
            Cari
        </button>
    </form>

    {{-- Riwayat Aduan di Perangkat Ini --}}
    <div id="riwayatBox" class="hidden mt-6">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-semibold text-slate-700">Aduan di perangkat ini</p>
            <button type="button" onclick="openClearHistoryModal(this)"
                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-50">
                Bersihkan Riwayat
            </button>
        </div>

        <div id="riwayatList" class="flex flex-wrap gap-2"></div>
    </div>

    {{-- Hasil Pelacakan --}}
    <div class="mt-6 space-y-4">

        {{-- Pesan Jika Pengaduan Tidak Ditemukan --}}
       @if ($query !== '' && $hasil->isEmpty())
    <div class="text-center text-sm text-slate-500 bg-white border border-slate-200 rounded-2xl shadow-sm py-10">
        Pengaduan tidak ditemukan atau telah dihapus oleh pihak administrasi.<br>
        Pastikan kode pengaduan yang Anda masukkan sudah benar.
    </div>
@endif

        {{-- Kartu Pengaduan --}}
        @foreach ($hasil as $item)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-5">

                {{-- Kode Pengaduan dan Status --}}
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <span class="min-w-0 break-all font-semibold text-green-700">
                        #{{ $item->kode_aduan }}
                    </span>

                    <span class="max-w-full break-words text-right text-xs font-medium px-3 py-1 rounded-full {{ $item->statusColor() }}">
                        {{ $item->statusLabel() }}
                    </span>
                </div>

                {{-- Informasi Pengaduan --}}
                <div class="space-y-2 text-sm">

                    {{-- Lokasi --}}
                    <p class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-x-2">
                        <span class="text-slate-400">Lokasi:</span>
                        <span class="min-w-0 break-words text-slate-700">{{ $item->lokasi }}</span>
                    </p>

                    {{-- Waktu Pengaduan --}}
                    <p class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-x-2">
                        <span class="text-slate-400">Dilaporkan:</span>
                        <span class="min-w-0 break-words text-slate-700">
                            {{ $item->created_at->format('d M Y, H:i') }} WIB
                        </span>
                    </p>

                    {{-- Fasilitas --}}
                    <p class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-x-2">
                        <span class="text-slate-400">Fasilitas:</span>
                        <span class="min-w-0 break-words text-slate-700">{{ $item->fasilitas }}</span>
                    </p>

                    {{-- Keterangan --}}
                    <div class="pt-1">
                        <p class="text-slate-400 mb-1">
                            Keterangan:
                        </p>

                        <p class="break-words text-slate-700 leading-relaxed">
                            {{ $item->deskripsi }}
                        </p>
                    </div>

                </div>

                {{-- Catatan Petugas --}}
                @if ($item->catatan_admin)
                    <div class="mt-4 break-words bg-slate-50 border-l-4 border-green-600 rounded-r-lg px-3 py-2 text-sm text-slate-600">
                        <span class="font-medium text-slate-700">
                            Catatan petugas:
                        </span>

                        {{ $item->catatan_admin }}
                    </div>
                @endif

            </div>
        @endforeach

    </div>
</div>

{{-- Modal Pengaduan Berhasil Terkirim --}}
@if (session('kode_aduan'))
    <div
        id="successModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
    >
        <div class="w-full max-w-sm rounded-2xl bg-white p-5 sm:p-7 text-center shadow-xl">

            {{-- Ikon Berhasil --}}
            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-8 h-8"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>

            <h2 class="text-lg font-bold text-slate-800">
                Pengaduan Berhasil Terkirim!
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Simpan kode ini untuk melacak status perbaikan. Kode tidak bisa dilihat lagi tanpa bantuan admin.
            </p>

            {{-- Kode Pengaduan --}}
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl py-3">
                <p class="text-xs font-medium text-green-700 uppercase tracking-wide">
                    Kode Pengaduan
                </p>

                <p
                    id="kodeAduanText"
                    class="break-all text-2xl sm:text-3xl font-bold text-green-700 tracking-wider mt-1"
                >
                    {{ session('kode_aduan') }}
                </p>
            </div>

            {{-- Tombol Salin Kode --}}
            <button
                type="button"
                onclick="salinKodeAduan(this)"
                class="mt-4 w-full flex items-center justify-center gap-2 border border-green-700 text-green-700 hover:bg-green-50 font-medium rounded-xl py-2.5 transition-colors"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"
                    />
                </svg>

                <span id="salinLabel">Salin Kode</span>
            </button>

            {{-- Tombol Tutup Modal --}}
            <button
                type="button"
                onclick="document.getElementById('successModal').remove()"
                class="mt-2 w-full bg-green-700 hover:bg-green-800 active:bg-green-900 text-white font-medium rounded-xl py-2.5 transition-colors"
            >
                Oke, Mengerti
            </button>

        </div>
    </div>
@endif

<div id="clearHistoryConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-labelledby="clearHistoryConfirmTitle" aria-describedby="clearHistoryConfirmMessage">
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 text-center shadow-xl sm:p-7">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h2 id="clearHistoryConfirmTitle" class="text-lg font-bold text-slate-800">Bersihkan Riwayat?</h2>
        <p id="clearHistoryConfirmMessage" class="mt-2 text-sm leading-relaxed text-slate-500">
            Semua kode pengaduan yang tersimpan di perangkat ini akan dihapus. Data pengaduan tidak akan terhapus.
        </p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" id="cancelClearHistoryButton" onclick="closeClearHistoryModal()"
                    class="w-full rounded-xl border border-slate-300 py-2.5 font-medium text-slate-600 transition-colors hover:bg-slate-50">
                Batal
            </button>
            <button type="button" id="confirmClearHistoryButton" onclick="clearRiwayatAduan()"
                    class="w-full rounded-xl bg-red-600 py-2.5 font-medium text-white transition-colors hover:bg-red-700">
                Ya, Bersihkan
            </button>
        </div>
    </div>
</div>

<div id="clearHistoryNoticeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-labelledby="clearHistoryNoticeTitle" aria-describedby="clearHistoryNoticeMessage">
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 text-center shadow-xl sm:p-7">
        <div id="clearHistoryNoticeIcon" class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 id="clearHistoryNoticeTitle" class="text-lg font-bold text-slate-800">Riwayat Dibersihkan</h2>
        <p id="clearHistoryNoticeMessage" class="mt-2 text-sm leading-relaxed text-slate-500"></p>
        <button type="button" onclick="closeClearHistoryNotice()"
                class="mt-5 w-full rounded-xl bg-green-700 py-2.5 font-medium text-white transition-colors hover:bg-green-800">
            Oke, Mengerti
        </button>
    </div>
</div>

{{-- JavaScript --}}
<script>
    let clearHistoryTrigger = null;

    function openClearHistoryModal(trigger) {
        clearHistoryTrigger = trigger;
        const modal = document.getElementById('clearHistoryConfirmModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        document.getElementById('cancelClearHistoryButton').focus();
    }

    function closeClearHistoryModal() {
        const modal = document.getElementById('clearHistoryConfirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        clearHistoryTrigger?.focus();
    }

    function closeClearHistoryNotice() {
        const modal = document.getElementById('clearHistoryNoticeModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        clearHistoryTrigger?.focus();
    }

    function clearRiwayatAduan() {
        closeClearHistoryModal();
        const noticeModal = document.getElementById('clearHistoryNoticeModal');
        const noticeTitle = document.getElementById('clearHistoryNoticeTitle');
        const noticeMessage = document.getElementById('clearHistoryNoticeMessage');
        const noticeIcon = document.getElementById('clearHistoryNoticeIcon');

        try {
            localStorage.removeItem('riwayatAduan');
            document.getElementById('riwayatList').replaceChildren();
            document.getElementById('riwayatBox').classList.add('hidden');
            noticeTitle.textContent = 'Riwayat Dibersihkan';
            noticeMessage.textContent = 'Semua riwayat kode pengaduan berhasil dihapus dari perangkat ini.';
            noticeIcon.className = 'mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-600';
        } catch (error) {
            noticeTitle.textContent = 'Riwayat Gagal Dibersihkan';
            noticeMessage.textContent = 'Periksa pengaturan penyimpanan browser Anda, lalu coba lagi.';
            noticeIcon.className = 'mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600';
            noticeIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }

        noticeModal.classList.remove('hidden');
        noticeModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    document.getElementById('clearHistoryConfirmModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeClearHistoryModal();
        }
    });

    document.getElementById('clearHistoryNoticeModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeClearHistoryNotice();
        }
    });

    document.addEventListener('keydown', function (event) {
        const confirmModal = document.getElementById('clearHistoryConfirmModal');
        const noticeModal = document.getElementById('clearHistoryNoticeModal');

        if (event.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
            closeClearHistoryModal();
        } else if (event.key === 'Escape' && !noticeModal.classList.contains('hidden')) {
            closeClearHistoryNotice();
        }
    });

    function salinKodeAduan(btn) {
        const kode = document.getElementById('kodeAduanText').textContent.trim();
        const label = document.getElementById('salinLabel');

        navigator.clipboard.writeText(kode).then(function () {
            label.textContent = 'Tersalin!';

            setTimeout(function () {
                label.textContent = 'Salin Kode';
            }, 2000);
        });
    }

    (function () {
        let riwayat = [];

        try {
            riwayat = JSON.parse(
                localStorage.getItem('riwayatAduan') || '[]'
            );
        } catch (e) {}

        // Simpan kode pengaduan yang baru dikirim ke riwayat perangkat.
        const kodeEl = document.getElementById('kodeAduanText');

        if (kodeEl) {
            const kode = kodeEl.textContent.trim();

            if (!riwayat.some(r => r.kode === kode)) {
                riwayat.unshift({
                    kode: kode,
                    tanggal: new Date().toLocaleDateString('id-ID')
                });

                riwayat = riwayat.slice(0, 20);

                try {
                    localStorage.setItem(
                        'riwayatAduan',
                        JSON.stringify(riwayat)
                    );
                } catch (e) {}
            }
        }

        // Tampilkan riwayat pengaduan yang tersimpan.
        if (riwayat.length) {
            const list = document.getElementById('riwayatList');

            document.getElementById('riwayatBox')
                .classList.remove('hidden');

            riwayat.forEach(function (r) {
                const btn = document.createElement('button');

                btn.type = 'button';
                btn.textContent = r.kode + ' · ' + r.tanggal;

                btn.className =
                    'rounded-full border border-slate-300 px-3 py-1 text-xs font-medium hover:bg-green-50 hover:border-green-500 transition-colors';

                btn.onclick = function () {
                    const input = document.querySelector('input[name=q]');

                    input.value = r.kode;
                    input.form.submit();
                };

                list.appendChild(btn);
            });
        }
    })();
</script>
@endsection