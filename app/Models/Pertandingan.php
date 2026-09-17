<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pertandingan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pertandingan';

    protected $fillable = [
        'nomor_lomba_id',
        'lapangan_id',
        'babak',
        'urutan_bracket',
        'parent_pertandingan_id',
        'tanggal',
        'waktu_mulai',
        'durasi_menit',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'urutan_bracket' => 'integer',
            'tanggal' => 'date',
            'durasi_menit' => 'integer',
        ];
    }

    public function nomorLomba(): BelongsTo
    {
        return $this->belongsTo(NomorLomba::class);
    }

    public function lapangan(): BelongsTo
    {
        return $this->belongsTo(Lapangan::class);
    }

    public function parentPertandingan(): BelongsTo
    {
        return $this->belongsTo(Pertandingan::class, 'parent_pertandingan_id');
    }

    public function childPertandingan(): HasMany
    {
        return $this->hasMany(Pertandingan::class, 'parent_pertandingan_id');
    }

    public function peserta(): HasMany
    {
        return $this->pesertaPertandingan();
    }

    public function pesertaPertandingan(): HasMany
    {
        return $this->hasMany(PesertaPertandingan::class)->orderBy('slot');
    }

    public function hasil(): HasOne
    {
        return $this->hasOne(HasilPertandingan::class);
    }

    public function hasilPertandingan(): HasOne
    {
        return $this->hasOne(HasilPertandingan::class);
    }

    public function penugasanPanitia(): HasMany
    {
        return $this->hasMany(PenugasanPanitia::class);
    }

    public function getPeserta1Attribute()
    {
        return $this->pesertaPertandingan->firstWhere('slot', 1);
    }

    public function getPeserta2Attribute()
    {
        return $this->pesertaPertandingan->firstWhere('slot', 2);
    }

    public function getJamMulaiAttribute()
    {
        return $this->waktu_mulai;
    }

    public function setJamMulaiAttribute($value)
    {
        $this->attributes['waktu_mulai'] = $value;
    }
}
