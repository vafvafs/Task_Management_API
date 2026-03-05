<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\TeamExcelController;


Route::post('/register', [AuthController::class, 'register']);
// Login: validates credentials, returns user + token for subsequent requests
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {

    // Revoke current user's tokens (logout)
    Route::post('/logout', [AuthController::class, 'logout']);

    
    Route::get('/tasks', [TaskController::class, 'index']);                    // List tasks; supports ?include=user
    Route::post('/tasks', [TaskController::class, 'store']);                   // Create task (assigns to current user)
    Route::get('/tasks/{task}', [TaskController::class, 'show']);             // Single task (404 if missing; policy for 403)
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);         // Update task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);       // Delete task (204)
    Route::get('/tasks/{task}/user', [TaskController::class, 'user']);        // Get the user who owns this task

  
    Route::get('/teams', [TeamController::class, 'index']);                   // List teams; ?include=users
    Route::get('/teams/{team}', [TeamController::class, 'show']);              // Single team; ?include=users
    Route::get('/teams/{team}/profile-photo', [ProfilePhotoController::class, 'previewTeam']); // Preview team profile photo
    Route::middleware('role:admin')->group(function () {
        Route::get('/export/teams', [TeamExcelController::class, 'export']);  // Export teams + members (Excel)
        Route::post('/import/teams', [TeamExcelController::class, 'import']); // Import teams + members (Excel)
        Route::post('/teams', [TeamController::class, 'store']);
        Route::patch('/teams/{team}', [TeamController::class, 'update']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::post('/teams/{team}/profile-photo', [ProfilePhotoController::class, 'uploadTeam']); // Upload team profile photo
    });
    Route::middleware('role:admin,team_leader')->group(function () {
        Route::post('/teams/{team}/members', [TeamController::class, 'addMember']);           // Body: { "user_id": 1 }
        Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
    });

   
    Route::get('/users', [UserController::class, 'index']);                    // List users; supports ?include=tasks
    Route::get('/users/{user}', [UserController::class, 'show']);             // Single user
    Route::get('/users/{user}/tasks', [UserController::class, 'tasks']);      // Tasks belonging to this user
    Route::get('/users/{user}/profile-photo', [ProfilePhotoController::class, 'previewUser']); // Preview user profile photo
    Route::post('/users/{user}/profile-photo', [ProfilePhotoController::class, 'uploadUser']); // Upload user profile photo

    // Only admin and team_leader can create users (enforced here and in UserPolicy)
    Route::middleware('role:admin,team_leader')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
    });

    // Update user: admin=any, team_leader=team members, user=self (enforced in controller via policy)
    Route::patch('/users/{user}', [UserController::class, 'update']);
});
