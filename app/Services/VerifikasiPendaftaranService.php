<?php

namespace App\Services;

use App\Models\Atlet;
use App\Models\Event;
use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Models\RiwayatVerifikasi;
use App\Models\TimKontingen;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifikasiPendaftaranService
{
    /**
     * Validasi kelayakan pendaftaran secara otomatis
     *
     * @return array{
     *     lolos: bool,
     *     alasan: ?string,
     *     peringatan: ?string,
     *     detail: array<string, bool|string>
     * }
     */
    public function validasiOtomatis(
        Event $event,
        NomorLomba $nomorLomba,
        int $kontingenId,
        ?Atlet $atlet = null,
        ?TimKontingen $tim = null
    ): array {
        $detail = [];
        $alasanList = [];
        $peringatan = null;

        // 1. Cek periode pendaftaran (REG-11, EVT-04)
        $now = now();
        $periodeBuka = $now->between($event->pendaftaran_mulai, $event->pendaftaran_selesai);
        $detail['periode_pendaftaran'] = $periodeBuka;
        if (! $periodeBuka) {
            $alasanList[] = 'Pendaftaran ditutup atau di luar rentang jadwal pendaftaran event.';
        }

        // 2. Cek Kuota Kontingen (REG-03)
        if ($nomorLomba->kuota_per_kontingen) {
            $terdaftarKontingen = Pendaftaran::where('nomor_lomba_id', $nomorLomba->id)
                ->where('kontingen_id', $kontingenId)
                ->whereNotIn('status', ['ditolak', 'ditolak_sistem', 'dibatalkan'])
                ->count();

            $kuotaCukup = $terdaftarKontingen < $nomorLomba->kuota_per_kontingen;
            $detail['kuota_kontingen'] = $kuotaCukup;
            if (! $kuotaCukup) {
                $alasanList[] = "Kuota kontingen untuk nomor lomba ini telah penuh (Maks: {$nomorLomba->kuota_per_kontingen}).";
            }
        } else {
            $detail['kuota_kontingen'] = true;
        }

        // 3. Cek Kapasitas Total Nomor Lomba (REG-03)
        if ($nomorLomba->kapasitas_total) {
            $terdaftarTotal = Pendaftaran::where('nomor_lomba_id', $nomorLomba->id)
                ->whereNotIn('status', ['ditolak', 'ditolak_sistem', 'dibatalkan'])
                ->count();

            $kapasitasCukup = $terdaftarTotal < $nomorLomba->kapasitas_total;
            $detail['kapasitas_total'] = $kapasitasCukup;
            if (! $kapasitasCukup) {
                $alasanList[] = "Kapasitas total peserta nomor lomba ini telah penuh ({$nomorLomba->kapasitas_total}).";
            }
        } else {
            $detail['kapasitas_total'] = true;
        }

        // 4. Validasi Peserta Perorangan (Atlet)
        if ($nomorLomba->jenis === 'perorangan' && $atlet) {
            // REG-04: Cek duplikasi di nomor yang sama
            $sudahTerdaftar = Pendaftaran::where('nomor_lomba_id', $nomorLomba->id)
                ->where('atlet_id', $atlet->id)
                ->whereNotIn('status', ['ditolak', 'ditolak_sistem', 'dibatalkan'])
                ->exists();

            $detail['duplikasi_nomor'] = ! $sudahTerdaftar;
            if ($sudahTerdaftar) {
                $alasanList[] = "Atlet {$atlet->nama} sudah terdaftar di nomor lomba ini.";
            }

            // REG-01: Cek kesesuaian gender
            $genderCocok = false;
            if ($nomorLomba->gender === 'campuran') {
                $genderCocok = true;
            } elseif ($nomorLomba->gender === 'putra' && $atlet->gender === 'L') {
                $genderCocok = true;
            } elseif ($nomorLomba->gender === 'putri' && $atlet->gender === 'P') {
                $genderCocok = true;
            }
            $detail['kesesuaian_gender'] = $genderCocok;
            if (! $genderCocok) {
                $genderLabel = $nomorLomba->gender === 'putra' ? 'Putra (L)' : 'Putri (P)';
                $alasanList[] = "Gender atlet ({$atlet->gender}) tidak cocok dengan kategori nomor lomba ({$genderLabel}).";
            }

            // REG-02: Cek Umur terhadap event.tanggal_patokan_umur
            $umur = $atlet->hitungUmurPada($event->tanggal_patokan_umur);
            $umurValid = true;
            if ($nomorLomba->umur_min && $umur < $nomorLomba->umur_min) {
                $umurValid = false;
                $alasanList[] = "Umur atlet ({$umur} tahun) kurang dari batas minimum ({$nomorLomba->umur_min} tahun) pada tanggal patokan {$event->tanggal_patokan_umur->format('d/m/Y')}.";
            }
            if ($nomorLomba->umur_maks && $umur > $nomorLomba->umur_maks) {
                $umurValid = false;
                $alasanList[] = "Umur atlet ({$umur} tahun) melebihi batas maksimum ({$nomorLomba->umur_maks} tahun) pada tanggal patokan {$event->tanggal_patokan_umur->format('d/m/Y')}.";
            }
            $detail['kesesuaian_umur'] = $umurValid;

            // REG-05: Peringatan jika atlet ikut > N nomor lomba
            if ($event->maks_nomor_lomba_per_atlet) {
                $totalIkut = Pendaftaran::where('atlet_id', $atlet->id)
                    ->whereNotIn('status', ['ditolak', 'ditolak_sistem', 'dibatalkan'])
                    ->count();

                if ($totalIkut >= $event->maks_nomor_lomba_per_atlet) {
                    $peringatan = "Perhatian: Atlet {$atlet->nama} telah terdaftar di {$totalIkut} nomor lomba (Batas anjuran event: {$event->maks_nomor_lomba_per_atlet}).";
                }
            }
        }

        // 5. Validasi Peserta Beregu
        if ($nomorLomba->jenis === 'beregu' && $tim) {
            $anggotaCount = $tim->anggotaTim()->where('peran', 'inti')->count();
            $jumlahAnggotaWajib = $nomorLomba->jumlah_anggota ?? 0;

            $anggotaCukup = $anggotaCount >= $jumlahAnggotaWajib;
            $detail['jumlah_anggota_tim'] = $anggotaCukup;
            if (! $anggotaCukup) {
                $alasanList[] = "Jumlah anggota tim inti ({$anggotaCount}) belum memenuhi kuota minimum tim ({$jumlahAnggotaWajib}).";
            }
        }

        $lolos = count($alasanList) === 0;

        return [
            'lolos' => $lolos,
            'alasan' => $lolos ? null : implode(' | ', $alasanList),
            'peringatan' => $peringatan,
            'detail' => $detail,
        ];
    }

    /**
     * Transisi Status Verifikasi oleh PJ Cabor
     */
    public function prosesVerifikasiCabor(
        Pendaftaran $pendaftaran,
        User $user,
        string $aksi, // 'setuju', 'revisi', 'tolak'
        ?string $catatan = null
    ): Pendaftaran {
        return DB::transaction(function () use ($pendaftaran, $user, $aksi, $catatan) {
            $statusSebelum = $pendaftaran->status;
            $statusSesudah = match ($aksi) {
                'setuju' => 'diverifikasi_cabor',
                'revisi' => 'revisi',
                'tolak' => 'ditolak',
                default => throw new \InvalidArgumentException("Aksi verifikasi cabor tidak valid: {$aksi}"),
            };

            $pendaftaran->update([
                'status' => $statusSesudah,
                'catatan_terakhir' => $catatan,
                'diverifikasi_cabor_oleh' => $user->id,
                'diverifikasi_cabor_pada' => now(),
            ]);

            RiwayatVerifikasi::create([
                'pendaftaran_id' => $pendaftaran->id,
                'user_id' => $user->id,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $statusSesudah,
                'catatan' => $catatan,
                'created_at' => now(),
            ]);

            return $pendaftaran->fresh();
        });
    }

    /**
     * Transisi Status Verifikasi Akhir oleh Admin
     */
    public function prosesVerifikasiAdmin(
        Pendaftaran $pendaftaran,
        User $user,
        string $aksi, // 'setuju', 'revisi', 'tolak'
        ?string $catatan = null
    ): Pendaftaran {
        return DB::transaction(function () use ($pendaftaran, $user, $aksi, $catatan) {
            $statusSebelum = $pendaftaran->status;
            $statusSesudah = match ($aksi) {
                'setuju' => 'disetujui',
                'revisi' => 'revisi',
                'tolak' => 'ditolak',
                default => throw new \InvalidArgumentException("Aksi verifikasi admin tidak valid: {$aksi}"),
            };

            $pendaftaran->update([
                'status' => $statusSesudah,
                'catatan_terakhir' => $catatan,
                'disetujui_oleh' => $user->id,
                'disetujui_pada' => now(),
            ]);

            RiwayatVerifikasi::create([
                'pendaftaran_id' => $pendaftaran->id,
                'user_id' => $user->id,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $statusSesudah,
                'catatan' => $catatan,
                'created_at' => now(),
            ]);

            return $pendaftaran->fresh();
        });
    }
}
