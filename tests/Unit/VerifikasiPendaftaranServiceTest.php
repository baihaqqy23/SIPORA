<?php

namespace Tests\Unit;

use App\Models\Atlet;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\NomorLomba;
use App\Services\VerifikasiPendaftaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifikasiPendaftaranServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VerifikasiPendaftaranService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VerifikasiPendaftaranService;
    }

    public function test_validasi_gender_tidak_cocok_ditolak(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-2026',
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
            'singkatan' => 'SILAT',
            'warna' => '#C8102E',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Tanding Kelas A Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'umur_min' => 14,
            'umur_maks' => 18,
            'kuota_per_kontingen' => 2,
        ]);

        $kontingen = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Kontingen DKI Jakarta',
            'slug' => 'dki-jakarta',
            'provinsi' => 'DKI Jakarta',
            'kota' => 'Jakarta Pusat',
            'nama_ofisial' => 'Budi Santoso',
            'no_hp_ofisial' => '08123456789',
            'email' => 'dki@example.com',
            'status' => 'disetujui',
        ]);

        // Atlet Putri mendaftar di nomor Putra
        $atletPutri = Atlet::create([
            'kontingen_id' => $kontingen->id,
            'nama' => 'Siti Rahma',
            'nik' => '3171012345678901',
            'tanggal_lahir' => now()->subYears(16),
            'gender' => 'P',
            'asal_kota' => 'Jakarta Timur',
        ]);

        $hasil = $this->service->validasiOtomatis($event, $nomorLomba, $kontingen->id, $atletPutri);

        $this->assertFalse($hasil['lolos']);
        $this->assertFalse($hasil['detail']['kesesuaian_gender']);
        $this->assertStringContainsString('Gender atlet (P) tidak cocok', $hasil['alasan']);
    }

    public function test_validasi_umur_melebihi_batas_maksimum_ditolak(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-2026-umur',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'tanggal_patokan_umur' => now()->addDays(10),
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Atletik',
            'singkatan' => 'ATK',
            'warna' => '#0F172A',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => 'Lari 100m Putra U-18',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'umur_min' => 14,
            'umur_maks' => 18,
            'kuota_per_kontingen' => 2,
        ]);

        $kontingen = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Kontingen Jawa Barat',
            'slug' => 'jawa-barat',
            'provinsi' => 'Jawa Barat',
            'kota' => 'Bandung',
            'nama_ofisial' => 'Asep Sunandar',
            'no_hp_ofisial' => '08123456788',
            'email' => 'jabar@example.com',
            'status' => 'disetujui',
        ]);

        // Atlet umur 20 tahun
        $atletDewasa = Atlet::create([
            'kontingen_id' => $kontingen->id,
            'nama' => 'Doni Ramadhan',
            'nik' => '3273012345678901',
            'tanggal_lahir' => now()->subYears(20),
            'gender' => 'L',
            'asal_kota' => 'Kota Bandung',
        ]);

        $hasil = $this->service->validasiOtomatis($event, $nomorLomba, $kontingen->id, $atletDewasa);

        $this->assertFalse($hasil['lolos']);
        $this->assertFalse($hasil['detail']['kesesuaian_umur']);
        $this->assertStringContainsString('melebihi batas maksimum', $hasil['alasan']);
    }

    public function test_validasi_lolos_bila_semua_syarat_terpenuhi(): void
    {
        $event = Event::create([
            'nama' => 'POPNAS 2026',
            'slug' => 'popnas-2026-lolos',
            'tanggal_mulai' => now()->addDays(10),
            'tanggal_selesai' => now()->addDays(20),
            'tanggal_patokan_umur' => now()->addDays(10),
            'pendaftaran_mulai' => now()->subDays(5),
            'pendaftaran_selesai' => now()->addDays(5),
            'status' => 'pendaftaran_dibuka',
        ]);

        $cabor = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Renang',
            'singkatan' => 'RNG',
            'warna' => '#0F172A',
        ]);

        $nomorLomba = NomorLomba::create([
            'cabang_olahraga_id' => $cabor->id,
            'nama' => '50m Gaya Bebas Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'umur_min' => 14,
            'umur_maks' => 18,
            'kuota_per_kontingen' => 2,
        ]);

        $kontingen = Kontingen::create([
            'event_id' => $event->id,
            'nama' => 'Kontingen Jawa Timur',
            'slug' => 'jawa-timur',
            'provinsi' => 'Jawa Timur',
            'kota' => 'Surabaya',
            'nama_ofisial' => 'Bambang Sudibyo',
            'no_hp_ofisial' => '08123456787',
            'email' => 'jatim@example.com',
            'status' => 'disetujui',
        ]);

        // Atlet umur 16 tahun, gender L
        $atletSah = Atlet::create([
            'kontingen_id' => $kontingen->id,
            'nama' => 'Ahmad Fajar',
            'nik' => '3578012345678901',
            'tanggal_lahir' => now()->subYears(16),
            'gender' => 'L',
            'asal_kota' => 'Surabaya',
        ]);

        $hasil = $this->service->validasiOtomatis($event, $nomorLomba, $kontingen->id, $atletSah);

        $this->assertTrue($hasil['lolos']);
        $this->assertNull($hasil['alasan']);
    }
}
