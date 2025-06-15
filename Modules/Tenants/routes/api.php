<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenants\Http\Controllers\TenantsController;

Route::middleware(['auth:sanctum','admin'])->prefix('v1')->group(function () {
    Route::apiResource('tenants', TenantsController::class)->names('tenants');
});
