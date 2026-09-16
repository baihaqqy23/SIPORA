<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanPanitia extends Model
{
    use HasFactory;

    protected $table = 'penugasan_panitia';

    protected $fillable = [
        'panitia_id',
        'cabang_olahraga_id',
        'pertandingan_id',
        'peran',
    ];

    public function panitia(): BelongsTo
    {
        return $this->belongsTo(Panitia::class);
    }

    public function cabangOlahraga(): BelongsTo
    {
        return $this->belongsTo(CabangOlahraga::class);
    }

    public function pertandingan(): BelongsTo
    {
        return $this->belongsTo(Pertandingan::class);
    }
}
