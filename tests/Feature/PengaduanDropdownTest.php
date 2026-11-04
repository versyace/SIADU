<?php

namespace Tests\Feature;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaduanDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_dropdown_choices_are_shown_and_standard_values_are_saved(): void
    {
        Storage::fake('public');

        $this->get(route('pengaduan.create'))
            ->assertOk()
            ->assertSee('<option value="" disabled selected', false)
            ->assertSee('Pilih kelas</option>', false)
            ->assertSee('XI RPL A')
            ->assertSee('XII RPL C')
            ->assertSee('Pilih lokasi')
            ->assertSee('Lab ASM 3')
            ->assertSee('Pilih kategori fasilitas')
            ->assertSee('Proyektor');

        $this->post(route('pengaduan.store'), $this->validSubmission([
            'kelas' => 'XI RPL B',
            'lokasi' => 'R202',
            'fasilitas' => 'Monitor',
        ]))->assertRedirect(route('pengaduan.track.form'));

        $this->assertDatabaseHas('pengaduans', [
            'kelas' => 'XI RPL B',
            'lokasi' => 'R202',
            'fasilitas' => 'Monitor',
        ]);
    }

    public function test_other_location_and_facility_details_are_saved_and_displayed(): void
    {
        Storage::fake('public');

        $this->post(route('pengaduan.store'), $this->validSubmission([
            'lokasi' => 'Lainnya',
            'lokasi_lainnya' => 'Perpustakaan',
            'fasilitas' => 'Lainnya',
            'fasilitas_lainnya' => 'Keyboard',
        ]))->assertRedirect(route('pengaduan.track.form'));

        $pengaduan = Pengaduan::query()->firstOrFail();
        $this->assertSame('Lainnya: Perpustakaan', $pengaduan->lokasi);
        $this->assertSame('Lainnya: Keyboard', $pengaduan->fasilitas);

        $this->actingAs($this->makeAdmin())
            ->get(route('admin.pengaduan.show', $pengaduan))
            ->assertOk()
            ->assertSee('Lainnya: Perpustakaan')
            ->assertSee('Lainnya: Keyboard');

        $this->get(route('pengaduan.track.form', ['q' => $pengaduan->kode_aduan]))
            ->assertOk()
            ->assertSee('Lainnya: Perpustakaan')
            ->assertSee('Lainnya: Keyboard');
    }

    public function test_custom_dropdown_values_are_rejected_and_other_details_are_required(): void
    {
        Storage::fake('public');

        $this->from(route('pengaduan.create'))
            ->post(route('pengaduan.store'), $this->validSubmission([
                'kelas' => 'Kelas Palsu',
                'lokasi' => 'R999',
                'lokasi_lainnya' => '',
                'fasilitas' => 'Fasilitas Palsu',
                'fasilitas_lainnya' => '',
            ]))
            ->assertRedirect(route('pengaduan.create'))
            ->assertSessionHasErrors(['kelas', 'lokasi', 'fasilitas']);

        $this->from(route('pengaduan.create'))
            ->post(route('pengaduan.store'), $this->validSubmission([
                'lokasi' => 'Lainnya',
                'lokasi_lainnya' => '',
                'fasilitas' => 'Lainnya',
                'fasilitas_lainnya' => '',
            ]))
            ->assertRedirect(route('pengaduan.create'))
            ->assertSessionHasErrors(['lokasi_lainnya', 'fasilitas_lainnya']);

        $this->assertDatabaseCount('pengaduans', 0);
    }

    private function validSubmission(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Siswa Contoh',
            'kelas' => 'XI RPL A',
            'lokasi' => 'R201',
            'fasilitas' => 'AC',
            'deskripsi' => 'AC tidak berfungsi.',
            'foto' => UploadedFile::fake()->image('bukti.jpg'),
        ], $overrides);
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->make();
        $admin->username = 'test-admin';
        $admin->save();

        return $admin;
    }
}
