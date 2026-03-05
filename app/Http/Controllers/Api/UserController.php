<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $auth = $request->user();
        $includes = array_map('trim', explode(',', $request->query('include', '')));
        $loadTeam = in_array('team', $includes, true);
        $loadTasks = in_array('tasks', $includes, true);

        if ($auth->isAdmin()) {
            $query = User::query();
        } elseif ($auth->isTeamLeader()) {
            $query = User::where('team_id', $auth->team_id);
        } else {
            $u = $auth->fresh();
            if ($loadTeam) {
                $u->load('team');
            }
            if ($loadTasks) {
                $u->load('tasks');
            }
            return api_success(UserResource::collection(collect([$u])), '', 200);
        }

        if ($loadTeam) {
            $query->with('team');
        }
        if ($loadTasks) {
            $query->with('tasks');
        }
        return api_success(UserResource::collection($query->get()), '', 200);
    }


    public function show(Request $request, User $user)
    {
        $this->authorize('view', $user);
        $includes = array_map('trim', explode(',', $request->query('include', '')));
        $relations = array_filter(['team', 'tasks'], fn ($r) => in_array($r, $includes, true));
        if (!empty($relations)) {
            $user->load($relations);
        } else {
            $user->load('tasks', 'team');
        }
        return api_success(new UserResource($user), '', 200);
    }

    
    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);
        $auth = $request->user();
        $validated = $request->validated();
        $role = $validated['role'] ?? User::ROLE_USER;

        if ($auth->isTeamLeader() && $role !== User::ROLE_USER) {
            return api_error('Team leader can only create regular users', 403);
        }

        $teamId = null;
        if ($role === User::ROLE_TEAM_LEADER) {
            $teamId = !empty($validated['team_name'])
                ? Team::create(['name' => trim($validated['team_name'])])->id
                : ($validated['team_id'] ?? null);
        } elseif ($role === User::ROLE_USER) {
            $teamId = $auth->isTeamLeader() ? $auth->team_id : ($validated['team_id'] ?? null);
        }

        $user = User::create([
            'name'     => trim($validated['name']),
            'email'    => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role'     => $role,
            'team_id'  => $teamId,
        ]);
        return api_success(new UserResource($user), '', 201);
    }

    
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $auth = $request->user();
        $validated = $request->validated();
        $payload = [
            'name'  => isset($validated['name']) ? trim($validated['name']) : $user->name,
            'email' => isset($validated['email']) ? strtolower(trim($validated['email'])) : $user->email,
        ];

        if ($auth->isAdmin()) {
            $role = $validated['role'] ?? $user->role;
            $teamId = $user->team_id;
            if ($role === User::ROLE_ADMIN) {
                $teamId = null;
            } elseif ($role === User::ROLE_TEAM_LEADER) {
                $teamId = !empty($validated['team_name'])
                    ? Team::create(['name' => trim($validated['team_name'])])->id
                    : ($validated['team_id'] ?? $user->team_id);
            } elseif (array_key_exists('team_id', $validated)) {
                $teamId = $validated['team_id'];
            }
            $payload['role'] = $role;
            $payload['team_id'] = $teamId;
        }

        $user->update($payload);
        return api_success(new UserResource($user->load('team')), '', 200);
    }
 
    public function tasks(Request $request, User $user)
    {
        $this->authorize('viewTasks', $user);
        return api_success(TaskResource::collection($user->tasks), '', 200);
    }
}
