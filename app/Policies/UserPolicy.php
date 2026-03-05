<?php

namespace App\Policies;

use App\Models\User;


class UserPolicy
{
    /** Admin: any user. Team leader: users in same team. User: only self. */
    public function view(User $auth, User $target): bool
    {
        return $auth->isAdmin()
            || ($auth->isTeamLeader() && $target->team_id === $auth->team_id)
            || $auth->id === $target->id;
    }

    /** Only admin and team_leader can create users (route middleware also restricts POST /users). */
    public function create(User $auth): bool
    {
        return $auth->isAdmin() || $auth->isTeamLeader();
    }

    /** Admin: any user. Team leader: only users in their team. User: only self. */
    public function update(User $auth, User $target): bool
    {
        if ($auth->isAdmin()) {
            return true;
        }
        if ($auth->isTeamLeader() && $target->team_id === $auth->team_id) {
            return true;
        }
        return $auth->id === $target->id;
    }

    /** Same as view: if you can view the user, you can view their tasks. */
    public function viewTasks(User $auth, User $target): bool
    {
        return $this->view($auth, $target);
    }
}
