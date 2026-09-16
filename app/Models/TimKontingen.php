<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimKontingen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tim_kontingen';

    protected $fillable = [
        'kontingen_id',
        'nomor_lomba_id',
        'nama',
    ];

    public function kontingen(): BelongsTo
    {
        return $this->belongsTo(Kontingen::class);
    }

    public function nomorLomba(): BelongsTo
    {
        return $this->belongsTo(NomorLomba::class);
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(AnggotaTim::class);
    }

    public function atlet(): BelongsToMany
    {
        return $this->belongsToMany(Atlet::class, 'anggota_tim', 'tim_kontingen_id', 'atlet_id')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function pesertaPertandingan(): MorphMany
    {
        return $this->morphMany(PesertaPertandingan::class, 'peserta');
    }

    public function medali(): MorphMany
    {
        return $this->morphMany(Medali::class, 'peserta');
    }
}
