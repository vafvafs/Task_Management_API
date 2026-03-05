<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
   
    public function index(Request $request)
    {
        $auth = $request->user();
        $includeUser = $request->query('include') === 'user';
        $query = Task::visibleTo($auth);
        if ($includeUser) {
            $query->with('user');
        }
        return api_success(TaskResource::collection($query->get()), '', 200);
    }

   
    public function store(TaskStoreRequest $request)
    {
        $v = $request->validated();
        $task = Task::create([
            'title'       => $v['title'],
            'description' => $v['description'],
            'completed'   => $v['completed'],
            'user_id'     => $request->user()->id,
        ]);
        return api_success(new TaskResource($task->load('user')), '', 201);
    }

   
    public function show(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        return api_success(new TaskResource($task->load('user')), '', 200);
    }

   
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $v = $request->validated();
        $task->update([
            'title'       => $v['title'] ?? $task->title,
            'description' => $v['description'] ?? $task->description,
            'completed'   => $v['completed'] ?? $task->completed,
        ]);
        return api_success(new TaskResource($task->load('user')), '', 200);
    }

   
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(null, 204);
    }

   
    public function user(Request $request, Task $task)
    {
        $this->authorize('viewUser', $task);
        return api_success(new UserResource($task->user), '', 200);
    }
}
