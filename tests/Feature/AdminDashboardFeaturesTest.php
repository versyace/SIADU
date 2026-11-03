<?php

namespace Tests\Feature;

use App\Exports\PengaduanExport;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AdminDashboardFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_reports_six_months_in_order_with_zeroes_for_empty_months(): void
    {
        Carbon::setTestNow('2026-11-02 12:00:00');
        $this->actingAs($this->makeAdmin());

        $this->createPengaduan([
            'kode_aduan' => 'TEST-JUN',
            'created_at' => '2026-06-10 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-NOV',
            'created_at' => '2026-11-01 09:00:00',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk()
            ->assertSee('Juni 2026')
            ->assertSee('November 2026')
            ->assertSee('Statistik Pengaduan per Bulan');

        $view = app(DashboardController::class)->index(Request::create('/admin/dashboard'));

        $this->assertSame([
            'Juni 2026',
            'Juli 2026',
            'Agustus 2026',
            'September 2026',
            'Oktober 2026',
            'November 2026',
        ], $view->getData()['chartLabels']);
        $this->assertSame([1, 0, 0, 0, 0, 1], $view->getData()['chartData']);
    }

    public function test_excel_download_is_admin_protected_and_contains_all_pengaduans(): void
    {
        $this->get(route('admin.pengaduan.export'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($this->makeAdmin());

        for ($number = 1; $number <= 12; $number++) {
            $this->createPengaduan([
                'kode_aduan' => sprintf('TEST-%02d', $number),
                'nama' => 'Pelapor '.$number,
                'created_at' => now()->subDays($number),
            ]);
        }

        $this->get(route('admin.pengaduan.export'))
            ->assertDownload('rekap-pengaduan-'.now()->format('Y-m-d').'.xlsx');

        $xlsx = Excel::raw(new PengaduanExport, ExcelFormat::XLSX);
        $temporaryFile = tempnam(sys_get_temp_dir(), 'pengaduan-xlsx-');

        $this->assertNotFalse($temporaryFile);

        try {
            file_put_contents($temporaryFile, $xlsx);
            $sheet = IOFactory::load($temporaryFile)->getActiveSheet();

            $this->assertSame('Kode Pengaduan', $sheet->getCell('A1')->getValue());
            $this->assertSame('TEST-01', $sheet->getCell('A2')->getValue());
            $this->assertSame('Catatan Admin', $sheet->getCell('I1')->getValue());
            $this->assertSame(13, $sheet->getHighestRow());
        } finally {
            unlink($temporaryFile);
        }
    }

    private function pengaduanData(array $overrides = []): array
    {
        return array_merge([
            'kode_aduan' => 'TEST-CODE',
            'nama' => 'Pelapor',
            'kelas' => 'X',
            'lokasi' => 'Ruang kelas',
            'fasilitas' => 'Kursi',
            'deskripsi' => 'Deskripsi pengaduan',
            'status' => 'dilaporkan',
            'catatan_admin' => null,
        ], $overrides);
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->make();
        $admin->username = 'test-admin';
        $admin->save();

        return $admin;
    }

    private function createPengaduan(array $overrides = []): Pengaduan
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $pengaduan = Pengaduan::create($this->pengaduanData($overrides));

        if ($createdAt !== null) {
            $pengaduan->forceFill(['created_at' => $createdAt])->save();
        }

        return $pengaduan;
    }
}
