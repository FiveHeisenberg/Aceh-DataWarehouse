<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Penduduk\PendudukController;

/*
|--------------------------------------------------------------------------
| Penduduk Routes
|--------------------------------------------------------------------------
*/

Route::prefix('Penduduk')->name('penduduk.')->group(function () {
    Route::get('/jumlah_penduduk', [PendudukController::class, 'index'])
         ->name('jumlah_penduduk');
});

Route::get('/kartu-keluarga', function () {
    return view('penduduk.kartu_keluarga');
})->name('penduduk.kartu_keluarga');