<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PesertaPertandingan extends Model
{
    use HasFactory;

    protected $table = 'peserta_pertandingan';

    protected $fillable = [
        'pertandingan_id',
        'peserta_type',
        'peserta_id',
        'slot',
        'lintasan',
        'skor',
        'catatan_waktu',
        'nilai',
        'hasil',
        'peringkat',
    ];

    protected function casts(): array
    {
        return [
            'slot' => 'integer',
            'lintasan' => 'integer',
            'skor' => 'integer',
            'catatan_waktu' => 'decimal:3',
            'nilai' => 'decimal:2',
            'peringkat' => 'integer',
        ];
    }

    public function pertandingan(): BelongsTo
    {
        return $this->belongsTo(Pertandingan::class);
    }

    public function peserta(): MorphTo
    {
        return $this->morphTo();
    }

    public function getNamaAttribute(): string
    {
        if (! $this->peserta) {
            return 'BYE / Menunggu';
        }

        return $this->peserta->nama ?? 'Peserta';
    }

    public function getKontingenAttribute()
    {
        if (! $this->peserta) {
            return null;
        }

        return $this->peserta->kontingen ?? null;
    }
}
