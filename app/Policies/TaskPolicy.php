<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /** Admin: any task. Owner: own task. Team leader: task belongs to a user in their team. */
    public function view(User $user, Task $task): bool
    {
        return $user->isAdmin()
            || $task->user_id === $user->id
            || ($user->isTeamLeader() && $task->user->team_id === $user->team_id);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /** Same as view: if you can see the task, you can see its owner. */
    public function viewUser(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }
}
