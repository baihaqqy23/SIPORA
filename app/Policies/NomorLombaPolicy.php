<?php

namespace App\Policies;

use App\Models\NomorLomba;
use App\Models\User;

class NomorLombaPolicy
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

    public function view(User $user, NomorLomba $nomorLomba): bool
    {
        return true;
    }

    public function manageBracket(User $user, NomorLomba $nomorLomba): bool
    {
        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        return false;
    }
}
