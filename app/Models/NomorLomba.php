<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomorLomba extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nomor_lomba';

    protected $fillable = [
        'cabang_olahraga_id',
        'nama',
        'gender',
        'jenis',
        'jumlah_anggota',
        'jumlah_cadangan',
        'umur_min',
        'umur_maks',
        'format_pertandingan',
        'kuota_per_kontingen',
        'kapasitas_total',
        'jumlah_perunggu',
        'status_bracket',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_anggota' => 'integer',
            'jumlah_cadangan' => 'integer',
            'umur_min' => 'integer',
            'umur_maks' => 'integer',
            'kuota_per_kontingen' => 'integer',
            'kapasitas_total' => 'integer',
            'jumlah_perunggu' => 'integer',
        ];
    }

    public function cabangOlahraga(): BelongsTo
    {
        return $this->belongsTo(CabangOlahraga::class);
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function pendaftaranDisetujui(): HasMany
    {
        return $this->hasMany(Pendaftaran::class)->where('status', 'disetujui');
    }

    public function timKontingen(): HasMany
    {
        return $this->hasMany(TimKontingen::class);
    }

    public function pertandingan(): HasMany
    {
        return $this->hasMany(Pertandingan::class);
    }

    public function medali(): HasMany
    {
        return $this->hasMany(Medali::class);
    }
}
