<?php

use Illuminate\Support\Facades\Route;
use Modules\Availability\Http\Controllers\AvailabilityController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::middleware(['auth:sanctum'])->prefix('v1/teamAvailability')->group(function () {
        Route::post('store/{team}', [AvailabilityController::class, 'setTeamAvailability']);
    });
});
