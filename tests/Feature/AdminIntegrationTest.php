<?php

namespace Tests\Feature;

use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\NomorLomba;
use App\Models\Notifikasi;
use App\Models\Panitia;
use App\Models\Pengumuman;
use App\Models\Pertandingan;
use App\Models\User;
use App\Models\Venue;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->adminUser = User::where('role', 'admin')->first();
    }

    public function test_admin_dashboard_renders_with_statistics(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    public function test_event_crud_lifecycle(): void
    {
        // 1. Index & Create page
        $this->actingAs($this->adminUser)->get(route('admin.event.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.event.create'))->assertStatus(200);

        // 2. Store
        $response = $this->actingAs($this->adminUser)->post(route('admin.event.store'), [
            'nama' => 'Pekan Olahraga Daerah 2026',
            'deskripsi' => 'Event uji coba integrasi',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-10',
            'pendaftaran_mulai' => '2026-09-01',
            'pendaftaran_selesai' => '2026-09-25',
            'tanggal_patokan_umur' => '2026-10-01',
            'maks_nomor_lomba_per_atlet' => 3,
            'status' => 'draft',
        ]);
        $response->assertRedirect();
        $event = Event::where('nama', 'Pekan Olahraga Daerah 2026')->first();
        $this->assertNotNull($event);
        $response->assertRedirect(route('admin.event.show', $event));

        // 3. Show & Edit page
        $this->actingAs($this->adminUser)->get(route('admin.event.show', $event))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.event.edit', $event))->assertStatus(200);

        // 4. Update
        $this->actingAs($this->adminUser)->put(route('admin.event.update', $event), [
            'nama' => 'Pekan Olahraga Daerah 2026 Update',
            'deskripsi' => 'Deskripsi update',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-10',
            'pendaftaran_mulai' => '2026-09-01',
            'pendaftaran_selesai' => '2026-09-25',
            'tanggal_patokan_umur' => '2026-10-01',
            'maks_nomor_lomba_per_atlet' => 4,
            'status' => 'pendaftaran_dibuka',
        ])->assertRedirect(route('admin.event.show', $event));

        $this->assertDatabaseHas('events', ['nama' => 'Pekan Olahraga Daerah 2026 Update', 'status' => 'pendaftaran_dibuka']);
    }

    public function test_cabor_and_nomor_lomba_pages(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.cabor.index'))->assertStatus(200);

        $cabor = CabangOlahraga::first();
        $this->assertNotNull($cabor);

        $this->actingAs($this->adminUser)->get(route('admin.cabor.show', $cabor))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.cabor.nomor-lomba.index', $cabor))->assertStatus(200);

        $nomorLomba = NomorLomba::first();
        $this->assertNotNull($nomorLomba);
        $this->actingAs($this->adminUser)->get(route('admin.nomor-lomba.show', $nomorLomba))->assertRedirect(route('admin.cabor.show', $nomorLomba->cabang_olahraga_id));
    }

    public function test_venue_and_lapangan_management(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.venue.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.venue.create'))->assertStatus(200);

        $venue = Venue::first();
        $this->assertNotNull($venue);

        $this->actingAs($this->adminUser)->get(route('admin.venue.show', $venue))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.venue.lapangan.index', $venue))->assertRedirect(route('admin.venue.show', $venue));
    }

    public function test_kontingen_and_panitia_pages(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.kontingen.index'))->assertStatus(200);
        $kontingen = Kontingen::first();
        $this->actingAs($this->adminUser)->get(route('admin.kontingen.show', $kontingen))->assertStatus(200);

        $this->actingAs($this->adminUser)->get(route('admin.panitia.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.panitia.create'))->assertStatus(200);

        // Test store panitia with user account
        $event = Event::first();
        $response = $this->actingAs($this->adminUser)->post(route('admin.panitia.store'), [
            'event_id' => $event->id,
            'nama' => 'Panitia Baru PJ',
            'jabatan' => 'Technical Delegate',
            'no_hp' => '081299998888',
            'email' => 'panitia.baru@sipora.id',
            'create_user' => '1',
            'user_email' => 'panitia.baru@sipora.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pj_cabor',
        ]);
        $response->assertRedirect();

        $panitia = Panitia::where('nama', 'Panitia Baru PJ')->first();
        $this->assertNotNull($panitia);
        $this->assertNotNull($panitia->user_id);
        $this->assertDatabaseHas('users', ['email' => 'panitia.baru@sipora.id', 'role' => 'pj_cabor']);

        // Test reset password akun
        $this->actingAs($this->adminUser)->post(route('admin.panitia.reset-password', $panitia), [
            'email' => 'panitia.baru@sipora.id',
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
            'role' => 'pj_cabor',
            'status' => 'aktif',
        ])->assertRedirect();
    }

    public function test_drawing_and_bracket_flow(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.drawing.index'))->assertStatus(200);

        $nomorLomba = NomorLomba::where('sistem_pertandingan', 'gugur_tunggal')->first() ?? NomorLomba::first();
        $this->assertNotNull($nomorLomba);

        $this->actingAs($this->adminUser)->get(route('admin.bracket.show', $nomorLomba))->assertStatus(200);
    }

    public function test_papan_jadwal_and_rundown_views(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.papan-jadwal.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.rundown.index'))->assertStatus(200);
    }

    public function test_hasil_and_klasemen_views(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.hasil.index'))->assertStatus(200);

        $pertandingan = Pertandingan::first();
        if ($pertandingan) {
            $this->actingAs($this->adminUser)->get(route('admin.hasil.show', $pertandingan))->assertStatus(200);
        }

        $this->actingAs($this->adminUser)->get(route('admin.klasemen.index'))->assertStatus(200);
    }

    public function test_pengumuman_crud(): void
    {
        $event = Event::first();
        $this->actingAs($this->adminUser)->get(route('admin.pengumuman.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.pengumuman.create'))->assertStatus(200);

        $response = $this->actingAs($this->adminUser)->post(route('admin.pengumuman.store'), [
            'event_id' => $event->id,
            'judul' => 'Pengumuman Uji Integrasi',
            'isi' => 'Konten pengumuman uji integrasi',
            'target' => 'semua',
        ]);
        $response->assertRedirect(route('admin.pengumuman.index'));

        $pengumuman = Pengumuman::where('judul', 'Pengumuman Uji Integrasi')->first();
        $this->assertNotNull($pengumuman);

        $this->actingAs($this->adminUser)->get(route('admin.pengumuman.edit', $pengumuman))->assertStatus(200);

        $this->actingAs($this->adminUser)->delete(route('admin.pengumuman.destroy', $pengumuman))
            ->assertRedirect(route('admin.pengumuman.index'));

        $this->assertSoftDeleted('pengumuman', ['id' => $pengumuman->id]);
    }

    public function test_laporan_and_audit_log_views(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.laporan.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get('/admin/audit-log')->assertStatus(200);
    }

    public function test_notifikasi_flow(): void
    {
        Notifikasi::create([
            'user_id' => $this->adminUser->id,
            'tipe' => 'informasi',
            'judul' => 'Tes Notifikasi',
            'pesan' => 'Pesan uji notifikasi',
        ]);

        $this->actingAs($this->adminUser)->get(route('notifikasi.index'))->assertStatus(200);
        $this->actingAs($this->adminUser)->post(route('notifikasi.baca-semua'))->assertRedirect();

        $unreadCount = Notifikasi::where('user_id', $this->adminUser->id)->whereNull('dibaca_pada')->count();
        $this->assertEquals(0, $unreadCount);
    }
}
