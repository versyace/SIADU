@extends('layouts.admin')

@section('title', 'Detail Pengaduan #' . $pengaduan->kode_aduan)

@section('content')
<a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-500 hover:text-green-700">&larr; Kembali ke Dashboard</a>

<div class="flex items-center gap-3 mt-2">
    <h1 class="text-2xl font-bold text-slate-800">Tiket Pengaduan #{{ $pengaduan->kode_aduan }}</h1>
    <span class="text-xs font-medium px-3 py-1 rounded-full {{ $pengaduan->statusColor() }}">
        {{ $pengaduan->statusLabel() }}
    </span>
</div>
<p class="text-sm text-slate-500 mt-1">
    Dilaporkan pada {{ $pengaduan->created_at->format('d M Y, H:i') }} WIB oleh {{ $pengaduan->nama }}
</p>

<div class="grid lg:grid-cols-3 gap-6 mt-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-slate-800 mb-4">Informasi Siswa & Sarana</h2>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-400">Nama Siswa / Pelapor</p>
                    <p class="font-medium text-slate-700">{{ $pengaduan->nama }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Kelas</p>
                    <p class="font-medium text-slate-700">{{ $pengaduan->kelas }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Lokasi Sarana</p>
                    <p class="font-medium text-slate-700">{{ $pengaduan->lokasi }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Fasilitas</p>
                    <p class="font-medium text-slate-700">{{ $pengaduan->fasilitas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-slate-800 mb-3">Keterangan Kerusakan</h2>
            <p class="text-sm text-slate-600">"{{ $pengaduan->deskripsi }}"</p>
        </div>

        @if ($pengaduan->foto)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="font-semibold text-slate-800 mb-3">Bukti Foto Kerusakan</h2>
                <img src="{{ Storage::url($pengaduan->foto) }}" alt="Bukti foto"
                     class="rounded-lg border border-slate-200 max-h-96">
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 h-fit">
        <h2 class="font-semibold text-slate-800 mb-1">Tindakan & Status Pengaduan</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui status agar siswa bisa memantau progresnya.</p>

        <form action="{{ route('admin.pengaduan.update', $pengaduan) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Status Pengaduan</label>
                <div class="relative">
                    <select name="status"
                            class="w-full appearance-none rounded-xl border border-slate-300 bg-white pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
                        <option value="dilaporkan" {{ $pengaduan->status === 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                        <option value="proses" {{ $pengaduan->status === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ $pengaduan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Petugas</label>
                <textarea name="catatan_admin" rows="4" placeholder="Contoh: Teknisi sedang mengganti komponen..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">{{ $pengaduan->catatan_admin }}</textarea>
                <p class="text-xs text-slate-400 mt-1.5">Catatan ini akan tampil di halaman lacak milik siswa.</p>
            </div>

            <button type="submit"
                    class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900 text-white font-medium rounded-xl py-3 shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection