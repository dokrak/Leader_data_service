<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

// File management routes
Route::get('/files', [FileController::class, 'index']);
Route::post('/files', [FileController::class, 'store']);
Route::get('/files/{id}', [FileController::class, 'show']);
Route::get('/files/{id}/download', [FileController::class, 'download']);
Route::put('/files/{id}', [FileController::class, 'update']);
Route::delete('/files/{id}', [FileController::class, 'destroy']);

// Storage statistics
Route::get('/storage/stats', [FileController::class, 'storageStats']);
