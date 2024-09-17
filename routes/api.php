<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// For Auth
Route::prefix('v1')->controller(UserController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', 'logout');
        Route::get('/user/profile', 'show');
        Route::put('/user/{id}/update-profile', 'update');
        Route::delete('/user/{id}/delete', 'destory');
        Route::post('/user/restore', 'restore');
    });
    Route::post('/auth/register', 'store');
    Route::post('/auth/login', 'login');
});

// For Project and Task
Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
        Route::post('projects/{id}/restore', [ProjectController::class, 'restore']);
        Route::get('projects/{id}/latest-task', [ProjectController::class, 'latestTask']);
        Route::get('projects/{id}/oldest-task', [ProjectController::class, 'oldestTask']);
        Route::get('projects/{id}/high-priority-task', [ProjectController::class, 'highPriority']);
        Route::apiResource('tasks', TaskController::class)->except(['index', 'show']);
        Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
        Route::post('tasks/{id}/delivery', [TaskController::class, 'taskDelivery']);
    });
    Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
    Route::apiResource('tasks', TaskController::class)->only(['index', 'show']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
