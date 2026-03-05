<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Base controller for all HTTP controllers.
 * AuthorizesRequests provides $this->authorize('ability', $model) which delegates to the matching Policy.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
