<?php

namespace App\Services;

use App\Models\Lapangan;
use App\Models\Pertandingan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PenjadwalanService
{
    /**
     * Mengambil matriks jadwal untuk tampilan papan jadwal (JDW-01)
     *
     * @return array{
     *     tanggal: string,
     *     lapangan: Collection,
     *     slots: array<string>,
     *     pertandingan: Collection,
     *     unallocated: Collection,
     *     matrix: array<int, array<string, Pertandingan|null>>,
     *     konflik: array
     * }
     */
    public function getMatrix(string $tanggal, ?int $caborId = null, ?int $venueId = null): array
    {
        $lapanganQuery = Lapangan::with('venue')->orderBy('venue_id')->orderBy('nama');
        if ($venueId) {
            $lapanganQuery->where('venue_id', $venueId);
        }
        $lapanganList = $lapanganQuery->get();

        $lagaQuery = Pertandingan::whereDate('tanggal', $tanggal)
            ->whereNotNull('lapangan_id')
            ->whereNotNull('waktu_mulai')
            ->whereNotIn('status', ['dibatalkan'])
            ->with([
                'lapangan.venue',
                'nomorLomba.cabangOlahraga',
                'pesertaPertandingan.peserta',
                'penugasanPanitia.panitia',
            ]);

        if ($caborId) {
            $lagaQuery->whereHas('nomorLomba', fn ($q) => $q->where('cabang_olahraga_id', $caborId));
        }

        $pertandingans = $lagaQuery->get();

        // Pertandingan yang belum dialokasikan (belum ada lapangan / waktu)
        $unallocated = Pertandingan::where(function ($q) use ($tanggal) {
            $q->whereNull('lapangan_id')
                ->orWhereNull('waktu_mulai')
                ->orWhereDate('tanggal', $tanggal);
        })
            ->where('status', 'draft')
            ->with(['nomorLomba.cabangOlahraga', 'pesertaPertandingan.peserta'])
            ->get();

        $konflikResult = $this->deteksiKonflik($tanggal);

        // Slot waktu interval 30 menit dari 07:00 sampai 21:00
        $slots = [];
        $startTime = Carbon::parse('07:00');
        $endTime = Carbon::parse('21:00');

        while ($startTime->lte($endTime)) {
            $slots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        // Susun struktur matrix: [lapangan_id][time_slot] => Pertandingan
        $matrix = [];
        foreach ($lapanganList as $lap) {
            $matrix[$lap->id] = [];
            foreach ($slots as $slot) {
                $matrix[$lap->id][$slot] = null;
            }
        }

        foreach ($pertandingans as $match) {
            if (isset($matrix[$match->lapangan_id])) {
                $timeFormatted = substr($match->waktu_mulai, 0, 5);
                $matrix[$match->lapangan_id][$timeFormatted] = $match;
            }
        }

        return [
            'tanggal' => $tanggal,
            'lapangan' => $lapanganList,
            'slots' => $slots,
            'pertandingan' => $pertandingans,
            'pertandingans' => $pertandingans,
            'unallocated' => $unallocated,
            'matrix' => $matrix,
            'konflik' => $konflikResult,
        ];
    }

    /**
     * Alias for backward compatibility
     */
    public function getPapanJadwal(string $tanggal, ?int $caborId = null, ?int $venueId = null): array
    {
        return $this->getMatrix($tanggal, $caborId, $venueId);
    }

    /**
     * Alokasikan slot pertandingan baru (JDW-02)
     */
    public function alokasiSlot(
        Pertandingan $pertandingan,
        int $lapanganId,
        string $tanggal,
        string $waktuMulai,
        ?int $durasiMenit = null
    ): array {
        $result = $this->pindahkanSlot($pertandingan, $lapanganId, $tanggal, $waktuMulai, $durasiMenit);

        if (! $result['berhasil']) {
            throw new \RuntimeException($result['pesan']);
        }

        return $result;
    }

    /**
     * Pindahkan slot pertandingan yang sudah terjadwal (JDW-04)
     */
    public function pindahSlot(
        Pertandingan $pertandingan,
        int $lapanganIdBaru,
        string $tanggalBaru,
        string $waktuMulaiBaru,
        ?int $durasiMenit = null
    ): array {
        $result = $this->pindahkanSlot($pertandingan, $lapanganIdBaru, $tanggalBaru, $waktuMulaiBaru, $durasiMenit);

        if (! $result['berhasil']) {
            throw new \RuntimeException($result['pesan']);
        }

        return $result;
    }

    /**
     * Publikasikan jadwal pertandingan (JDW-05)
     */
    public function publikasikan(Pertandingan $pertandingan): bool
    {
        if (! $pertandingan->lapangan_id || ! $pertandingan->waktu_mulai || ! $pertandingan->tanggal) {
            throw new \RuntimeException('Pertandingan belum memiliki jadwal lengkap (lapangan, tanggal, atau waktu mulai).');
        }

        $pertandingan->update([
            'status' => 'terjadwal',
        ]);

        return true;
    }

    /**
     * Deteksi seluruh konflik pada satu tanggal tertentu (JDW-03)
     *
     * @return array{
     *     konflik_keras: array<int, array{tipe: string, pesan: string, pertandingan_ids: array<int>}>,
     *     konflik_lunak: array<int, array{tipe: string, pesan: string, pertandingan_ids: array<int>}>,
     *     ada_konflik_keras: bool,
     *     ada_konflik_lunak: bool
     * }
     */
    public function deteksiKonflik(string $tanggal): array
    {
        $pertandingans = Pertandingan::whereDate('tanggal', $tanggal)
            ->whereNotNull('lapangan_id')
            ->whereNotNull('waktu_mulai')
            ->whereNotIn('status', ['dibatalkan'])
            ->with([
                'lapangan.venue',
                'nomorLomba.cabangOlahraga',
                'pesertaPertandingan.peserta',
                'penugasanPanitia.panitia',
            ])
            ->get();

        $konflikKeras = [];
        $konflikLunak = [];

        // 1. Cek Bentrok Venue/Lapangan (Dua laga di lapangan yang sama dengan waktu overlap)
        $byLapangan = $pertandingans->groupBy('lapangan_id');
        foreach ($byLapangan as $lapanganId => $lagaList) {
            $count = $lagaList->count();
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $lagaA = $lagaList[$i];
                    $lagaB = $lagaList[$j];

                    if ($this->apakahWaktuOverlap($lagaA, $lagaB)) {
                        $lapanganNama = $lagaA->lapangan->nama ?? 'Lapangan #'.$lapanganId;
                        $venueNama = $lagaA->lapangan->venue->nama ?? 'Venue';
                        $konflikKeras[] = [
                            'tipe' => 'venue_dobel',
                            'pesan' => "Bentrok Lapangan: [{$venueNama} - {$lapanganNama}] dijadwalkan bersamaan untuk Pertandingan #{$lagaA->id} ({$lagaA->nomorLomba?->nama}) dan #{$lagaB->id} ({$lagaB->nomorLomba?->nama}) pada jam {$lagaA->waktu_mulai} / {$lagaB->waktu_mulai}.",
                            'pertandingan_ids' => [$lagaA->id, $lagaB->id],
                        ];
                    }
                }
            }
        }

        // 2. Cek Bentrok Atlet (Atlet bertanding overlap di 2 pertandingan)
        $atletMap = [];
        foreach ($pertandingans as $laga) {
            foreach ($laga->pesertaPertandingan as $peserta) {
                if ($peserta->peserta_type === 'App\\Models\\Atlet' && $peserta->peserta_id) {
                    $atletMap[$peserta->peserta_id][] = [
                        'atlet_nama' => $peserta->peserta->nama ?? 'Atlet #'.$peserta->peserta_id,
                        'pertandingan' => $laga,
                    ];
                }
            }
        }

        foreach ($atletMap as $atletId => $entries) {
            $count = count($entries);
            if ($count < 2) {
                continue;
            }

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $lagaA = $entries[$i]['pertandingan'];
                    $lagaB = $entries[$j]['pertandingan'];
                    $atletNama = $entries[$i]['atlet_nama'];

                    // Overlap waktu langsung -> Konflik Keras
                    if ($this->apakahWaktuOverlap($lagaA, $lagaB)) {
                        $konflikKeras[] = [
                            'tipe' => 'atlet_bertumpuk',
                            'pesan' => "Bentrok Jadwal Atlet: Atlet {$atletNama} memiliki jadwal tanding bertumpuk di Pertandingan #{$lagaA->id} dan #{$lagaB->id}.",
                            'pertandingan_ids' => [$lagaA->id, $lagaB->id],
                        ];
                    } else {
                        // Cek jeda minimum antar tanding -> Konflik Lunak (JDW-04)
                        $jedaMenit = $this->hitungJedaMenit($lagaA, $lagaB);
                        $minJeda = max(
                            $lagaA->nomorLomba?->cabangOlahraga?->jeda_antar_tanding_menit ?? 30,
                            $lagaB->nomorLomba?->cabangOlahraga?->jeda_antar_tanding_menit ?? 30
                        );

                        if ($jedaMenit >= 0 && $jedaMenit < $minJeda) {
                            $konflikLunak[] = [
                                'tipe' => 'jeda_kurang',
                                'pesan' => "Peringatan Jeda Atlet: Atlet {$atletNama} hanya memiliki jeda istirahat {$jedaMenit} menit antara Pertandingan #{$lagaA->id} dan #{$lagaB->id} (Standar minimal: {$minJeda} menit).",
                                'pertandingan_ids' => [$lagaA->id, $lagaB->id],
                            ];
                        }
                    }
                }
            }
        }

        // 3. Cek Bentrok Wasit / Panitia (1 Panitia ditugaskan di 2 pertandingan overlap)
        $panitiaMap = [];
        foreach ($pertandingans as $laga) {
            foreach ($laga->penugasanPanitia as $penugasan) {
                if ($penugasan->panitia_id) {
                    $panitiaMap[$penugasan->panitia_id][] = [
                        'panitia_nama' => $penugasan->panitia->nama ?? 'Panitia',
                        'peran' => $penugasan->peran,
                        'pertandingan' => $laga,
                    ];
                }
            }
        }

        foreach ($panitiaMap as $panitiaId => $entries) {
            $count = count($entries);
            if ($count < 2) {
                continue;
            }

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $lagaA = $entries[$i]['pertandingan'];
                    $lagaB = $entries[$j]['pertandingan'];
                    $panitiaNama = $entries[$i]['panitia_nama'];

                    if ($this->apakahWaktuOverlap($lagaA, $lagaB)) {
                        $konflikLunak[] = [
                            'tipe' => 'panitia_bertumpuk',
                            'pesan' => "Bentrok Panitia: {$panitiaNama} ditugaskan pada 2 pertandingan bertumpuk (#{$lagaA->id} dan #{$lagaB->id}).",
                            'pertandingan_ids' => [$lagaA->id, $lagaB->id],
                        ];
                    }
                }
            }
        }

        return [
            'konflik_keras' => $konflikKeras,
            'konflik_lunak' => $konflikLunak,
            'ada_konflik_keras' => count($konflikKeras) > 0,
            'ada_konflik_lunak' => count($konflikLunak) > 0,
        ];
    }

    /**
     * Validasi dan pindahkan slot pertandingan (JDW-04, JDW-05)
     */
    public function pindahkanSlot(
        Pertandingan $pertandingan,
        int $lapanganId,
        string $tanggal,
        string $waktuMulai,
        ?int $durasiMenit = null
    ): array {
        $durasi = $durasiMenit ?? $pertandingan->durasi_menit ?? $pertandingan->nomorLomba?->cabangOlahraga?->durasi_default_menit ?? 60;

        // Simpan sementara nilai asli
        $oldLapangan = $pertandingan->lapangan_id;
        $oldTanggal = $pertandingan->tanggal?->format('Y-m-d');
        $oldWaktu = $pertandingan->waktu_mulai;
        $oldDurasi = $pertandingan->durasi_menit;

        // Set simulasi
        $pertandingan->lapangan_id = $lapanganId;
        $pertandingan->tanggal = $tanggal;
        $pertandingan->waktu_mulai = $waktuMulai;
        $pertandingan->durasi_menit = $durasi;
        $pertandingan->save();

        // Evaluasi konflik
        $hasilDeteksi = $this->deteksiKonflik($tanggal);

        // Cari apakah pertandingan ini terlibat dalam konflik keras
        $terlibatKonflikKeras = collect($hasilDeteksi['konflik_keras'])->first(function ($k) use ($pertandingan) {
            return in_array($pertandingan->id, $k['pertandingan_ids']);
        });

        if ($terlibatKonflikKeras) {
            // Rollback jika ada konflik keras (JDW-04)
            $pertandingan->lapangan_id = $oldLapangan;
            $pertandingan->tanggal = $oldTanggal;
            $pertandingan->waktu_mulai = $oldWaktu;
            $pertandingan->durasi_menit = $oldDurasi;
            $pertandingan->save();

            return [
                'berhasil' => false,
                'pesan' => 'Pemindahan ditolak karena menimbulkan konflik keras: '.$terlibatKonflikKeras['pesan'],
                'konflik' => $terlibatKonflikKeras,
            ];
        }

        // Ubah status ke 'terjadwal' jika sebelumnya draft
        if ($pertandingan->status === 'draft') {
            $pertandingan->update(['status' => 'terjadwal']);
        }

        return [
            'berhasil' => true,
            'pesan' => 'Jadwal pertandingan berhasil dialokasikan.',
            'sisa_konflik' => $hasilDeteksi,
        ];
    }

    /**
     * Helper cek overlap dua pertandingan
     */
    protected function apakahWaktuOverlap(Pertandingan $a, Pertandingan $b): bool
    {
        if (! $a->waktu_mulai || ! $b->waktu_mulai) {
            return false;
        }

        $durasiA = $a->durasi_menit ?? $a->nomorLomba?->cabangOlahraga?->durasi_default_menit ?? 60;
        $durasiB = $b->durasi_menit ?? $b->nomorLomba?->cabangOlahraga?->durasi_default_menit ?? 60;

        $startA = Carbon::parse($a->waktu_mulai);
        $endA = (clone $startA)->addMinutes($durasiA);

        $startB = Carbon::parse($b->waktu_mulai);
        $endB = (clone $startB)->addMinutes($durasiB);

        return $startA->lt($endB) && $startB->lt($endA);
    }

    /**
     * Hitung selisih jeda menit antara 2 pertandingan
     */
    protected function hitungJedaMenit(Pertandingan $a, Pertandingan $b): int
    {
        $durasiA = $a->durasi_menit ?? $a->nomorLomba?->cabangOlahraga?->durasi_default_menit ?? 60;
        $durasiB = $b->durasi_menit ?? $b->nomorLomba?->cabangOlahraga?->durasi_default_menit ?? 60;

        $startA = Carbon::parse($a->waktu_mulai);
        $endA = (clone $startA)->addMinutes($durasiA);

        $startB = Carbon::parse($b->waktu_mulai);
        $endB = (clone $startB)->addMinutes($durasiB);

        if ($endA->lte($startB)) {
            return (int) $endA->diffInMinutes($startB);
        }

        if ($endB->lte($startA)) {
            return (int) $endB->diffInMinutes($startA);
        }

        return -1; // Overlap
    }
}
