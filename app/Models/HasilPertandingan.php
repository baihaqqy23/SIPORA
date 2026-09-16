<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPertandingan extends Model
{
    use HasFactory;

    protected $table = 'hasil_pertandingan';

    protected $fillable = [
        'pertandingan_id',
        'pemenang_peserta_id',
        'keterangan',
        'diinput_oleh',
        'diinput_pada',
        'diubah_oleh',
        'alasan_perubahan',
    ];

    protected function casts(): array
    {
        return [
            'diinput_pada' => 'datetime',
        ];
    }

    public function pertandingan(): BelongsTo
    {
        return $this->belongsTo(Pertandingan::class);
    }

    public function pemenang(): BelongsTo
    {
        return $this->belongsTo(PesertaPertandingan::class, 'pemenang_peserta_id');
    }

    public function penginput(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
