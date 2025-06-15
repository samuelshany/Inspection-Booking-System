<?php

use Illuminate\Support\Facades\Route;
use Modules\Availability\Http\Controllers\AvailabilityController;
use Modules\Availability\Http\Controllers\TimeSlotController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::middleware(['auth:sanctum'])->prefix('v1/teamAvailability')->group(function () {
        Route::post('{team}/availability', [AvailabilityController::class, 'setTeamAvailability'])->middleware('admin');
        Route::get('{team}/generateSlots', [TimeSlotController::class, 'generateSlots']);
    });
});
