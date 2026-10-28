<?php

use App\Http\Controllers\Kesehatan\KesehatanController;
use Illuminate\Support\Facades\Route;

Route::prefix('Kesehatan')->name('kesehatan.')->group(function () {

    Route::get('/rumah-sakit', [KesehatanController::class, 'rumahSakit'])
        ->name('rumah-sakit');

    Route::get('/penyakit', [KesehatanController::class, 'penyakit'])
        ->name('penyakit');

    Route::get('/tenaga-medis', [KesehatanController::class, 'tenagaMedis'])
        ->name('tenaga-medis');

    Route::get('/ketersediaan-fasilitas', [KesehatanController::class, 'ketersediaanFasilitas'])
        ->name('ketersediaan-fasilitas');

    Route::get('/rumah-sakit/{rs}/data', [KesehatanController::class, 'dataRs'])
        ->name('data-rs');

});