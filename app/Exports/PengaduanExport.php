<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PengaduanExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return Pengaduan::query()->latest();
    }

    public function headings(): array
    {
        return [
            'Kode Pengaduan',
            'Nama Pelapor',
            'Kelas',
            'Lokasi',
            'Fasilitas',
            'Deskripsi',
            'Status',
            'Tanggal Pengaduan',
            'Catatan Admin',
        ];
    }

    public function map($pengaduan): array
    {
        return [
            $pengaduan->kode_aduan ?? '',
            $pengaduan->nama,
            $pengaduan->kelas,
            $pengaduan->lokasi,
            $pengaduan->fasilitas,
            $pengaduan->deskripsi,
            $pengaduan->statusLabel(),
            $pengaduan->created_at?->format('d M Y, H:i'),
            $pengaduan->catatan_admin ?? '',
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

        return true;
    }
}
