<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Medali extends Model
{
    use HasFactory;

    protected $table = 'medali';

    protected $fillable = [
        'nomor_lomba_id',
        'kontingen_id',
        'peserta_type',
        'peserta_id',
        'jenis',
    ];

    public function nomorLomba(): BelongsTo
    {
        return $this->belongsTo(NomorLomba::class);
    }

    public function kontingen(): BelongsTo
    {
        return $this->belongsTo(Kontingen::class);
    }

    public function peserta(): MorphTo
    {
        return $this->morphTo();
    }
}
