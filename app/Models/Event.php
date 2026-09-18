<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    /**
     * Daftar kategori usia yang tersedia beserta batas umur min dan maks.
     *
     * @return array<string, array{label: string, umur_min: int|null, umur_maks: int|null}>
     */
    public static function daftarKategoriUsia(): array
    {
        return [
            'usia_muda' => ['label' => 'Usia Muda (< 17)', 'umur_min' => null, 'umur_maks' => 16],
            'remaja' => ['label' => 'Remaja (17–21)',   'umur_min' => 17,   'umur_maks' => 21],
            'junior' => ['label' => 'Junior (21–25)',   'umur_min' => 21,   'umur_maks' => 25],
            'senior' => ['label' => 'Senior (25–35)',   'umur_min' => 25,   'umur_maks' => 35],
            'master' => ['label' => 'Master (35+)',     'umur_min' => 35,   'umur_maks' => null],
        ];
    }

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'logo_path',
        'tanggal_mulai',
        'tanggal_selesai',
        'kategori_usia',
        'pendaftaran_mulai',
        'pendaftaran_selesai',
        'maks_nomor_lomba_per_atlet',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'pendaftaran_mulai' => 'datetime',
            'pendaftaran_selesai' => 'datetime',
            'maks_nomor_lomba_per_atlet' => 'integer',
        ];
    }

    /**
     * Ambil label tampilan dari kategori_usia.
     */
    public function getLabelKategoriUsiaAttribute(): string
    {
        return static::daftarKategoriUsia()[$this->kategori_usia]['label'] ?? $this->kategori_usia;
    }

    /**
     * Batas umur minimum dari kategori_usia.
     */
    public function getUmurMinKategoriAttribute(): ?int
    {
        return static::daftarKategoriUsia()[$this->kategori_usia]['umur_min'] ?? null;
    }

    /**
     * Batas umur maksimum dari kategori_usia.
     */
    public function getUmurMaksKategoriAttribute(): ?int
    {
        return static::daftarKategoriUsia()[$this->kategori_usia]['umur_maks'] ?? null;
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['berlangsung', 'pendaftaran_dibuka', 'pendaftaran_ditutup']);
    }

    public function cabangOlahraga(): HasMany
    {
        return $this->hasMany(CabangOlahraga::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function kontingen(): HasMany
    {
        return $this->hasMany(Kontingen::class);
    }

    public function panitia(): HasMany
    {
        return $this->hasMany(Panitia::class);
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function acaraRundown(): HasMany
    {
        return $this->hasMany(AcaraRundown::class);
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class);
    }

    public function isPendaftaranDibuka(): bool
    {
        $now = now();

        return in_array($this->status, ['pendaftaran_dibuka', 'berlangsung'])
            && $now->between($this->pendaftaran_mulai, $this->pendaftaran_selesai);
    }
}
