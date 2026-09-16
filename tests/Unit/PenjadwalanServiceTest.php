<?php

namespace Tests\Unit;

use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\NomorLomba;
use App\Models\Pertandingan;
use App\Models\Venue;
use App\Services\PenjadwalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjadwalanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PenjadwalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenjadwalanService;
    }

    public function test_deteksi_konflik_venue_dobel_pada_jam_dan_lapangan_sama(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-jdw-test',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'tanggal_patokan_umur' => now()->addDays(10),
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Pencak Silat',
            'singkatan' => 'PSL',
            'warna' => '#C8102E',
            'durasi_default_menit' => 60,
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Kelas C Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
        ]);

        $venue = Venue::create([
            'event_id' => $event->id,
            'nama' => 'GOR Bulungan',
            'jam_operasional_mulai' => '08:00:00',
            'jam_operasional_selesai' => '20:00:00',
        ]);

        $lapangan = Lapangan::create([
            'venue_id' => $venue->id,
            'nama' => 'Gelanggang 1',
        ]);

        $tanggal = now()->addDays(12)->format('Y-m-d');

        // Laga 1 jam 09:00 (durasi 60m)
        Pertandingan::create([
            'nomor_lomba_id' => $nomorLomba->id,
            'lapangan_id' => $lapangan->id,
            'babak' => 'Babak 1',
            'tanggal' => $tanggal,
            'waktu_mulai' => '09:00:00',
            'durasi_menit' => 60,
            'status' => 'terjadwal',
        ]);

        // Laga 2 jam 09:30 di lapangan yang sama (overlap!)
        $laga2 = Pertandingan::create([
            'nomor_lomba_id' => $nomorLomba->id,
            'lapangan_id' => $lapangan->id,
            'babak' => 'Babak 2',
            'tanggal' => $tanggal,
            'waktu_mulai' => '09:30:00',
            'durasi_menit' => 60,
            'status' => 'terjadwal',
        ]);

        $konflik = $this->service->deteksiKonflik($tanggal);

        $this->assertTrue($konflik['ada_konflik_keras']);
        $this->assertEquals('venue_dobel', $konflik['konflik_keras'][0]['tipe']);
        $this->assertStringContainsString('Bentrok Lapangan', $konflik['konflik_keras'][0]['pesan']);

        // Uji bahwa sistem menolak pindah slot yang menimbulkan konflik keras
        $pindahResult = $this->service->pindahkanSlot($laga2, $lapangan->id, $tanggal, '09:15:00', 60);
        $this->assertFalse($pindahResult['berhasil']);
        $this->assertStringContainsString('Pemindahan ditolak karena menimbulkan konflik keras', $pindahResult['pesan']);
    }
}
