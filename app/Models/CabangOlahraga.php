<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabangOlahraga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cabang_olahraga';

    protected $fillable = [
        'event_id',
        'nama',
        'singkatan',
        'warna',
        'deskripsi',
        'durasi_default_menit',
        'jeda_antar_tanding_menit',
    ];

    protected function casts(): array
    {
        return [
            'durasi_default_menit' => 'integer',
            'jeda_antar_tanding_menit' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function nomorLomba(): HasMany
    {
        return $this->hasMany(NomorLomba::class);
    }

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'cabor_venue', 'cabang_olahraga_id', 'venue_id');
    }

    public function penugasanPanitia(): HasMany
    {
        return $this->hasMany(PenugasanPanitia::class);
    }

    public function pjPanitia()
    {
        return $this->penugasanPanitia()
            ->where('peran', 'pj_cabor')
            ->with('panitia')
            ->get()
            ->pluck('panitia');
    }
}
