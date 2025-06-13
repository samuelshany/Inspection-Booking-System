<?php

use Illuminate\Support\Facades\Route;
use Modules\Availability\Http\Controllers\AvailabilityController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('availabilities', AvailabilityController::class)->names('availability');
});
