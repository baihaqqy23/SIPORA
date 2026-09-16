<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'aksi',
        'model_type',
        'model_id',
        'data_sebelum',
        'data_sesudah',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data_sebelum' => 'array',
            'data_sesudah' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(
        string $aksi,
        ?Model $model = null,
        ?array $sebelum = null,
        ?array $sesudah = null,
        ?string $catatan = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'data_sebelum' => $sebelum,
            'data_sesudah' => $sesudah ?? ($catatan ? ['catatan' => $catatan] : null),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
