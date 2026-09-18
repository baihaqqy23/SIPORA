<?php

namespace Tests\Feature;

use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\NomorLomba;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\PenugasanPanitia;
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

    public function test_pj_cabor_context_switching_event_and_cabor(): void
    {
        $event2 = Event::create([
            'nama' => 'PORDA 2026',
            'slug' => 'porda-2026-test',
            'tanggal_mulai' => now()->addDays(30),
            'tanggal_selesai' => now()->addDays(40),
            'kategori_usia' => 'senior',
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(15),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor2 = CabangOlahraga::create([
            'event_id' => $event2->id,
            'nama' => 'Karate',
            'singkatan' => 'KRT',
            'warna' => '#0284C7',
        ]);

        $panitia2 = Panitia::create([
            'event_id' => $event2->id,
            'user_id' => $this->pjUser->id,
            'nama' => $this->pjUser->name,
            'jabatan' => 'PJ Karate',
            'no_hp' => '081234567899',
        ]);

        PenugasanPanitia::create([
            'panitia_id' => $panitia2->id,
            'cabang_olahraga_id' => $cabor2->id,
            'peran' => 'pj_cabor',
        ]);

        // Verify eventsDitugaskan
        $events = $this->pjUser->eventsDitugaskan();
        $this->assertGreaterThanOrEqual(2, $events->count());

        // Test dashboard with event_id & cabor_id query params
        $response = $this->actingAs($this->pjUser)->get(route('cabor.dashboard', [
            'event_id' => $event2->id,
            'cabor_id' => $cabor2->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('PORDA 2026');
        $response->assertSee('Karate');
        $this->assertEquals($event2->id, session('pj_cabor_active_event_id'));
        $this->assertEquals($cabor2->id, session('pj_cabor_active_cabor_id'));
    }
}
