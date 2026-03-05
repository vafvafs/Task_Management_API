<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| Application bootstrap: routing, middleware aliases, exception handling
|--------------------------------------------------------------------------
*/
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Allow routes to use ->middleware('role:admin,team_leader'); arguments become ...$roles
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // When a Task is not found (e.g. GET /tasks/999), return 404 with API spec message (not default Laravel)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson() && $e->getModel() === \App\Models\Task::class) {
                return response()->json(['message' => 'Tasks not found.'], 404);
            }
        });

        // When validation fails due to duplicate task title, return 400 (not 422) with spec message
        $exceptions->render(function (ValidationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }
            $errors = $e->errors();
            if (isset($errors['title']) && in_array('Title already exist', $errors['title'], true)) {
                return response()->json(['message' => 'Title already exist'], 400);
            }
            return null;
        });
    })
    ->create();
