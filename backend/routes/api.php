<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Get teams list (public for registration)
Route::get('/teams', [TeamController::class, 'index']);

// Public file viewing (can be restricted later)
Route::get('/files', [FileController::class, 'index']);
Route::get('/storage/stats', [FileController::class, 'storageStats']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // File management routes
    Route::post('/files', [FileController::class, 'store']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::get('/files/{id}/download', [FileController::class, 'download']);
    Route::put('/files/{id}', [FileController::class, 'update']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    
    // Team routes
    Route::get('/teams/{id}', [TeamController::class, 'show']);
    Route::get('/teams/{id}/files', [TeamController::class, 'files']);
    Route::get('/teams/{id}/dashboard', [TeamController::class, 'dashboard']);
});
