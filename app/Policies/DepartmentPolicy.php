<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use App\Enums\RoleType;

class DepartmentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === RoleType::SUPER_ADMIN) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Department $department): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Department $department): bool
    {
        return false;
    }

    public function delete(User $user, Department $department): bool
    {
        return false;
    }
}
