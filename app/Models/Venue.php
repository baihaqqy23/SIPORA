<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'venues';

    protected $fillable = [
        'event_id',
        'nama',
        'alamat',
        'kapasitas',
        'jam_operasional_mulai',
        'jam_operasional_selesai',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function lapangan(): HasMany
    {
        return $this->hasMany(Lapangan::class);
    }

    public function cabangOlahraga(): BelongsToMany
    {
        return $this->belongsToMany(CabangOlahraga::class, 'cabor_venue', 'venue_id', 'cabang_olahraga_id');
    }
}
