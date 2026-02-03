<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     * Optional: ?include=user
     */
    public function index(Request $request)
    {
        if ($request->query('include') === 'user') {
            return response()->json(
                Task::with('user')->get(),
                200
            );
        }

        return response()->json(Task::all(), 200);
    }

    /**
     * GET /api/tasks/{id}
     */
    public function show($id)
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Tasks not found.'
            ], 404);
        }

        return response()->json($task, 200);
    }

    /**
     * POST /api/tasks
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks,title',
            'description' => 'nullable'
        ]);

        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'user_id'     => $request->user()->id
        ]);

        return response()->json($task, 201);
    }

    /**
     * PATCH /api/tasks/{id}
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Tasks not found.'
            ], 404);
        }

        // Prevent duplicate title on update
        if ($request->has('title')) {
            $exists = Task::where('title', $request->title)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Title already exist'
                ], 400);
            }
        }

        $task->update(
            $request->only(['title', 'description'])
        );

        return response()->json($task, 200);
    }

    /**
     * DELETE /api/tasks/{id}
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Tasks not found.'
            ], 404);
        }

        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * GET /api/tasks/{id}/user
     */
    public function user($id)
    {
        $task = Task::with('user')->find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Tasks not found.'
            ], 404);
        }

        return response()->json($task->user, 200);
    }
}
