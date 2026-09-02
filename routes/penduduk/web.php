<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Penduduk\PendudukController;

Route::prefix('penduduk')->name('penduduk.')->group(function () {
    Route::get('/', [PendudukController::class, 'index'])->name('index');
    Route::get('/jumlah', [PendudukController::class, 'jumlah'])->name('jumlah');
    Route::get('/kepadatan', [PendudukController::class, 'kepadatan'])->name('kepadatan');
    Route::get('/pertumbuhan', [PendudukController::class, 'pertumbuhan'])->name('pertumbuhan');
});