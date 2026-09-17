<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kontingen_id',
        'panitia_id',
        'harus_ganti_password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'harus_ganti_password' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPjCabor(): bool
    {
        return $this->role === 'pj_cabor';
    }

    public function isKontingen(): bool
    {
        return $this->role === 'kontingen';
    }

    public function kontingen(): BelongsTo
    {
        return $this->belongsTo(Kontingen::class);
    }

    public function panitia(): BelongsTo
    {
        return $this->belongsTo(Panitia::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Cabang olahraga yang ditugaskan ke PJ Cabor ini (lewat penugasan panitia).
     * Query langsung ke PenugasanPanitia agar tidak bergantung pada lazy-load relasi panitia.
     */
    public function caborDitugaskan(): Collection
    {
        if (! $this->panitia_id) {
            return collect();
        }

        return PenugasanPanitia::where('panitia_id', $this->panitia_id)
            ->whereNotNull('cabang_olahraga_id')
            ->with('cabangOlahraga')
            ->get()
            ->pluck('cabangOlahraga')
            ->filter()
            ->unique('id')
            ->values();
    }
}
