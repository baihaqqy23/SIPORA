<?php

namespace App\Policies;

use App\Models\Pendaftaran;
use App\Models\User;

class PendaftaranPolicy
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

    public function view(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->isKontingen()) {
            return $user->kontingen_id === $pendaftaran->kontingen_id;
        }

        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($pendaftaran->nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isKontingen();
    }

    public function update(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->isKontingen()) {
            if ($user->kontingen_id !== $pendaftaran->kontingen_id) {
                return false;
            }

            // REG-10: Pendaftaran tidak bisa diubah kontingen setelah status disetujui
            return in_array($pendaftaran->status, ['draft', 'menunggu', 'revisi']);
        }

        return false;
    }

    public function delete(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->isKontingen()) {
            if ($user->kontingen_id !== $pendaftaran->kontingen_id) {
                return false;
            }

            return in_array($pendaftaran->status, ['draft', 'menunggu', 'revisi']);
        }

        return false;
    }

    public function verifikasi(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPjCabor()) {
            $caborIds = $user->caborDitugaskan()->pluck('id');

            return in_array($pendaftaran->nomorLomba->cabang_olahraga_id, $caborIds->toArray());
        }

        return false;
    }
}
