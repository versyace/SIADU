<?php

namespace Tests\Feature;

use App\Exports\PengaduanExport;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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

    public function test_dashboard_reports_all_months_for_selected_year_with_zeroes_for_empty_months(): void
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
        $this->createPengaduan([
            'kode_aduan' => 'TEST-JAN-2025',
            'created_at' => '2025-01-15 09:00:00',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk()
            ->assertSee('Januari')
            ->assertSee('Desember')
            ->assertSee('Statistik Pengaduan per Bulan');

        $view = app(DashboardController::class)->index(Request::create('/admin/dashboard'));

        $this->assertSame([
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ], $view->getData()['chartLabels']);
        $this->assertSame([0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0], $view->getData()['chartData']);

        $selectedYearResponse = $this->get(route('admin.dashboard', ['tahun' => 2025]));
        $selectedYearResponse->assertOk()
            ->assertViewHas('chartYear', 2025)
            ->assertViewHas('chartData', [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
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

    public function test_excel_download_can_be_filtered_by_month_and_styles_each_status(): void
    {
        Carbon::setTestNow('2026-11-04 08:00:00');
        $this->actingAs($this->makeAdmin());

        $this->createPengaduan([
            'kode_aduan' => 'TEST-OCT-REPORT',
            'status' => 'dilaporkan',
            'created_at' => '2026-10-03 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-OCT-PROCESS',
            'status' => 'proses',
            'created_at' => '2026-10-12 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-OCT-DONE',
            'status' => 'selesai',
            'created_at' => '2026-10-25 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-NOV',
            'created_at' => '2026-11-02 09:00:00',
        ]);

        $this->get(route('admin.pengaduan.export', ['bulan' => 10, 'tahun' => 2026]))
            ->assertDownload('rekap-pengaduan-oktober-2026-2026-11-04.xlsx');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pilih bulan rekap')
            ->assertSee('Semua Data')
            ->assertSee('Pilih tahun rekap');

        $temporaryFile = tempnam(sys_get_temp_dir(), 'pengaduan-monthly-xlsx-');
        $this->assertNotFalse($temporaryFile);

        try {
            file_put_contents($temporaryFile, Excel::raw(
                new PengaduanExport(10, 2026),
                ExcelFormat::XLSX
            ));
            $sheet = IOFactory::load($temporaryFile)->getActiveSheet();

            $this->assertSame(4, $sheet->getHighestRow());
            $this->assertSame('TEST-OCT-DONE', $sheet->getCell('A2')->getValue());
            $this->assertSame('Selesai', $sheet->getCell('G2')->getValue());
            $this->assertSame('TEST-OCT-PROCESS', $sheet->getCell('A3')->getValue());
            $this->assertSame('Proses', $sheet->getCell('G3')->getValue());
            $this->assertSame('Dilaporkan', $sheet->getCell('G4')->getValue());
            $this->assertSame('166534', $sheet->getStyle('A1')->getFill()->getStartColor()->getRGB());
            $this->assertSame('DCFCE7', $sheet->getStyle('G2')->getFill()->getStartColor()->getRGB());
            $this->assertSame('FEF3C7', $sheet->getStyle('G3')->getFill()->getStartColor()->getRGB());
            $this->assertSame('DBEAFE', $sheet->getStyle('G4')->getFill()->getStartColor()->getRGB());
        } finally {
            unlink($temporaryFile);
        }
    }

    public function test_month_and_year_export_selectors_also_filter_the_dashboard_list(): void
    {
        Carbon::setTestNow('2026-11-04 08:00:00');
        $this->actingAs($this->makeAdmin());

        $this->createPengaduan([
            'kode_aduan' => 'TEST-OCT-2026',
            'created_at' => '2026-10-03 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-NOV-2026',
            'created_at' => '2026-11-02 09:00:00',
        ]);
        $this->createPengaduan([
            'kode_aduan' => 'TEST-OCT-2025',
            'created_at' => '2025-10-03 09:00:00',
        ]);

        $this->get(route('admin.dashboard', [
            'filter_bulan' => 10,
            'filter_tahun' => 2026,
        ]))
            ->assertOk()
            ->assertSee('TEST-OCT-2026')
            ->assertDontSee('TEST-NOV-2026')
            ->assertDontSee('TEST-OCT-2025')
            ->assertSee('value="10" selected', false)
            ->assertSee('value="2026" selected', false);

        $this->get(route('admin.dashboard', ['filter_tahun' => 2025]))
            ->assertOk()
            ->assertSee('TEST-OCT-2025')
            ->assertDontSee('TEST-OCT-2026')
            ->assertDontSee('TEST-NOV-2026');

        $this->get(route('admin.dashboard', ['filter_bulan' => 10]))
            ->assertOk()
            ->assertSee('TEST-OCT-2026')
            ->assertSee('TEST-OCT-2025')
            ->assertDontSee('TEST-NOV-2026');
    }

    public function test_admin_can_delete_one_pengaduan_and_its_photo_without_deleting_other_rows(): void
    {
        Storage::fake('public');
        $this->actingAs($this->makeAdmin());
        $foto = 'bukti-pengaduan/selected.jpg';
        Storage::disk('public')->put($foto, 'photo');

        $selected = $this->createPengaduan([
            'kode_aduan' => 'TEST-DELETE',
            'foto' => $foto,
        ]);
        $remaining = $this->createPengaduan(['kode_aduan' => 'TEST-KEEP']);

        $this->from(route('admin.dashboard'))
            ->delete(route('admin.pengaduan.destroy', $selected))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('pengaduans', ['id' => $selected->id]);
        $this->assertDatabaseHas('pengaduans', ['id' => $remaining->id]);
        Storage::disk('public')->assertMissing($foto);
    }

    public function test_admin_can_delete_all_pengaduans_and_their_photos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->makeAdmin());

        foreach (['one.jpg', 'two.jpg'] as $filename) {
            $foto = 'bukti-pengaduan/'.$filename;
            Storage::disk('public')->put($foto, 'photo');
            $this->createPengaduan([
                'kode_aduan' => 'TEST-'.strtoupper(pathinfo($filename, PATHINFO_FILENAME)),
                'foto' => $foto,
            ]);
        }

        $this->delete(route('admin.pengaduan.destroyAll'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success', 'Seluruh data pengaduan berhasil dihapus.');

        $this->assertDatabaseCount('pengaduans', 0);
        Storage::disk('public')->assertMissing('bukti-pengaduan/one.jpg');
        Storage::disk('public')->assertMissing('bukti-pengaduan/two.jpg');
    }

    public function test_delete_routes_require_admin_authentication(): void
    {
        $pengaduan = $this->createPengaduan(['kode_aduan' => 'TEST-PROTECTED']);

        $this->delete(route('admin.pengaduan.destroy', $pengaduan))
            ->assertRedirect(route('admin.login'));
        $this->delete(route('admin.pengaduan.destroyAll'))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('pengaduans', ['id' => $pengaduan->id]);
    }

    public function test_public_pages_show_privacy_notice_without_exposing_reporter_identity(): void
    {
        $this->get(route('pengaduan.create'))
            ->assertOk()
            ->assertSee('✓ Privasi Pelapor Terjaga')
            ->assertSee('Nama dan kelas tidak akan ditampilkan kepada publik.');

        $pengaduan = $this->createPengaduan([
            'kode_aduan' => 'TEST-PRIVACY',
            'nama' => 'Nama Rahasia',
            'kelas' => 'Kelas Rahasia',
        ]);

        $this->get(route('pengaduan.track.form', ['q' => $pengaduan->kode_aduan]))
            ->assertOk()
            ->assertDontSee('Nama Rahasia')
            ->assertDontSee('Kelas Rahasia');
    }

    public function test_tracking_page_clear_history_only_removes_browser_history(): void
    {
        $this->get(route('pengaduan.track.form'))
            ->assertOk()
            ->assertSee('Bersihkan Riwayat')
            ->assertSee('clearHistoryConfirmModal')
            ->assertSee('clearHistoryNoticeModal')
            ->assertSee("localStorage.removeItem('riwayatAduan')", false)
            ->assertDontSee('window.confirm', false);
    }

    public function test_admin_delete_actions_use_custom_confirmation_and_feedback_modals(): void
    {
        $this->actingAs($this->makeAdmin());
        $pengaduan = $this->createPengaduan(['kode_aduan' => 'TEST-MODAL']);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('deleteConfirmModal')
            ->assertSee('data-confirm-message="Hapus pengaduan #TEST-MODAL beserta foto buktinya?"', false)
            ->assertSee('data-confirm-message="PERINGATAN: Seluruh data pengaduan akan dihapus permanen.', false)
            ->assertDontSee('window.confirm', false)
            ->assertDontSee('onsubmit="return confirm', false);

        $this->delete(route('admin.pengaduan.destroy', $pengaduan))
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('actionNoticeModal')
            ->assertSee('Pengaduan berhasil dihapus.');
    }

    public function test_admin_detail_ambience_changes_with_report_status(): void
    {
        $this->actingAs($this->makeAdmin());
        $pengaduan = $this->createPengaduan(['kode_aduan' => 'TEST-STATUS-AMBIENCE']);
        $expectedGradients = [
            'dilaporkan' => 'from-blue-50',
            'proses' => 'from-amber-50',
            'selesai' => 'from-green-50',
        ];

        foreach ($expectedGradients as $status => $gradient) {
            $this->put(route('admin.pengaduan.update', $pengaduan), [
                'status' => $status,
                'catatan_admin' => '',
            ])->assertRedirect();

            $this->get(route('admin.pengaduan.show', $pengaduan))
                ->assertOk()
                ->assertSee($gradient);
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
