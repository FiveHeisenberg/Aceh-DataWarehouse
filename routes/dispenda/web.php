<?php

use App\Http\Controllers\Dispenda\DispendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dispenda Routes
|--------------------------------------------------------------------------
*/

Route::prefix('Dispenda')->name('dispenda.')->group(function () {

    Route::get('/', [DispendaController::class, 'index'])
        ->name('index');

});
