<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users
     * Optional: ?include=tasks
     */
    public function index(Request $request)
    {
        if ($request->query('include') === 'tasks') {
            return response()->json(
                User::with('tasks')->get(),
                200
            );
        }

        return response()->json(
            User::all(),
            200
        );
    }

    /**
     * GET /api/users/{id}/tasks
     */
    public function tasks($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json(
            $user->tasks,
            200
        );
    }
}
