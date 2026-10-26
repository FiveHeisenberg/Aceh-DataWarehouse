<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EkonomiPangan\EkonomiPanganController;

Route::prefix('Ekonomi-Pangan')->name('ekonomi-pangan.')->group(function () {
    Route::get('/pertanian', [EkonomiPanganController::class, 'pertanian'])->name('pertanian');
    Route::get('/peternakan', [EkonomiPanganController::class, 'peternakan'])->name('peternakan');
    Route::get('/pangan', [EkonomiPanganController::class, 'pangan'])->name('pangan');
    Route::get('/perikanan', [EkonomiPanganController::class, 'perikanan'])->name('perikanan');
});