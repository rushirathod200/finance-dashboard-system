<?php

namespace App\Policies;

use App\Models\FinancialRecord;
use App\Models\User;

class FinancialRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, FinancialRecord $financialRecord): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->hasRole(User::ROLE_ADMIN, User::ROLE_ANALYST);
    }

    public function update(User $user, FinancialRecord $financialRecord): bool
    {
        return $user->is_active && $user->hasRole(User::ROLE_ADMIN, User::ROLE_ANALYST);
    }

    public function delete(User $user, FinancialRecord $financialRecord): bool
    {
        return $user->is_active && $user->hasRole(User::ROLE_ADMIN, User::ROLE_ANALYST);
    }
}
