<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PengaduanExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $month = null,
        private readonly ?int $year = null,
    ) {}

    public function query(): Builder
    {
        return Pengaduan::query()
            ->when($this->year !== null && $this->month !== null, function (Builder $query) {
                $start = now()->setDate($this->year, $this->month, 1)->startOfDay();
                $query->where('created_at', '>=', $start)
                    ->where('created_at', '<', $start->copy()->addMonth());
            })
            ->when($this->year !== null && $this->month === null, function (Builder $query) {
                $start = now()->setDate($this->year, 1, 1)->startOfDay();
                $query->where('created_at', '>=', $start)
                    ->where('created_at', '<', $start->copy()->addYear());
            })
            ->when($this->month !== null && $this->year === null, function (Builder $query) {
                $query->whereMonth('created_at', $this->month);
            })
            ->latest();
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:I{$lastRow}");
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
                    'alignment' => ['vertical' => 'top'],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '166534'],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setWrapText(true);

                $statusStyles = [
                    'Dilaporkan' => ['DBEAFE', '1D4ED8'],
                    'Proses' => ['FEF3C7', 'B45309'],
                    'Selesai' => ['DCFCE7', '15803D'],
                ];

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 1) {
                        $sheet->getStyle("A{$row}:I{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8FAFC');
                    }

                    $status = $sheet->getCell("G{$row}")->getValue();
                    if (isset($statusStyles[$status])) {
                        [$background, $foreground] = $statusStyles[$status];
                        $sheet->getStyle("G{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => $foreground]],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $background],
                            ],
                            'alignment' => ['horizontal' => 'center'],
                        ]);
                    }
                }
            },
        ];
    }
}
