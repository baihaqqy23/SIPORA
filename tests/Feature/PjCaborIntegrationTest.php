<?php

namespace Tests\Feature;

use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PjCaborIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $pjUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->pjUser = User::where('role', 'pj_cabor')->first();
    }

    public function test_pj_cabor_redirects_to_cabor_dashboard_on_login(): void
    {
        $this->assertNotNull($this->pjUser);

        $response = $this->post('/login', [
            'email' => $this->pjUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cabor.dashboard'));
        $this->assertAuthenticatedAs($this->pjUser);
    }

    public function test_pj_cabor_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->pjUser)->get(route('cabor.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Portal PJ Cabor');
    }

    public function test_pj_cabor_peserta_pages_render_and_can_verify(): void
    {
        // 1. Index
        $this->actingAs($this->pjUser)->get(route('cabor.peserta.index'))->assertStatus(200);

        // 2. Verifikasi show & proses
        $pendaftaran = Pendaftaran::where('status', 'menunggu')->first() ?? Pendaftaran::first();
        if ($pendaftaran) {
            $this->actingAs($this->pjUser)->get(route('cabor.peserta.verifikasi', $pendaftaran))->assertStatus(200);

            $this->actingAs($this->pjUser)->post(route('cabor.peserta.verifikasi.proses', $pendaftaran), [
                'status' => 'disetujui',
                'catatan' => 'Berkas absah lengkap',
            ])->assertRedirect(route('cabor.peserta.index'));

            $this->assertDatabaseHas('pendaftaran', [
                'id' => $pendaftaran->id,
                'status' => 'disetujui',
            ]);
        }
    }

    public function test_pj_cabor_jadwal_and_hasil_pages(): void
    {
        $this->actingAs($this->pjUser)->get(route('cabor.jadwal.index'))->assertStatus(200);

        $pertandingan = Pertandingan::first();
        if ($pertandingan) {
            $this->actingAs($this->pjUser)->get(route('cabor.hasil.show', $pertandingan))->assertStatus(200);
        }
    }

    public function test_pj_cabor_bracket_page(): void
    {
        $nomorLomba = NomorLomba::first();
        $this->actingAs($this->pjUser)->get(route('cabor.bracket.show', $nomorLomba))->assertStatus(200);
    }
}
