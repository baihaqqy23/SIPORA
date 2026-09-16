<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lapangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lapangan';

    protected $fillable = [
        'venue_id',
        'nama',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function pertandingan(): HasMany
    {
        return $this->hasMany(Pertandingan::class);
    }
}
