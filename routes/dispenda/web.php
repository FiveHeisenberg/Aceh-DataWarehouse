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

    Route::get('/dashboard', [DispendaController::class, 'index'])
        ->name('dashboard');

    Route::get('/tagihan', [DispendaController::class, 'dataTagihan'])
        ->name('tagihan');

    Route::get('/tagihan/{id}', [DispendaController::class, 'detailTagihan'])
        ->name('tagihan.detail');

    Route::get('/tagihan-export', [DispendaController::class, 'exportTagihan'])
        ->name('tagihan.export');

    Route::get('/objek-pajak', [DispendaController::class, 'objekPajak'])
        ->name('objek-pajak');

    Route::get('/objek-pajak-export', [DispendaController::class, 'exportObjekPajak'])
        ->name('objek-pajak.export');

    Route::get('/objek-pajak/{id}', [DispendaController::class, 'detailObjekPajak'])
        ->name('objek-pajak.detail');

});
