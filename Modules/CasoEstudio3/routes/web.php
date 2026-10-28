<?php

use Illuminate\Support\Facades\Route;
use Modules\CasoEstudio3\Http\Controllers\CasoEstudio3Controller;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('casoestudio3s', CasoEstudio3Controller::class)->names('casoestudio3');
});
