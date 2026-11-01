<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = [
        'kode_aduan',
        'nama',
        'kelas',
        'lokasi',
        'fasilitas',
        'deskripsi',
        'foto',
        'status',
        'catatan_admin',
    ];

    // Buat kode acak unik, contoh: K7M2-QX9P (tanpa huruf/angka yang mirip: 0, O, 1, I, L)
    public static function buatKode(): string
    {
        $huruf = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $kode = '';
            for ($i = 0; $i < 8; $i++) {
                $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
            }
            $kode = substr($kode, 0, 4) . '-' . substr($kode, 4);
        } while (static::where('kode_aduan', $kode)->exists());

        return $kode;
    }

    // Label rapi untuk ditampilkan di badge status
    public function statusLabel(): string
    {
        return match ($this->status) {
            'dilaporkan' => 'Dilaporkan',
            'proses' => 'Proses',
            'selesai' => 'Selesai',
            default => ucfirst($this->status),
        };
    }

    // Kelas warna badge Tailwind sesuai status
    public function statusColor(): string
    {
        return match ($this->status) {
            'dilaporkan' => 'bg-blue-100 text-blue-700',
            'proses' => 'bg-amber-100 text-amber-700',
            'selesai' => 'bg-green-100 text-green-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}