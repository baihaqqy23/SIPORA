<?php

namespace App\Services;

use App\Models\Kontingen;
use App\Models\Medali;
use App\Models\NomorLomba;
use App\Models\PesertaPertandingan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KlasemenMedaliService
{
    /**
     * Hitung rekap perolehan medali per kontingen (MED-01, MED-02, MED-03)
     *
     * @return Collection<int, array{
     *     peringkat: int,
     *     kontingen_id: int,
     *     kontingen_nama: string,
     *     provinsi: string,
     *     logo_path: ?string,
     *     emas: int,
     *     perak: int,
     *     perunggu: int,
     *     total: int
     * }>
     */
    public function getKlasemen(?int $eventId = null, ?int $caborId = null, bool $useCache = true): Collection
    {
        $cacheKey = "klasemen_medali_{$eventId}_{$caborId}";

        if ($useCache) {
            $data = Cache::remember($cacheKey, 60, fn () => $this->kalkulasiKlasemen($eventId, $caborId)->toArray());

            return collect($data);
        }

        return $this->kalkulasiKlasemen($eventId, $caborId);
    }

    protected function kalkulasiKlasemen(?int $eventId, ?int $caborId): Collection
    {
        // Ambil semua kontingen terdaftar di event
        $kontingenQuery = Kontingen::where('status', 'disetujui');
        if ($eventId) {
            $kontingenQuery->where('event_id', $eventId);
        }
        $kontingens = $kontingenQuery->get();

        // Ambil data medali yang sudah diinput
        $medaliQuery = Medali::query();
        if ($caborId) {
            $medaliQuery->whereHas('nomorLomba', fn ($q) => $q->where('cabang_olahraga_id', $caborId));
        }
        if ($eventId) {
            $medaliQuery->whereHas('kontingen', fn ($q) => $q->where('event_id', $eventId));
        }

        $medaliList = $medaliQuery->get();

        $rows = $kontingens->map(function ($k) use ($medaliList) {
            $kMedali = $medaliList->where('kontingen_id', $k->id);
            $emas = $kMedali->where('jenis', 'emas')->count();
            $perak = $kMedali->where('jenis', 'perak')->count();
            $perunggu = $kMedali->where('jenis', 'perunggu')->count();

            return [
                'kontingen_id' => $k->id,
                'kontingen_nama' => $k->nama,
                'provinsi' => $k->provinsi,
                'slug' => $k->slug,
                'kontingen_slug' => $k->slug,
                'logo_path' => $k->logo_path,
                'emas' => $emas,
                'perak' => $perak,
                'perunggu' => $perunggu,
                'total' => $emas + $perak + $perunggu,
            ];
        });

        // Urutkan Emas (desc) -> Perak (desc) -> Perunggu (desc) -> Nama Kontingen (asc) (MED-01)
        $sorted = $rows->sort(function ($a, $b) {
            if ($a['emas'] !== $b['emas']) {
                return $b['emas'] <=> $a['emas'];
            }
            if ($a['perak'] !== $b['perak']) {
                return $b['perak'] <=> $a['perak'];
            }
            if ($a['perunggu'] !== $b['perunggu']) {
                return $b['perunggu'] <=> $a['perunggu'];
            }

            return strcmp($a['kontingen_nama'], $b['kontingen_nama']);
        })->values();

        // Beri peringkat
        $rank = 1;

        return $sorted->map(function ($item) use (&$rank) {
            $item['peringkat'] = $rank++;

            return $item;
        });
    }

    /**
     * Catat perolehan medali untuk nomor lomba yang telah selesai pertandingannya (HSL-07)
     */
    public function tetapkanMedaliNomorLomba(
        NomorLomba $nomorLomba,
        PesertaPertandingan $emas,
        PesertaPertandingan $perak,
        Collection|PesertaPertandingan|array $perunggu
    ): void {
        DB::transaction(function () use ($nomorLomba, $emas, $perak, $perunggu) {
            // Bersihkan medali lama nomor lomba ini jika ada
            Medali::where('nomor_lomba_id', $nomorLomba->id)->delete();

            // Simpan Emas
            if ($emas->peserta && $emas->kontingen) {
                Medali::create([
                    'nomor_lomba_id' => $nomorLomba->id,
                    'kontingen_id' => $emas->kontingen->id,
                    'peserta_type' => $emas->peserta_type,
                    'peserta_id' => $emas->peserta_id,
                    'jenis' => 'emas',
                ]);
            }

            // Simpan Perak
            if ($perak->peserta && $perak->kontingen) {
                Medali::create([
                    'nomor_lomba_id' => $nomorLomba->id,
                    'kontingen_id' => $perak->kontingen->id,
                    'peserta_type' => $perak->peserta_type,
                    'peserta_id' => $perak->peserta_id,
                    'jenis' => 'perak',
                ]);
            }

            // Simpan Perunggu (bisa 1 atau 2 sesuai nomor lomba)
            $perungguList = is_iterable($perunggu) ? $perunggu : [$perunggu];
            foreach ($perungguList as $p) {
                if ($p && $p->peserta && $p->kontingen) {
                    Medali::create([
                        'nomor_lomba_id' => $nomorLomba->id,
                        'kontingen_id' => $p->kontingen->id,
                        'peserta_type' => $p->peserta_type,
                        'peserta_id' => $p->peserta_id,
                        'jenis' => 'perunggu',
                    ]);
                }
            }
        });

        // Hapus cache klasemen
        Cache::flush();
    }
}
