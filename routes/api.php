<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FeedController;

Route::apiResource('posts', FeedController::class);
