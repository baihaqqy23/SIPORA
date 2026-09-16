<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Panitia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'panitia';

    protected $fillable = [
        'event_id',
        'user_id',
        'nama',
        'jabatan',
        'instansi',
        'no_hp',
        'email',
        'foto_path',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanPanitia::class);
    }
}
