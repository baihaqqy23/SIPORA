<?php

namespace App\Policies;

use App\Models\Atlet;
use App\Models\User;

class AtletPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isKontingen();
    }

    public function view(User $user, Atlet $atlet): bool
    {
        if ($user->isKontingen()) {
            return $user->kontingen_id === $atlet->kontingen_id;
        }

        if ($user->isPjCabor()) {
            // PJ Cabor can view athletes registered in their cabor
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return $atlet->pendaftaran()
                ->whereHas('nomorLomba', fn ($q) => $q->whereIn('cabang_olahraga_id', $caborIds))
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isKontingen();
    }

    public function update(User $user, Atlet $atlet): bool
    {
        if ($user->isKontingen()) {
            if ($user->kontingen_id !== $atlet->kontingen_id) {
                return false;
            }

            // KTA-02: Atlet yang sudah punya pendaftaran disetujui tidak dapat diubah data intinya
            $hasApproved = $atlet->pendaftaran()->where('status', 'disetujui')->exists();

            return ! $hasApproved;
        }

        return false;
    }

    public function delete(User $user, Atlet $atlet): bool
    {
        if ($user->isKontingen()) {
            if ($user->kontingen_id !== $atlet->kontingen_id) {
                return false;
            }

            $hasApproved = $atlet->pendaftaran()->where('status', 'disetujui')->exists();

            return ! $hasApproved;
        }

        return false;
    }

    public function viewBerkas(User $user, Atlet $atlet): bool
    {
        if ($user->isKontingen()) {
            return $user->kontingen_id === $atlet->kontingen_id;
        }

        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return $atlet->pendaftaran()
                ->whereHas('nomorLomba', fn ($q) => $q->whereIn('cabang_olahraga_id', $caborIds))
                ->exists();
        }

        return false;
    }
}
