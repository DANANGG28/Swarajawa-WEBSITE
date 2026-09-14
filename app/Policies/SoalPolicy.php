<?php

namespace App\Policies;

use App\Models\Guru;
use App\Models\Soal;
use App\Models\Superadmin;

class SoalPolicy
{
    /**
     * Guru & superadmin dapat melihat bank soal.
     */
    public function viewAny(Guru|Superadmin $user): bool
    {
        return true;
    }

    public function view(Guru|Superadmin $user, Soal $soal): bool
    {
        return true;
    }

    public function create(Guru|Superadmin $user): bool
    {
        return true;
    }

    /**
     * Guru hanya boleh mengubah soal buatannya sendiri;
     * superadmin punya override penuh (PRD §9).
     */
    public function update(Guru|Superadmin $user, Soal $soal): bool
    {
        if ($user instanceof Superadmin) {
            return true;
        }

        return $soal->guru_id !== null && $soal->guru_id === $user->id;
    }

    public function delete(Guru|Superadmin $user, Soal $soal): bool
    {
        return $this->update($user, $soal);
    }
}
