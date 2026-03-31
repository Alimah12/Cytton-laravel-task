<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

// Temporary test route to verify API routing
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::prefix('v1')->group(function () {
    Route::get('/tasks/report', [TaskController::class, 'report']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});
