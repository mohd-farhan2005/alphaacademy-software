<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Enums\RoleType;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === RoleType::SUPER_ADMIN) {
            return true;
        }
        return null;
    }

    public function view(User $user, Task $task): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            return $user->department_id === $task->assignee->department_id;
        }
        return $user->id === $task->assigned_to || $user->id === $task->assigned_by;
    }

    public function update(User $user, Task $task): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            return $user->department_id === $task->assignee->department_id;
        }
        return $user->id === $task->assigned_to || $user->id === $task->assigned_by;
    }

    public function delete(User $user, Task $task): bool
    {
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            return $user->department_id === $task->assignee->department_id;
        }
        return $user->id === $task->assigned_to || $user->id === $task->assigned_by;
    }
}
