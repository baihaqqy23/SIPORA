<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atlet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'atlet';

    protected $fillable = [
        'kontingen_id',
        'nama',
        'nik',
        'tanggal_lahir',
        'gender',
        'asal_kota',
        'foto_path',
        'status',
        'catatan_status',
    ];

    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'tanggal_lahir' => 'date',
        ];
    }

    public function kontingen(): BelongsTo
    {
        return $this->belongsTo(Kontingen::class);
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(BerkasAtlet::class);
    }

    public function berkasAtlet(): HasMany
    {
        return $this->hasMany(BerkasAtlet::class);
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function pesertaPertandingan(): MorphMany
    {
        return $this->morphMany(PesertaPertandingan::class, 'peserta');
    }

    public function medali(): MorphMany
    {
        return $this->morphMany(Medali::class, 'peserta');
    }

    /**
     * Hitung umur atlet pada tanggal patokan tertentu (event.tanggal_patokan_umur)
     */
    public function hitungUmurPada(?Carbon $patokan = null): int
    {
        if (! $this->tanggal_lahir) {
            return 0;
        }

        $tanggalPatokan = $patokan ?? now();

        return (int) $this->tanggal_lahir->diffInYears($tanggalPatokan);
    }
}
