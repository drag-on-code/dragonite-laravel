<?php

use Illuminate\Support\Facades\Route;
use Dragonite\Accessibility\Controllers\AccessibilityController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('accessibility', AccessibilityController::class)->names('accessibility');
});
