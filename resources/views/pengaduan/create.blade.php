@extends('layouts.app')

@section('title', 'Form Pengaduan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <span class="inline-block text-xs font-medium text-green-700 bg-green-50 rounded-full px-3 py-1 mb-3">Layanan Terpadu &bull; Respons Cepat</span>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">Form Pengaduan Sarana Sekolah</h1>
        <p class="text-slate-500 mt-2 text-sm leading-relaxed">
            Laporkan kerusakan atau kendala fasilitas sarana dan prasarana sekolah agar segera ditindaklanjuti.
        </p>
    </div>

    <form id="pengaduanForm" action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" novalidate
          class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <label for="nama" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap Siswa</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Muhammad Rizky" required data-label="Nama Lengkap Siswa"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
            <p id="nama-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <div>
            <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1.5">Kelas</label>
            <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: X PPLG A" required data-label="Kelas"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
            <p id="kelas-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <div>
            <label for="lokasi" class="block text-sm font-medium text-slate-700 mb-1.5">Lokasi Sarana</label>
            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Lab RPL 3" required data-label="Lokasi Sarana"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
            <p id="lokasi-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <div>
            <label for="fasilitas" class="block text-sm font-medium text-slate-700 mb-1.5">Fasilitas yang Dilaporkan</label>
            <input type="text" id="fasilitas" name="fasilitas" value="{{ old('fasilitas') }}" placeholder="Contoh: Komputer PC-08" required data-label="Fasilitas yang Dilaporkan"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
            <p id="fasilitas-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-1.5">Keterangan Kerusakan</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan kendala secara singkat dan jelas..." required data-label="Keterangan Kerusakan"
                      class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">{{ old('deskripsi') }}</textarea>
            <p id="deskripsi-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Upload Bukti Foto</label>
            <label for="foto" id="dropzone"
                   class="group flex flex-col items-center justify-center gap-2 w-full rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-green-50/60 hover:border-green-400 transition-colors cursor-pointer py-8 px-4 text-center">
                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-green-700 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 17a4 4 0 100-8 4 4 0 000 8z" />
                    </svg>
                </div>
                <p class="text-sm text-slate-600"><span class="font-medium text-green-700">Klik untuk upload</span> atau seret foto ke sini</p>
                <p id="foto-filename" class="text-xs text-slate-400">Belum ada file dipilih &bull; Maks. 5MB, format JPG/PNG</p>
                <img id="foto-preview" class="hidden mt-1 max-h-32 rounded-lg border border-slate-200" alt="Pratinjau foto">
                <input type="file" id="foto" name="foto" accept="image/*" required data-label="Upload Bukti Foto"
                       class="hidden" onchange="handleFotoChange(this)">
            </label>
            <p id="foto-error" class="hidden text-xs text-red-600 mt-1.5"></p>
        </div>

        <button type="submit"
                class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900 text-white font-medium rounded-xl py-3 shadow-sm transition-colors">
            Kirim Pengaduan
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        Sudah punya kode pengaduan?
        <a href="{{ route('pengaduan.track.form') }}" class="text-green-700 font-medium hover:underline">
            Lacak status pengaduan Anda di sini &rarr;
        </a>
    </p>
</div>

<script>
    function handleFotoChange(input) {
        const label = document.getElementById('foto-filename');
        const preview = document.getElementById('foto-preview');
        const error = document.getElementById('foto-error');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            label.textContent = file.name + ' • siap diunggah';
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
            error.classList.add('hidden');
            document.getElementById('dropzone').classList.remove('border-red-400');
        }
    }

    (function () {
        const dropzone = document.getElementById('dropzone');
        const fotoInput = document.getElementById('foto');

        ['dragover', 'dragenter'].forEach(evt => dropzone.addEventListener(evt, function (e) {
            e.preventDefault();
            dropzone.classList.add('border-green-500', 'bg-green-50');
        }));
        ['dragleave', 'dragend'].forEach(evt => dropzone.addEventListener(evt, function () {
            dropzone.classList.remove('border-green-500', 'bg-green-50');
        }));
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('border-green-500', 'bg-green-50');
            if (e.dataTransfer.files.length) {
                fotoInput.files = e.dataTransfer.files;
                handleFotoChange(fotoInput);
            }
        });

        document.getElementById('pengaduanForm').addEventListener('submit', function (e) {
            let valid = true;
            this.querySelectorAll('[data-label]').forEach(function (field) {
                const errorEl = document.getElementById(field.id + '-error');
                const isEmpty = field.type === 'file' ? field.files.length === 0 : field.value.trim() === '';
                const box = field.id === 'foto' ? document.getElementById('dropzone') : field;

                if (isEmpty) {
                    valid = false;
                    if (errorEl) {
                        errorEl.textContent = 'Kolom ' + field.dataset.label + ' wajib diisi.';
                        errorEl.classList.remove('hidden');
                    }
                    box.classList.add('border-red-400');
                } else {
                    if (errorEl) errorEl.classList.add('hidden');
                    box.classList.remove('border-red-400');
                }
            });

            if (!valid) {
                e.preventDefault();
                const firstError = this.querySelector('.border-red-400');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    })();
</script>
@endsection