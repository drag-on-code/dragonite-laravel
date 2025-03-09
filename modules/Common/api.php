<?php

use Illuminate\Support\Facades\Route;
use Dragonite\Common\Controllers\CommonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('common', CommonController::class)->names('common');
});
