<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaTim extends Model
{
    use HasFactory;

    protected $table = 'anggota_tim';

    protected $fillable = [
        'tim_kontingen_id',
        'atlet_id',
        'peran',
    ];

    public function timKontingen(): BelongsTo
    {
        return $this->belongsTo(TimKontingen::class);
    }

    public function atlet(): BelongsTo
    {
        return $this->belongsTo(Atlet::class);
    }
}
