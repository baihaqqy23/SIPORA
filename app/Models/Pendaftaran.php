<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'event_id',
        'kontingen_id',
        'nomor_lomba_id',
        'atlet_id',
        'tim_kontingen_id',
        'status',
        'catatan_terakhir',
        'diverifikasi_cabor_oleh',
        'diverifikasi_cabor_pada',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    protected function casts(): array
    {
        return [
            'diverifikasi_cabor_pada' => 'datetime',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function kontingen(): BelongsTo
    {
        return $this->belongsTo(Kontingen::class);
    }

    public function nomorLomba(): BelongsTo
    {
        return $this->belongsTo(NomorLomba::class);
    }

    public function atlet(): BelongsTo
    {
        return $this->belongsTo(Atlet::class);
    }

    public function timKontingen(): BelongsTo
    {
        return $this->belongsTo(TimKontingen::class);
    }

    public function verifikatorCabor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_cabor_oleh');
    }

    public function verifikatorAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function riwayatVerifikasi(): HasMany
    {
        return $this->hasMany(RiwayatVerifikasi::class)->orderByDesc('created_at');
    }

    public function getPesertaNameAttribute(): string
    {
        if ($this->atlet_id && $this->atlet) {
            return $this->atlet->nama;
        }

        if ($this->tim_kontingen_id && $this->timKontingen) {
            return $this->timKontingen->nama;
        }

        return 'Peserta';
    }
}
