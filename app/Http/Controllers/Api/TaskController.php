<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     */
    public function index()
    {
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
                'message' => 'Task not found.'
            ], 404);
        }

        return response()->json($task, 200);
    }

    /**
     * POST /api/tasks
     */
    public function store(Request $request)
    {
        // 🔹 Check if title already exists
        $exists = Task::where('title', $request->title)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Title already exist'
            ], 400);
        }

        // 🔹 Create task
        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
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
                'message' => 'Task not found.'
            ], 404);
        }

        // Optional: prevent duplicate title on update
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

        $task->update($request->only(['title', 'description']));

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
                'message' => 'Task not found.'
            ], 404);
        }

        $task->delete();

        // 204 = No Content
        return response()->noContent();
    }
}
