<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengaduanController extends Controller
{
    // Tampilkan form pengaduan (halaman utama, tanpa login)
    public function create()
    {
        return view('pengaduan.create');
    }

    // Simpan pengaduan baru dari siswa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', Rule::in([
                'XI RPL A',
                'XI RPL B',
                'XI RPL C',
                'XII RPL A',
                'XII RPL B',
                'XII RPL C',
            ])],
            'lokasi' => ['required', Rule::in([
                'R201',
                'R202',
                'R203',
                'Lab ASM 1',
                'Lab ASM 2',
                'Lab ASM 3',
                'Lainnya',
            ])],
            'lokasi_lainnya' => ['required_if:lokasi,Lainnya', 'nullable', 'string', 'max:140'],
            'fasilitas' => ['required', Rule::in([
                'AC',
                'Internet',
                'Monitor',
                'Proyektor',
                'Mouse',
                'PC',
                'Lainnya',
            ])],
            'fasilitas_lainnya' => ['required_if:fasilitas,Lainnya', 'nullable', 'string', 'max:140'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'foto' => ['required', 'image', 'max:5120'], // maks 5MB
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'image' => 'Kolom :attribute harus berupa file gambar.',
            'max' => 'Kolom :attribute maksimal :max.',
        ], [
            'nama' => 'Nama Lengkap Siswa',
            'kelas' => 'Kelas',
            'lokasi' => 'Lokasi Sarana',
            'lokasi_lainnya' => 'Keterangan Lokasi',
            'fasilitas' => 'Fasilitas yang Dilaporkan',
            'fasilitas_lainnya' => 'Keterangan Fasilitas',
            'deskripsi' => 'Keterangan Kerusakan',
            'foto' => 'Upload Bukti Foto',
        ]);

        if ($validated['lokasi'] === 'Lainnya') {
            $validated['lokasi'] = $validated['lokasi_lainnya'];
        }

        if ($validated['fasilitas'] === 'Lainnya') {
            $validated['fasilitas'] = $validated['fasilitas_lainnya'];
        }

        unset($validated['lokasi_lainnya'], $validated['fasilitas_lainnya']);

        $path = $request->file('foto')->store('bukti-pengaduan', 'public');

        $pengaduan = Pengaduan::create([
            ...$validated,
            'kode_aduan' => Pengaduan::buatKode(),
            'foto' => $path,
            'status' => 'dilaporkan',
        ]);

        return redirect()
            ->route('pengaduan.track.form')
            ->with('success', 'Pengaduan berhasil dikirim! Kode aduanmu: ' . $pengaduan->kode_aduan)
            ->with('kode_aduan', $pengaduan->kode_aduan);
    }

    // Halaman lacak: hanya bisa dicari lewat kode aduan (tidak bisa lewat nama)
    public function track(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:20']]);

        // rapikan input: huruf besar, buang spasi dan tanda hubung
        $query = strtoupper(preg_replace('/[\s-]+/', '', (string) $request->get('q', '')));

        // kalau 8 karakter, tambahkan tanda hubung di tengah (K7M2QX9P -> K7M2-QX9P)
        if (strlen($query) === 8) {
            $query = substr($query, 0, 4) . '-' . substr($query, 4);
        }

        $hasil = collect();

        if ($query !== '') {
            $hasil = Pengaduan::where('kode_aduan', $query)->get();
        }

        return view('pengaduan.track', [
            'query' => $query,
            'hasil' => $hasil,
        ]);
    }
}