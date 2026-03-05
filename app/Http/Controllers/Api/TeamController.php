<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    
    public function index(Request $request)
    {
        $includeUsers = $request->query('include') === 'users';
        $query = Team::query();
        if ($includeUsers) {
            $query->with('users');
        }
        return api_success(TeamResource::collection($query->get()), '', 200);
    }

    
    public function store(StoreTeamRequest $request)
    {
        $this->authorize('create', Team::class);
        $team = Team::create(['name' => trim($request->validated('name'))]);
        return api_success(new TeamResource($team), '', 201);
    }

    
    public function show(Request $request, Team $team)
    {
        $this->authorize('view', $team);
        $includeUsers = $request->query('include') === 'users';
        if ($includeUsers) {
            $team->load('users');
        }
        return api_success(new TeamResource($team), '', 200);
    }

    
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);
        $team->update(['name' => trim($request->validated('name', $team->name))]);
        return api_success(new TeamResource($team), '', 200);
    }

   
    public function destroy(Request $request, Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();
        return response()->json(null, 204);
    }

   
    public function addMember(AddTeamMemberRequest $request, Team $team)
    {
        $this->authorize('addMember', $team);
        $user = User::findOrFail($request->validated('user_id'));
        $user->update(['team_id' => $team->id]);
        return api_success(new TeamResource($team->load('users')), 'Member added.', 200);
    }

   
    public function removeMember(Request $request, Team $team, User $user)
    {
        $this->authorize('removeMember', $team);
        if ($user->team_id != $team->id) {
            return api_error('User is not a member of this team.', 422);
        }
        $user->update(['team_id' => null]);
        return response()->json(null, 204);
    }
}
