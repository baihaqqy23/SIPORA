<?php

namespace App\Policies;

use App\Models\Pertandingan;
use App\Models\User;

class PertandinganPolicy
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
        return true;
    }

    public function view(User $user, Pertandingan $pertandingan): bool
    {
        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($pertandingan->nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        if ($user->isKontingen()) {
            // Can view if published or if their athlete is in it
            return in_array($pertandingan->status, ['dipublikasikan', 'berlangsung', 'selesai']);
        }

        return false;
    }

    public function update(User $user, Pertandingan $pertandingan): bool
    {
        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($pertandingan->nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        return false;
    }

    public function inputHasil(User $user, Pertandingan $pertandingan): bool
    {
        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($pertandingan->nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        return false;
    }
}
