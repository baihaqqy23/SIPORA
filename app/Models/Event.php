<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'logo_path',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_patokan_umur',
        'pendaftaran_mulai',
        'pendaftaran_selesai',
        'maks_nomor_lomba_per_atlet',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_patokan_umur' => 'date',
            'pendaftaran_mulai' => 'datetime',
            'pendaftaran_selesai' => 'datetime',
            'maks_nomor_lomba_per_atlet' => 'integer',
        ];
    }

    public function cabangOlahraga(): HasMany
    {
        return $this->hasMany(CabangOlahraga::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function kontingen(): HasMany
    {
        return $this->hasMany(Kontingen::class);
    }

    public function panitia(): HasMany
    {
        return $this->hasMany(Panitia::class);
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function acaraRundown(): HasMany
    {
        return $this->hasMany(AcaraRundown::class);
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class);
    }

    public function isPendaftaranDibuka(): bool
    {
        $now = now();

        return in_array($this->status, ['pendaftaran_dibuka', 'berlangsung'])
            && $now->between($this->pendaftaran_mulai, $this->pendaftaran_selesai);
    }
}
