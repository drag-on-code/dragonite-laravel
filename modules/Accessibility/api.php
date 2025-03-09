<?php

use Dragonite\Accessibility\Controllers\AccessibilityController;
use Dragonite\Accessibility\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->prefix('v1')->group(function () {
    Route::prefix('auths')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('accessibility', AccessibilityController::class)->names('accessibility');
// });
