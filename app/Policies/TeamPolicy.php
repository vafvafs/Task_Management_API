<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;


class TeamPolicy
{
    /** Everyone can list and view teams (view-only for non-admin). */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Team $team): bool
    {
        return true;
    }

    /** Only admin can create teams. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can update teams. */
    public function update(User $user, Team $team): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can delete teams. */
    public function delete(User $user, Team $team): bool
    {  
        return $user->isAdmin();
    }

    /** Admin: any team. Team leader: only their own team (team.id === auth.team_id). User: no. */
    public function addMember(User $user, Team $team): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->isTeamLeader() && $user->team_id === $team->id;
    }

    /** Admin: any team. Team leader: only their own team. User: no. */
    public function removeMember(User $user, Team $team): bool
    {
        return $this->addMember($user, $team);
    }
}
