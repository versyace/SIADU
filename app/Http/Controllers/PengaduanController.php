<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

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
            'kelas' => ['required', 'string', 'max:50'],
            'lokasi' => ['required', 'string', 'max:150'],
            'fasilitas' => ['required', 'string', 'max:150'],
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
            'fasilitas' => 'Fasilitas yang Dilaporkan',
            'deskripsi' => 'Keterangan Kerusakan',
            'foto' => 'Upload Bukti Foto',
        ]);

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