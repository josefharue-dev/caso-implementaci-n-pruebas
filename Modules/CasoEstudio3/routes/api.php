<?php

use Illuminate\Support\Facades\Route;
use Modules\CasoEstudio3\Http\Controllers\CasoEstudio3Controller;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('casoestudio3s', CasoEstudio3Controller::class)->names('casoestudio3');
});
