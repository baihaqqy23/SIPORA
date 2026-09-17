<?php

namespace App\Services;

use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use App\Models\PesertaPertandingan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DrawingService
{
    /**
     * Ambil seluruh peserta yang sah (pendaftaran disetujui) untuk sebuah nomor lomba (BRK-01)
     *
     * @return Collection<int, array{type: string, id: int, nama: string, kontingen_id: int, kontingen_nama: string}>
     */
    public function getPesertaSah(NomorLomba $nomorLomba): Collection
    {
        $pendaftaranList = Pendaftaran::where('nomor_lomba_id', $nomorLomba->id)
            ->where('status', 'disetujui')
            ->with(['atlet.kontingen', 'timKontingen.kontingen'])
            ->get();

        return $pendaftaranList->map(function ($p) {
            if ($p->atlet_id && $p->atlet) {
                return [
                    'type' => 'App\\Models\\Atlet',
                    'id' => $p->atlet->id,
                    'nama' => $p->atlet->nama,
                    'kontingen_id' => $p->kontingen_id,
                    'kontingen_nama' => $p->atlet->kontingen->nama ?? 'Kontingen',
                    'provinsi' => $p->atlet->kontingen->provinsi ?? '',
                ];
            }

            if ($p->tim_kontingen_id && $p->timKontingen) {
                return [
                    'type' => 'App\\Models\\TimKontingen',
                    'id' => $p->timKontingen->id,
                    'nama' => $p->timKontingen->nama,
                    'kontingen_id' => $p->kontingen_id,
                    'kontingen_nama' => $p->timKontingen->kontingen->nama ?? 'Kontingen',
                    'provinsi' => $p->timKontingen->kontingen->provinsi ?? '',
                ];
            }

            return null;
        })->filter()->values();
    }

    /**
     * Mengambil bracket yang sudah digenerate untuk sebuah nomor lomba
     */
    public function getBracket(NomorLomba $nomorLomba): array
    {
        $pertandingans = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)
            ->with([
                'pesertaPertandingan.peserta',
                'hasilPertandingan.pemenang',
                'lapangan.venue',
            ])
            ->orderBy('urutan_bracket')
            ->get();

        $grouped = $pertandingans->groupBy('babak');

        return [
            'nomor_lomba' => $nomorLomba,
            'format' => $nomorLomba->format_pertandingan,
            'status' => $nomorLomba->status_bracket,
            'total_pertandingan' => $pertandingans->count(),
            'pertandingans' => $pertandingans,
            'rounds' => $grouped,
            'peserta_sah' => $this->getPesertaSah($nomorLomba),
        ];
    }

    /**
     * Preview Drawing (BRK-08) sebelum disimpan permanen ke database
     *
     * @param  array<int, int>|string  $seededIds
     */
    public function previewDrawing(NomorLomba $nomorLomba, array|string $seededIds = [], int $jumlahGrup = 2, int $kapasitasLintasan = 8): array
    {
        $seeds = is_array($seededIds) ? $seededIds : [];
        $peserta = $this->getPesertaSah($nomorLomba);

        if ($peserta->count() < 2 && $nomorLomba->format_pertandingan !== 'penilaian') {
            throw new \RuntimeException('Jumlah peserta sah minimal 2 untuk melakukan drawing.');
        }

        // Susun peserta dengan seeding dan proteksi kontingen (BRK-06)
        $pesertaOrdered = $this->organisirPesertaSeedingDanProteksi($peserta, $seeds);

        return match ($nomorLomba->format_pertandingan) {
            'gugur_tunggal' => $this->bangunStrukturGugurTunggal($pesertaOrdered),
            'round_robin' => $this->bangunStrukturRoundRobin($pesertaOrdered, $jumlahGrup),
            'heat' => $this->bangunStrukturHeat($pesertaOrdered, $kapasitasLintasan),
            'penilaian' => $this->bangunStrukturPenilaian($pesertaOrdered),
            default => $this->bangunStrukturGugurTunggal($pesertaOrdered),
        };
    }

    /**
     * Langsung laksanakan drawing dan simpan ke database
     */
    public function laksanakanDrawing(NomorLomba $nomorLomba, array|string $seed = 'acak'): void
    {
        $preview = $this->previewDrawing($nomorLomba, is_array($seed) ? $seed : []);
        $this->simpanDrawing($nomorLomba, $preview);
    }

    /**
     * Simpan hasil drawing ke database secara permanen (BRK-09, BRK-10)
     */
    public function simpanDrawing(NomorLomba $nomorLomba, array $previewData): void
    {
        // Cegah reset/overwrite bila sudah ada hasil tersimpan (BRK-10)
        $hasResult = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)
            ->whereHas('hasilPertandingan')
            ->exists();

        if ($hasResult) {
            throw new \RuntimeException('Tidak dapat mereset bracket: sudah ada hasil pertandingan yang tersimpan.');
        }

        DB::transaction(function () use ($nomorLomba, $previewData) {
            // Hapus pertandingan lama yang belum ada hasil
            $lama = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)->get();
            foreach ($lama as $p) {
                PesertaPertandingan::where('pertandingan_id', $p->id)->delete();
                $p->delete();
            }

            if ($nomorLomba->format_pertandingan === 'gugur_tunggal' || empty($nomorLomba->format_pertandingan)) {
                $this->persistGugurTunggal($nomorLomba, $previewData);
            } elseif ($nomorLomba->format_pertandingan === 'round_robin') {
                $this->persistRoundRobin($nomorLomba, $previewData);
            } elseif ($nomorLomba->format_pertandingan === 'heat') {
                $this->persistHeat($nomorLomba, $previewData);
            } elseif ($nomorLomba->format_pertandingan === 'penilaian') {
                $this->persistPenilaian($nomorLomba, $previewData);
            }

            $nomorLomba->update(['status_bracket' => 'tergenerate']);
        });
    }

    /**
     * Reset Bracket oleh Admin (BRK-10)
     */
    public function resetBracket(NomorLomba $nomorLomba): void
    {
        $hasResult = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)
            ->whereHas('hasilPertandingan')
            ->exists();

        if ($hasResult) {
            throw new \RuntimeException('Tidak dapat mereset bracket: sudah ada hasil pertandingan yang tersimpan.');
        }

        DB::transaction(function () use ($nomorLomba) {
            $pertandingans = Pertandingan::where('nomor_lomba_id', $nomorLomba->id)->get();
            foreach ($pertandingans as $p) {
                PesertaPertandingan::where('pertandingan_id', $p->id)->delete();
                $p->delete();
            }

            $nomorLomba->update(['status_bracket' => 'belum']);
        });
    }

    /**
     * Organisir peserta dengan memisahkan peserta dari kontingen yang sama (BRK-06)
     */
    protected function organisirPesertaSeedingDanProteksi(Collection $peserta, array $seededIds): Collection
    {
        // Pisahkan seeded dan unseeded
        $seeded = $peserta->filter(fn ($p) => in_array($p['id'], $seededIds))->values();
        $unseeded = $peserta->reject(fn ($p) => in_array($p['id'], $seededIds))->shuffle()->values();

        // Kelompokkan per kontingen untuk proteksi kontingen di babak awal
        $grouped = $unseeded->groupBy('kontingen_id');

        $interleaved = collect();
        $maxItems = $grouped->max(fn ($g) => $g->count()) ?? 0;

        for ($i = 0; $i < $maxItems; $i++) {
            foreach ($grouped as $group) {
                if ($group->has($i)) {
                    $interleaved->push($group->get($i));
                }
            }
        }

        return $seeded->concat($interleaved);
    }

    /**
     * Struktur Bagan Gugur Tunggal dengan auto-bye (BRK-02)
     */
    protected function bangunStrukturGugurTunggal(Collection $peserta): array
    {
        $count = $peserta->count();
        // Hitung kelipatan pangkat 2 terdekat
        $bracketSize = 2;
        while ($bracketSize < $count) {
            $bracketSize *= 2;
        }

        $totalRounds = (int) log($bracketSize, 2);
        $totalByes = $bracketSize - $count;

        // Distribusikan slot peserta dan bye
        $slots = array_fill(0, $bracketSize, null);

        // Seeding / penempatan selang-seling peserta
        $pesertaList = $peserta->toArray();
        $assigned = 0;

        // Pasang peserta ke slot babak 1
        for ($i = 0; $i < $bracketSize; $i++) {
            if ($assigned < $count) {
                // Beri bye selang-seling di slot kedua tiap match bila ada bye
                if ($totalByes > 0 && ($i % 2 === 1)) {
                    $slots[$i] = null; // BYE
                    $totalByes--;
                } else {
                    $slots[$i] = $pesertaList[$assigned++];
                }
            }
        }

        // Buat babak-babak
        $rounds = [];
        $currentMatchesCount = $bracketSize / 2;

        for ($r = 1; $r <= $totalRounds; $r++) {
            $namaBabak = $this->getNamaBabakGugur($totalRounds, $r);
            $matches = [];

            for ($m = 0; $m < $currentMatchesCount; $m++) {
                $p1 = ($r === 1) ? ($slots[$m * 2] ?? null) : null;
                $p2 = ($r === 1) ? ($slots[$m * 2 + 1] ?? null) : null;

                $matches[] = [
                    'urutan' => $m + 1,
                    'babak' => $namaBabak,
                    'round_number' => $r,
                    'peserta_1' => $p1,
                    'peserta_2' => $p2,
                    'is_bye' => ($r === 1) && ($p1 !== null && $p2 === null),
                ];
            }

            $rounds[] = [
                'round_number' => $r,
                'nama' => $namaBabak,
                'matches' => $matches,
            ];

            $currentMatchesCount /= 2;
        }

        return [
            'format' => 'gugur_tunggal',
            'bracket_size' => $bracketSize,
            'rounds' => $rounds,
        ];
    }

    protected function getNamaBabakGugur(int $totalRounds, int $currentRound): string
    {
        $remainingRounds = $totalRounds - $currentRound;

        return match ($remainingRounds) {
            0 => 'Final',
            1 => 'Semifinal',
            2 => 'Perempat Final',
            3 => '16 Besar',
            4 => '32 Besar',
            5 => '64 Besar',
            default => 'Babak '.$currentRound,
        };
    }

    protected function bangunStrukturRoundRobin(Collection $peserta, int $jumlahGrup): array
    {
        $grupList = [];
        $grupNames = range('A', 'Z');
        $pesertaList = $peserta->all();

        // Bagi peserta ke grup
        for ($i = 0; $i < count($pesertaList); $i++) {
            $grupIndex = $i % $jumlahGrup;
            $grupList[$grupIndex][] = $pesertaList[$i];
        }

        $resultGrup = [];
        foreach ($grupList as $idx => $members) {
            $grupName = 'Grup '.($grupNames[$idx] ?? ($idx + 1));
            $matches = [];
            $matchUrutan = 1;

            // Pasangkan setiap 2 peserta (round-robin: n*(n-1)/2)
            $n = count($members);
            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $matches[] = [
                        'urutan' => $matchUrutan++,
                        'babak' => $grupName,
                        'peserta_1' => $members[$i],
                        'peserta_2' => $members[$j],
                    ];
                }
            }

            $resultGrup[] = [
                'nama' => $grupName,
                'peserta' => $members,
                'matches' => $matches,
            ];
        }

        return [
            'format' => 'round_robin',
            'groups' => $resultGrup,
        ];
    }

    protected function bangunStrukturHeat(Collection $peserta, int $kapasitasLintasan): array
    {
        $heats = [];
        $pesertaChunks = $peserta->chunk($kapasitasLintasan);
        $heatNum = 1;

        foreach ($pesertaChunks as $chunk) {
            $namaBabak = 'Seri '.$heatNum;
            $pesertaWithLanes = [];
            $lane = 1;

            foreach ($chunk as $p) {
                $p['lintasan'] = $lane++;
                $pesertaWithLanes[] = $p;
            }

            $heats[] = [
                'urutan' => $heatNum,
                'babak' => $namaBabak,
                'peserta' => $pesertaWithLanes,
            ];
            $heatNum++;
        }

        return [
            'format' => 'heat',
            'heats' => $heats,
        ];
    }

    protected function bangunStrukturPenilaian(Collection $peserta): array
    {
        $shuffled = $peserta->shuffle()->values();
        $urutanList = [];
        $slot = 1;

        foreach ($shuffled as $p) {
            $p['urutan_tampil'] = $slot++;
            $urutanList[] = $p;
        }

        return [
            'format' => 'penilaian',
            'daftar_tampil' => $urutanList,
        ];
    }

    /**
     * Persist bracket gugur tunggal ke database dengan hierarki parent_pertandingan_id (BRK-11)
     */
    protected function persistGugurTunggal(NomorLomba $nomorLomba, array $previewData): void
    {
        $rounds = array_reverse($previewData['rounds']); // Mulai dari Final ke bawah untuk bind parent
        $previousRoundMatches = [];

        foreach ($rounds as $round) {
            $currentRoundMatches = [];

            foreach ($round['matches'] as $mIdx => $m) {
                // Cari parent match di babak berikutnya (jika bukan Final)
                $parentIndex = (int) floor($mIdx / 2);
                $parentId = $previousRoundMatches[$parentIndex] ?? null;

                $pertandingan = Pertandingan::create([
                    'nomor_lomba_id' => $nomorLomba->id,
                    'babak' => $m['babak'],
                    'urutan_bracket' => $m['urutan'],
                    'parent_pertandingan_id' => $parentId,
                    'status' => 'draft',
                ]);

                $currentRoundMatches[$mIdx] = $pertandingan->id;

                // Masukkan peserta jika babak 1
                if (! empty($m['peserta_1'])) {
                    PesertaPertandingan::create([
                        'pertandingan_id' => $pertandingan->id,
                        'peserta_type' => $m['peserta_1']['type'],
                        'peserta_id' => $m['peserta_1']['id'],
                        'slot' => 1,
                    ]);
                }

                if (! empty($m['peserta_2'])) {
                    PesertaPertandingan::create([
                        'pertandingan_id' => $pertandingan->id,
                        'peserta_type' => $m['peserta_2']['type'],
                        'peserta_id' => $m['peserta_2']['id'],
                        'slot' => 2,
                    ]);
                }

                // Otomatis menangkan bila BYE (BRK-02, BRK-11)
                if (! empty($m['is_bye']) && ! empty($m['peserta_1'])) {
                    $peserta1 = PesertaPertandingan::where('pertandingan_id', $pertandingan->id)->first();
                    if ($peserta1) {
                        $peserta1->update(['hasil' => 'menang']);

                        // Promosikan ke parent jika parent ada
                        if ($parentId) {
                            $parentSlot = ($mIdx % 2 === 0) ? 1 : 2;
                            PesertaPertandingan::create([
                                'pertandingan_id' => $parentId,
                                'peserta_type' => $m['peserta_1']['type'],
                                'peserta_id' => $m['peserta_1']['id'],
                                'slot' => $parentSlot,
                            ]);
                        }
                    }
                }
            }

            $previousRoundMatches = $currentRoundMatches;
        }
    }

    protected function persistRoundRobin(NomorLomba $nomorLomba, array $previewData): void
    {
        foreach ($previewData['groups'] as $group) {
            foreach ($group['matches'] as $m) {
                $pertandingan = Pertandingan::create([
                    'nomor_lomba_id' => $nomorLomba->id,
                    'babak' => $m['babak'],
                    'urutan_bracket' => $m['urutan'],
                    'status' => 'draft',
                ]);

                if (! empty($m['peserta_1'])) {
                    PesertaPertandingan::create([
                        'pertandingan_id' => $pertandingan->id,
                        'peserta_type' => $m['peserta_1']['type'],
                        'peserta_id' => $m['peserta_1']['id'],
                        'slot' => 1,
                    ]);
                }

                if (! empty($m['peserta_2'])) {
                    PesertaPertandingan::create([
                        'pertandingan_id' => $pertandingan->id,
                        'peserta_type' => $m['peserta_2']['type'],
                        'peserta_id' => $m['peserta_2']['id'],
                        'slot' => 2,
                    ]);
                }
            }
        }
    }

    protected function persistHeat(NomorLomba $nomorLomba, array $previewData): void
    {
        foreach ($previewData['heats'] as $heat) {
            $pertandingan = Pertandingan::create([
                'nomor_lomba_id' => $nomorLomba->id,
                'babak' => $heat['babak'],
                'urutan_bracket' => $heat['urutan'],
                'status' => 'draft',
            ]);

            $slot = 1;
            foreach ($heat['peserta'] as $p) {
                PesertaPertandingan::create([
                    'pertandingan_id' => $pertandingan->id,
                    'peserta_type' => $p['type'],
                    'peserta_id' => $p['id'],
                    'slot' => $slot++,
                    'lintasan' => $p['lintasan'],
                ]);
            }
        }
    }

    protected function persistPenilaian(NomorLomba $nomorLomba, array $previewData): void
    {
        $pertandingan = Pertandingan::create([
            'nomor_lomba_id' => $nomorLomba->id,
            'babak' => 'Babak Penilaian',
            'urutan_bracket' => 1,
            'status' => 'draft',
        ]);

        $slot = 1;
        foreach ($previewData['daftar_tampil'] as $p) {
            PesertaPertandingan::create([
                'pertandingan_id' => $pertandingan->id,
                'peserta_type' => $p['type'],
                'peserta_id' => $p['id'],
                'slot' => $slot++,
                'lintasan' => $p['urutan_tampil'],
            ]);
        }
    }
}
