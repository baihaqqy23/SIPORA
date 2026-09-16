<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcaraRundown extends Model
{
    use HasFactory;

    protected $table = 'acara_rundown';

    protected $fillable = [
        'event_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'judul',
        'lokasi',
        'penanggung_jawab',
        'catatan',
        'dipublikasikan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'dipublikasikan' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
