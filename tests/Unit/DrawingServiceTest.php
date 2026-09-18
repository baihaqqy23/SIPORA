<?php

namespace Tests\Unit;

use App\Models\Atlet;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\HasilPertandingan;
use App\Models\Kontingen;
use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use App\Models\User;
use App\Services\DrawingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrawingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DrawingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DrawingService;
    }

    public function test_drawing_gugur_tunggal_menghasilkan_bracket_dan_bye_otomatis(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-drawing',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'kategori_usia' => 'senior',
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Bulu Tangkis',
            'singkatan' => 'BDM',
            'warna' => '#C8102E',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Tunggal Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'format_pertandingan' => 'gugur_tunggal',
        ]);

        $kontingenA = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Kontingen A',
            'slug' => 'kontingen-a',
            'provinsi' => 'Provinsi A',
            'kota' => 'Kota A',
            'nama_ofisial' => 'Ofisial A',
            'no_hp_ofisial' => '0811111111',
            'email' => 'a@example.com',
            'status' => 'disetujui',
        ]);

        $kontingenB = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Kontingen B',
            'slug' => 'kontingen-b',
            'provinsi' => 'Provinsi B',
            'kota' => 'Kota B',
            'nama_ofisial' => 'Ofisial B',
            'no_hp_ofisial' => '0822222222',
            'email' => 'b@example.com',
            'status' => 'disetujui',
        ]);

        // Daftarkan 5 atlet berstatus disetujui (non-power of 2)
        for ($i = 1; $i <= 5; $i++) {
            $k = ($i % 2 === 0) ? $kontingenA : $kontingenB;
            $atlet = Atlet::create([
                'kontingen_id' => $k->id,
                'nama' => "Atlet Bulutangkis {$i}",
                'nik' => "123456789012340{$i}",
                'tanggal_lahir' => now()->subYears(16),
                'gender' => 'L',
                'asal_kota' => $k->kota,
            ]);

            Pendaftaran::create([
                'event_id' => $event->id,
                'kontingen_id' => $k->id,
                'nomor_lomba_id' => $nomorLomba->id,
                'atlet_id' => $atlet->id,
                'status' => 'disetujui',
            ]);
        }

        $preview = $this->service->previewDrawing($nomorLomba);

        $this->assertEquals('gugur_tunggal', $preview['format']);
        // 5 peserta -> bracket size kelipatan pangkat 2 terdekat adalah 8
        $this->assertEquals(8, $preview['bracket_size']);
        $this->assertCount(3, $preview['rounds']); // Perempat Final, Semifinal, Final

        // Simpan bracket ke database
        $this->service->simpanDrawing($nomorLomba, $preview);

        $this->assertEquals('tergenerate', $nomorLomba->fresh()->status_bracket);
        $totalPertandingan = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)->count();
        // 8-slot single elimination has 4 + 2 + 1 = 7 matches
        $this->assertEquals(7, $totalPertandingan);
    }

    public function test_cegah_reset_bracket_jika_ada_hasil(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sudah ada hasil pertandingan yang tersimpan');

        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-reset-test',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'kategori_usia' => 'senior',
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Pencak Silat',
            'singkatan' => 'PSL',
            'warna' => '#C8102E',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Kelas B Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'format_pertandingan' => 'gugur_tunggal',
            'status_bracket' => 'tergenerate',
        ]);

        $pertandingan = Pertandingan::create([
            'nomor_lomba_id' => $nomorLomba->id,
            'babak' => 'Final',
            'status' => 'selesai',
        ]);

        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        HasilPertandingan::create([
            'pertandingan_id' => $pertandingan->id,
            'diinput_oleh' => $user->id,
            'diinput_pada' => now(),
            'keterangan' => 'Selesai',
        ]);

        // Coba reset saat sudah ada hasil
        $this->service->resetBracket($nomorLomba);
    }
}
