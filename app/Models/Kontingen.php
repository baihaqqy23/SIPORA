<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kontingen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontingen';

    protected $fillable = [
        'event_id',
        'nama',
        'slug',
        'provinsi',
        'kota',
        'nama_ofisial',
        'no_hp_ofisial',
        'email',
        'surat_mandat_path',
        'logo_path',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function atlet(): HasMany
    {
        return $this->hasMany(Atlet::class);
    }

    public function timKontingen(): HasMany
    {
        return $this->hasMany(TimKontingen::class);
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function medali(): HasMany
    {
        return $this->hasMany(Medali::class);
    }
}
