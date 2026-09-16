<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasAtlet extends Model
{
    use HasFactory;

    protected $table = 'berkas_atlet';

    protected $fillable = [
        'atlet_id',
        'jenis',
        'file_path',
        'nama_file_asli',
        'ukuran_byte',
        'mime',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_byte' => 'integer',
        ];
    }

    public function atlet(): BelongsTo
    {
        return $this->belongsTo(Atlet::class);
    }
}
