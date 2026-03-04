<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleType;

class UserPolicy
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
        return in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD]);
    }

    public function view(User $user, User $model): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            return $user->department_id === $model->department_id;
        }
        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD]);
    }

    public function update(User $user, User $model): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            // Heads can only update employees not other heads
            return $user->department_id === $model->department_id && $model->role === RoleType::EMPLOYEE;
        }
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            return $user->department_id === $model->department_id && $model->role === RoleType::EMPLOYEE;
        }
        return false;
    }
}
