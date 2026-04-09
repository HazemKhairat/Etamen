<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/home', [HomeController::class, 'index']);
    
    Route::apiResource('readings', ReadingController::class);
    
    Route::get('/stats/summary', [StatsController::class, 'summary']);
    Route::get('/stats/chart', [StatsController::class, 'chart']);
    
    Route::apiResource('reminders', ReminderController::class);
});
