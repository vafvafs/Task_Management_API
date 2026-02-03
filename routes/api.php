<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | TASKS
    |--------------------------------------------------------------------------
    */

    // CRUD
    Route::get('/tasks', [TaskController::class, 'index']);     // GET /tasks?include=user
    Route::post('/tasks', [TaskController::class, 'store']);   // POST /tasks
    Route::get('/tasks/{id}', [TaskController::class, 'show']); // GET /tasks/:id
    Route::patch('/tasks/{id}', [TaskController::class, 'update']); // PATCH /tasks/:id
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // DELETE /tasks/:id

    // RELATIONSHIP
    Route::get('/tasks/{id}/user', [TaskController::class, 'user']);

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    // GET /users?include=tasks
    Route::get('/users', [UserController::class, 'index']);

    // GET /users/:id/tasks
    Route::get('/users/{id}/tasks', [UserController::class, 'tasks']);
});
