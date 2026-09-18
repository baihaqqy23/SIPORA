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
     * Event yang ditugaskan ke PJ Cabor ini (atau semua event jika Admin).
     */
    public function eventsDitugaskan(): Collection
    {
        if ($this->isAdmin()) {
            return Event::orderBy('nama')->get();
        }

        $panitiaIds = Panitia::where('user_id', $this->id)
            ->when($this->panitia_id, fn ($q) => $q->orWhere('id', $this->panitia_id))
            ->pluck('id');

        if ($panitiaIds->isEmpty()) {
            return collect();
        }

        $caborIds = PenugasanPanitia::whereIn('panitia_id', $panitiaIds)
            ->whereNotNull('cabang_olahraga_id')
            ->pluck('cabang_olahraga_id');

        $eventIdsFromCabor = CabangOlahraga::whereIn('id', $caborIds)->pluck('event_id');
        $eventIdsFromPanitia = Panitia::whereIn('id', $panitiaIds)->pluck('event_id');
        $allEventIds = $eventIdsFromCabor->concat($eventIdsFromPanitia)->filter()->unique();

        return Event::whereIn('id', $allEventIds)->orderBy('nama')->get();
    }

    /**
     * Cabang olahraga yang ditugaskan ke PJ Cabor ini (lewat penugasan panitia).
     * Dapat difilter berdasarkan $eventId spesifik.
     */
    public function caborDitugaskan(?int $eventId = null): Collection
    {
        if ($this->isAdmin()) {
            return CabangOlahraga::with('event')
                ->when($eventId, fn ($q) => $q->where('event_id', $eventId))
                ->orderBy('nama')
                ->get();
        }

        $panitiaIds = Panitia::where('user_id', $this->id)
            ->when($this->panitia_id, fn ($q) => $q->orWhere('id', $this->panitia_id))
            ->pluck('id');

        if ($panitiaIds->isEmpty()) {
            return collect();
        }

        $query = PenugasanPanitia::whereIn('panitia_id', $panitiaIds)
            ->whereNotNull('cabang_olahraga_id')
            ->with(['cabangOlahraga.event']);

        if ($eventId) {
            $query->whereHas('cabangOlahraga', fn ($q) => $q->where('event_id', $eventId));
        }

        return $query->get()
            ->pluck('cabangOlahraga')
            ->filter()
            ->unique('id')
            ->values();
    }
}
