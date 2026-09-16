<?php

namespace Tests\Unit;

use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\Medali;
use App\Models\NomorLomba;
use App\Services\KlasemenMedaliService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlasemenMedaliServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KlasemenMedaliService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KlasemenMedaliService;
    }

    public function test_klasemen_terurut_emas_perak_perunggu(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-klasemen',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'tanggal_patokan_umur' => now()->addDays(10),
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'berlangsung',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Pencak Silat',
            'singkatan' => 'PSL',
            'warna' => '#C8102E',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Kelas A Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
        ]);

        // Buat 3 kontingen
        $dki = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'DKI Jakarta',
            'slug' => 'dki-jakarta',
            'provinsi' => 'DKI Jakarta',
            'kota' => 'Jakarta Pusat',
            'nama_ofisial' => 'Ofisial 1',
            'no_hp_ofisial' => '081',
            'email' => 'dki@test.com',
            'status' => 'disetujui',
        ]);

        $jabar = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Jawa Barat',
            'slug' => 'jawa-barat',
            'provinsi' => 'Jawa Barat',
            'kota' => 'Bandung',
            'nama_ofisial' => 'Ofisial 2',
            'no_hp_ofisial' => '082',
            'email' => 'jabar@test.com',
            'status' => 'disetujui',
        ]);

        $jatim = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Jawa Timur',
            'slug' => 'jawa-timur',
            'provinsi' => 'Jawa Timur',
            'kota' => 'Surabaya',
            'nama_ofisial' => 'Ofisial 3',
            'no_hp_ofisial' => '083',
            'email' => 'jatim@test.com',
            'status' => 'disetujui',
        ]);

        // Jabar: 2 Emas, 0 Perak
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $jabar->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 1, 'jenis' => 'emas']);
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $jabar->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 2, 'jenis' => 'emas']);

        // DKI: 1 Emas, 3 Perak, 1 Perunggu
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $dki->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 3, 'jenis' => 'emas']);
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $dki->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 4, 'jenis' => 'perak']);
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $dki->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 5, 'jenis' => 'perak']);
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $dki->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 6, 'jenis' => 'perak']);

        // Jatim: 1 Emas, 1 Perak
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $jatim->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 7, 'jenis' => 'emas']);
        Medali::create(['nomor_lomba_id' => $nomorLomba->id, 'kontingen_id' => $jatim->id, 'peserta_type' => 'App\\Models\\Atlet', 'peserta_id' => 8, 'jenis' => 'perak']);

        $klasemen = $this->service->getKlasemen($event->id, null, false);

        // Peringkat 1: Jabar (2 emas)
        $this->assertEquals('Jawa Barat', $klasemen[0]['kontingen_nama']);
        $this->assertEquals(1, $klasemen[0]['peringkat']);
        $this->assertEquals(2, $klasemen[0]['emas']);

        // Peringkat 2: DKI (1 emas, 3 perak)
        $this->assertEquals('DKI Jakarta', $klasemen[1]['kontingen_nama']);
        $this->assertEquals(2, $klasemen[1]['peringkat']);
        $this->assertEquals(1, $klasemen[1]['emas']);
        $this->assertEquals(3, $klasemen[1]['perak']);

        // Peringkat 3: Jatim (1 emas, 1 perak)
        $this->assertEquals('Jawa Timur', $klasemen[2]['kontingen_nama']);
        $this->assertEquals(3, $klasemen[2]['peringkat']);
    }
}
