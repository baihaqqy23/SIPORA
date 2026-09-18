<?php

namespace Database\Seeders;

use App\Models\AcaraRundown;
use App\Models\Atlet;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\HasilPertandingan;
use App\Models\Kontingen;
use App\Models\Lapangan;
use App\Models\Medali;
use App\Models\NomorLomba;
use App\Models\Notifikasi;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\PenugasanPanitia;
use App\Models\Pertandingan;
use App\Models\PesertaPertandingan;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Admin User
        $admin = User::create([
            'name' => 'Administrator Sistem',
            'email' => 'admin@sipornas.id',
            'password' => $password,
            'role' => 'admin',
            'status' => 'aktif',
            'harus_ganti_password' => false,
        ]);

        // 2. Event
        $event = Event::create([
            'nama' => 'PEKAN OLAHRAGA PROVINSI (PORPROV) X 2026',
            'slug' => 'porprov-x-2026',
            'deskripsi' => 'Pesta olahraga akbar antar kota/kabupaten se-provinsi mempertandingkan cabang olahraga resmi berstandar nasional.',
            'tanggal_mulai' => Carbon::today()->subDays(2)->format('Y-m-d'),
            'tanggal_selesai' => Carbon::today()->addDays(8)->format('Y-m-d'),
            'kategori_usia' => 'senior',
            'pendaftaran_mulai' => Carbon::today()->subDays(30)->setTime(0, 0),
            'pendaftaran_selesai' => Carbon::today()->subDays(5)->setTime(23, 59),
            'maks_nomor_lomba_per_atlet' => 3,
            'status' => 'berlangsung',
        ]);

        // 3. Venues & Lapangan
        $venue1 = Venue::create([
            'event_id' => $event->id,
            'nama' => 'GOR Bulutangkis Gelora Megah',
            'alamat' => 'Jl. Pemuda Olahraga No. 12, Kompleks Olahraga Utama',
            'kapasitas' => 2500,
            'jam_operasional_mulai' => '08:00:00',
            'jam_operasional_selesai' => '22:00:00',
        ]);

        $lap1_1 = Lapangan::create(['venue_id' => $venue1->id, 'nama' => 'Court 1 (Utama)']);
        $lap1_2 = Lapangan::create(['venue_id' => $venue1->id, 'nama' => 'Court 2']);
        $lap1_3 = Lapangan::create(['venue_id' => $venue1->id, 'nama' => 'Court 3']);

        $venue2 = Venue::create([
            'event_id' => $event->id,
            'nama' => 'Padepokan Pencak Silat Satria',
            'alamat' => 'Jl. Veteran Olahraga No. 45',
            'kapasitas' => 1500,
            'jam_operasional_mulai' => '08:00:00',
            'jam_operasional_selesai' => '21:00:00',
        ]);

        $lap2_1 = Lapangan::create(['venue_id' => $venue2->id, 'nama' => 'Gelanggang Matras A']);
        $lap2_2 = Lapangan::create(['venue_id' => $venue2->id, 'nama' => 'Gelanggang Matras B']);

        $venue3 = Venue::create([
            'event_id' => $event->id,
            'nama' => 'Stadion Atletik Graha Pratama',
            'alamat' => 'Jl. Stadion Lingkar Barat Kav. 1-3',
            'kapasitas' => 10000,
            'jam_operasional_mulai' => '06:30:00',
            'jam_operasional_selesai' => '20:00:00',
        ]);

        $lap3_1 = Lapangan::create(['venue_id' => $venue3->id, 'nama' => 'Lintasan 100M-400M']);

        // 4. Cabang Olahraga
        $caborBdm = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Bulu Tangkis',
            'singkatan' => 'BDM',
            'warna' => '#2563EB',
            'deskripsi' => 'Pertandingan Bulu Tangkis Perorangan dan Ganda',
            'durasi_default_menit' => 45,
            'jeda_antar_tanding_menit' => 15,
        ]);
        $caborBdm->venues()->attach($venue1->id);

        $caborSilat = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Pencak Silat',
            'singkatan' => 'PSL',
            'warna' => '#16A34A',
            'deskripsi' => 'Pertandingan Silat Tanding dan Seni',
            'durasi_default_menit' => 30,
            'jeda_antar_tanding_menit' => 10,
        ]);
        $caborSilat->venues()->attach($venue2->id);

        $caborAtl = CabangOlahraga::create([
            'event_id' => $event->id,
            'nama' => 'Atletik',
            'singkatan' => 'ATL',
            'warna' => '#DC2626',
            'deskripsi' => 'Nomor Lari Cepat dan Lintasan',
            'durasi_default_menit' => 20,
            'jeda_antar_tanding_menit' => 10,
        ]);
        $caborAtl->venues()->attach($venue3->id);

        // 5. Panitia & PJ Cabor Users
        $panitiaBdm = Panitia::create([
            'event_id' => $event->id,
            'nama' => 'Drs. Hendra Setiawan, M.Pd',
            'jabatan' => 'Technical Delegate Bulu Tangkis',
            'instansi' => 'Pengprov PBSI',
            'no_hp' => '081234567891',
            'email' => 'pjcabor.bdm@sipornas.id',
        ]);

        $userPjBdm = User::create([
            'name' => 'PJ Bulu Tangkis',
            'email' => 'pjcabor.bdm@sipornas.id',
            'password' => $password,
            'role' => 'pj_cabor',
            'panitia_id' => $panitiaBdm->id,
            'status' => 'aktif',
            'harus_ganti_password' => false,
        ]);
        $panitiaBdm->update(['user_id' => $userPjBdm->id]);

        PenugasanPanitia::create([
            'panitia_id' => $panitiaBdm->id,
            'cabang_olahraga_id' => $caborBdm->id,
            'peran' => 'pj_cabor',
        ]);

        $panitiaSilat = Panitia::create([
            'event_id' => $event->id,
            'nama' => 'Bambang Sudibyo, S.Or',
            'jabatan' => 'Technical Delegate Pencak Silat',
            'instansi' => 'Pengprov IPSI',
            'no_hp' => '081234567892',
            'email' => 'pjcabor.psl@sipornas.id',
        ]);

        $userPjSilat = User::create([
            'name' => 'PJ Pencak Silat',
            'email' => 'pjcabor.psl@sipornas.id',
            'password' => $password,
            'role' => 'pj_cabor',
            'panitia_id' => $panitiaSilat->id,
            'status' => 'aktif',
            'harus_ganti_password' => false,
        ]);
        $panitiaSilat->update(['user_id' => $userPjSilat->id]);

        PenugasanPanitia::create([
            'panitia_id' => $panitiaSilat->id,
            'cabang_olahraga_id' => $caborSilat->id,
            'peran' => 'pj_cabor',
        ]);

        // 6. Nomor Lomba
        $nomorBdmMs = NomorLomba::create([
            'cabang_olahraga_id' => $caborBdm->id,
            'nama' => 'Tunggal Putra (MS)',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'jumlah_anggota' => 1,
            'format_pertandingan' => 'gugur_tunggal',
            'kuota_per_kontingen' => 2,
            'kapasitas_total' => 16,
            'jumlah_perunggu' => 2,
            'status_bracket' => 'terkunci',
        ]);

        $nomorBdmWs = NomorLomba::create([
            'cabang_olahraga_id' => $caborBdm->id,
            'nama' => 'Tunggal Putri (WS)',
            'gender' => 'putri',
            'jenis' => 'perorangan',
            'jumlah_anggota' => 1,
            'format_pertandingan' => 'gugur_tunggal',
            'kuota_per_kontingen' => 2,
            'kapasitas_total' => 16,
            'jumlah_perunggu' => 2,
            'status_bracket' => 'terkunci',
        ]);

        $nomorSilatA = NomorLomba::create([
            'cabang_olahraga_id' => $caborSilat->id,
            'nama' => 'Tanding Kelas A Putra (45-50 kg)',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'jumlah_anggota' => 1,
            'format_pertandingan' => 'gugur_tunggal',
            'kuota_per_kontingen' => 1,
            'kapasitas_total' => 8,
            'jumlah_perunggu' => 2,
            'status_bracket' => 'terkunci',
        ]);

        $nomorAtl100m = NomorLomba::create([
            'cabang_olahraga_id' => $caborAtl->id,
            'nama' => 'Lari 100 Meter Putra',
            'gender' => 'putra',
            'jenis' => 'perorangan',
            'jumlah_anggota' => 1,
            'format_pertandingan' => 'heat',
            'kuota_per_kontingen' => 2,
            'kapasitas_total' => 24,
            'jumlah_perunggu' => 1,
            'status_bracket' => 'terkunci',
        ]);

        // 7. Kontingen & Akun Kontingen
        $kontingenData = [
            ['nama' => 'Kontingen Kota Bandung', 'slug' => 'kota-bandung', 'prov' => 'Jawa Barat', 'kota' => 'Kota Bandung', 'email' => 'bandung@kontingen.id'],
            ['nama' => 'Kontingen Kota Surabaya', 'slug' => 'kota-surabaya', 'prov' => 'Jawa Timur', 'kota' => 'Kota Surabaya', 'email' => 'surabaya@kontingen.id'],
            ['nama' => 'Kontingen Kota Semarang', 'slug' => 'kota-semarang', 'prov' => 'Jawa Tengah', 'kota' => 'Kota Semarang', 'email' => 'semarang@kontingen.id'],
            ['nama' => 'Kontingen Kota Medan', 'slug' => 'kota-medan', 'prov' => 'Sumatera Utara', 'kota' => 'Kota Medan', 'email' => 'medan@kontingen.id'],
        ];

        $kontingenModels = [];
        foreach ($kontingenData as $kd) {
            $kontingen = Kontingen::create([
                'event_id' => $event->id,
                'nama' => $kd['nama'],
                'slug' => $kd['slug'],
                'provinsi' => $kd['prov'],
                'kota' => $kd['kota'],
                'nama_ofisial' => 'Ofisial '.$kd['kota'],
                'no_hp_ofisial' => '0812987654'.rand(10, 99),
                'email' => $kd['email'],
                'status' => 'disetujui',
            ]);

            $userK = User::create([
                'name' => $kd['nama'],
                'email' => $kd['email'],
                'password' => $password,
                'role' => 'kontingen',
                'kontingen_id' => $kontingen->id,
                'status' => 'aktif',
                'harus_ganti_password' => false,
            ]);

            $kontingenModels[] = $kontingen;
        }

        // 8. Atlet & Pendaftaran
        $pendaftaranBdmMs = [];
        $pendaftaranBdmWs = [];
        $pendaftaranSilatA = [];
        $pendaftaranAtl100 = [];

        foreach ($kontingenModels as $kIndex => $kontingen) {
            // Male Athlete 1 (Bulu Tangkis MS)
            $atletBdmM = Atlet::create([
                'kontingen_id' => $kontingen->id,
                'nama' => fake('id_ID')->firstNameMale().' '.fake('id_ID')->lastName(),
                'nik' => '320'.rand(1000000000000, 9999999999999),
                'tanggal_lahir' => '2002-04-15',
                'gender' => 'L',
                'asal_kota' => $kontingen->kota,
                'status' => 'aktif',
            ]);
            $pBdmM = Pendaftaran::create([
                'event_id' => $event->id,
                'kontingen_id' => $kontingen->id,
                'nomor_lomba_id' => $nomorBdmMs->id,
                'atlet_id' => $atletBdmM->id,
                'status' => 'disetujui',
                'disetujui_oleh' => $admin->id,
                'disetujui_pada' => now(),
            ]);
            $pendaftaranBdmMs[] = $pBdmM;

            // Female Athlete 1 (Bulu Tangkis WS)
            $atletBdmF = Atlet::create([
                'kontingen_id' => $kontingen->id,
                'nama' => fake('id_ID')->firstNameFemale().' '.fake('id_ID')->lastName(),
                'nik' => '320'.rand(1000000000000, 9999999999999),
                'tanggal_lahir' => '2003-09-20',
                'gender' => 'P',
                'asal_kota' => $kontingen->kota,
                'status' => 'aktif',
            ]);
            $pBdmF = Pendaftaran::create([
                'event_id' => $event->id,
                'kontingen_id' => $kontingen->id,
                'nomor_lomba_id' => $nomorBdmWs->id,
                'atlet_id' => $atletBdmF->id,
                'status' => 'disetujui',
                'disetujui_oleh' => $admin->id,
                'disetujui_pada' => now(),
            ]);
            $pendaftaranBdmWs[] = $pBdmF;

            // Male Athlete 2 (Silat A)
            $atletSilat = Atlet::create([
                'kontingen_id' => $kontingen->id,
                'nama' => fake('id_ID')->firstNameMale().' '.fake('id_ID')->lastName(),
                'nik' => '320'.rand(1000000000000, 9999999999999),
                'tanggal_lahir' => '2001-11-10',
                'gender' => 'L',
                'asal_kota' => $kontingen->kota,
                'status' => 'aktif',
            ]);
            $pSilat = Pendaftaran::create([
                'event_id' => $event->id,
                'kontingen_id' => $kontingen->id,
                'nomor_lomba_id' => $nomorSilatA->id,
                'atlet_id' => $atletSilat->id,
                'status' => 'disetujui',
                'disetujui_oleh' => $admin->id,
                'disetujui_pada' => now(),
            ]);
            $pendaftaranSilatA[] = $pSilat;

            // Male Athlete 3 (Atletik 100m)
            $atletAtl = Atlet::create([
                'kontingen_id' => $kontingen->id,
                'nama' => fake('id_ID')->firstNameMale().' '.fake('id_ID')->lastName(),
                'nik' => '320'.rand(1000000000000, 9999999999999),
                'tanggal_lahir' => '2000-02-05',
                'gender' => 'L',
                'asal_kota' => $kontingen->kota,
                'status' => 'aktif',
            ]);
            $pAtl = Pendaftaran::create([
                'event_id' => $event->id,
                'kontingen_id' => $kontingen->id,
                'nomor_lomba_id' => $nomorAtl100m->id,
                'atlet_id' => $atletAtl->id,
                'status' => 'disetujui',
                'disetujui_oleh' => $admin->id,
                'disetujui_pada' => now(),
            ]);
            $pendaftaranAtl100[] = $pAtl;
        }

        // 9. Pertandingan Bulutangkis (Semifinal & Final)
        if (count($pendaftaranBdmMs) >= 4) {
            // Match 1: Semifinal 1 - Selesai
            $m1 = Pertandingan::create([
                'nomor_lomba_id' => $nomorBdmMs->id,
                'lapangan_id' => $lap1_1->id,
                'babak' => 'Semifinal',
                'urutan_bracket' => 1,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'durasi_menit' => 45,
                'status' => 'selesai',
            ]);

            $m1_pA = PesertaPertandingan::create([
                'pertandingan_id' => $m1->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[0]->atlet_id,
                'slot' => 1,
                'skor' => 2,
                'hasil' => 'menang',
            ]);

            $m1_pB = PesertaPertandingan::create([
                'pertandingan_id' => $m1->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[1]->atlet_id,
                'slot' => 2,
                'skor' => 1,
                'hasil' => 'kalah',
            ]);

            HasilPertandingan::create([
                'pertandingan_id' => $m1->id,
                'pemenang_peserta_id' => $m1_pA->id,
                'keterangan' => '21-18, 19-21, 21-15 (Menang Rubber Game)',
                'diinput_oleh' => $admin->id,
                'diinput_pada' => now(),
            ]);

            // Match 2: Semifinal 2 - Selesai
            $m2 = Pertandingan::create([
                'nomor_lomba_id' => $nomorBdmMs->id,
                'lapangan_id' => $lap1_2->id,
                'babak' => 'Semifinal',
                'urutan_bracket' => 2,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'waktu_mulai' => '10:00:00',
                'durasi_menit' => 45,
                'status' => 'selesai',
            ]);

            $m2_pA = PesertaPertandingan::create([
                'pertandingan_id' => $m2->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[2]->atlet_id,
                'slot' => 1,
                'skor' => 2,
                'hasil' => 'menang',
            ]);

            $m2_pB = PesertaPertandingan::create([
                'pertandingan_id' => $m2->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[3]->atlet_id,
                'slot' => 2,
                'skor' => 0,
                'hasil' => 'kalah',
            ]);

            HasilPertandingan::create([
                'pertandingan_id' => $m2->id,
                'pemenang_peserta_id' => $m2_pA->id,
                'keterangan' => '21-14, 21-12 (Straight Set)',
                'diinput_oleh' => $admin->id,
                'diinput_pada' => now(),
            ]);

            // Match 3: Final - Berlangsung
            $m3 = Pertandingan::create([
                'nomor_lomba_id' => $nomorBdmMs->id,
                'lapangan_id' => $lap1_1->id,
                'babak' => 'Final',
                'urutan_bracket' => 3,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'waktu_mulai' => '15:30:00',
                'durasi_menit' => 60,
                'status' => 'berlangsung',
            ]);

            PesertaPertandingan::create([
                'pertandingan_id' => $m3->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[0]->atlet_id,
                'slot' => 1,
                'skor' => 1,
            ]);

            PesertaPertandingan::create([
                'pertandingan_id' => $m3->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[2]->atlet_id,
                'slot' => 2,
                'skor' => 0,
            ]);
        }

        // 10. Medali Klasemen
        if (count($kontingenModels) >= 4) {
            // Bandung: 2 Emas, 1 Perak
            Medali::create([
                'nomor_lomba_id' => $nomorSilatA->id,
                'kontingen_id' => $kontingenModels[0]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranSilatA[0]->atlet_id,
                'jenis' => 'emas',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorAtl100m->id,
                'kontingen_id' => $kontingenModels[0]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranAtl100[0]->atlet_id,
                'jenis' => 'emas',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorBdmWs->id,
                'kontingen_id' => $kontingenModels[0]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmWs[0]->atlet_id,
                'jenis' => 'perak',
            ]);

            // Surabaya: 1 Emas, 2 Perak, 1 Perunggu
            Medali::create([
                'nomor_lomba_id' => $nomorBdmWs->id,
                'kontingen_id' => $kontingenModels[1]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmWs[1]->atlet_id,
                'jenis' => 'emas',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorSilatA->id,
                'kontingen_id' => $kontingenModels[1]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranSilatA[1]->atlet_id,
                'jenis' => 'perak',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorAtl100m->id,
                'kontingen_id' => $kontingenModels[1]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranAtl100[1]->atlet_id,
                'jenis' => 'perak',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorBdmMs->id,
                'kontingen_id' => $kontingenModels[1]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[1]->atlet_id,
                'jenis' => 'perunggu',
            ]);

            // Semarang: 1 Emas, 1 Perunggu
            Medali::create([
                'nomor_lomba_id' => $nomorSilatA->id,
                'kontingen_id' => $kontingenModels[2]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranSilatA[2]->atlet_id,
                'jenis' => 'perunggu',
            ]);
            Medali::create([
                'nomor_lomba_id' => $nomorBdmMs->id,
                'kontingen_id' => $kontingenModels[2]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranBdmMs[2]->atlet_id,
                'jenis' => 'perunggu',
            ]);

            // Medan: 1 Perunggu
            Medali::create([
                'nomor_lomba_id' => $nomorAtl100m->id,
                'kontingen_id' => $kontingenModels[3]->id,
                'peserta_type' => 'App\Models\Atlet',
                'peserta_id' => $pendaftaranAtl100[3]->atlet_id,
                'jenis' => 'perunggu',
            ]);
        }

        // 11. Rundown Acara
        AcaraRundown::create([
            'event_id' => $event->id,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'waktu_mulai' => '07:30:00',
            'waktu_selesai' => '08:30:00',
            'judul' => 'Briefing Ofisial dan Wasit Pertandingan',
            'lokasi' => 'Ruang Rapat VIP Gelora Sriwijaya',
            'penanggung_jawab' => 'Panpel Pusat',
            'catatan' => 'Wajib dihadiri seluruh Technical Delegate',
            'dipublikasikan' => true,
        ]);

        AcaraRundown::create([
            'event_id' => $event->id,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '17:00:00',
            'judul' => 'Penyisihan & Semifinal Cabor Bulu Tangkis & Silat',
            'lokasi' => 'GOR Bulutangkis & Padepokan Silat',
            'penanggung_jawab' => 'Koordinator Pertandingan',
            'catatan' => 'Sesuai jadwal pertandingan masing-masing gelanggang',
            'dipublikasikan' => true,
        ]);

        // 12. Pengumuman
        Pengumuman::create([
            'event_id' => $event->id,
            'judul' => 'Jadwal Upacara Pembukaan dan Defile Kontingen PORPROV X 2026',
            'isi' => '<p>Upacara pembukaan akan dilangsungkan di Stadion Utama pada pukul 19.00 WIB. Seluruh defile kontingen diharapkan telah bersiap di holding area sejak pukul 17.30 WIB dengan mengenakan seragam resmi kontingen.</p>',
            'target' => 'semua',
            'tayang_mulai' => Carbon::now()->subDays(1),
        ]);

        Pengumuman::create([
            'event_id' => $event->id,
            'judul' => 'Petunjuk Teknis Pengambilan ID Card dan Akreditasi Ofisial',
            'isi' => '<p>Pengambilan ID Card atlet dan ofisial dapat dilakukan di Sekretariat Utama Panpel mulai pukul 08.00 - 17.00 WIB dengan membawa surat mandat asli.</p>',
            'target' => 'kontingen',
            'tayang_mulai' => Carbon::now()->subDays(2),
        ]);

        // 13. Notifikasi
        Notifikasi::create([
            'user_id' => $admin->id,
            'tipe' => 'info',
            'judul' => 'Sistem PORPROV X 2026 Siap',
            'pesan' => 'Database telah diinisialisasi dengan data simulasi pertandingan dan kontingen.',
            'url_tujuan' => '/admin/dashboard',
            'dibaca_pada' => null,
        ]);
    }
}
