<?php

use App\Http\Controllers\Dispenda\DispendaApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Dispenda Module
|--------------------------------------------------------------------------
|
| Base URL: /api/dispenda
|
*/

Route::prefix('dispenda')->group(function () {

    /**
     * GET /api/dispenda/years
     * Ambil daftar tahun yang tersedia di database
     */
    Route::get('/years', [DispendaApiController::class, 'getYears'])
        ->name('dispenda.api.years');

    /**
     * GET /api/dispenda/index
     * Ambil summary + detail data wajib pajak
     * Query params: ?tahun=2023&search=banda&per_page=25
     */
    Route::get('/index', [DispendaApiController::class, 'getIndex'])
        ->name('dispenda.api.index');

    /**
     * GET /api/dispenda/detail/{kode_kabupaten}
     * Ambil detail data per kabupaten/kota
     */
    Route::get('/detail/{kode_kabupaten}', [DispendaApiController::class, 'getDetail'])
        ->name('dispenda.api.detail');

    /**
     * GET /api/dispenda/tren
     * Ambil data tren untuk chart
     * Query params: ?kode_kab=1101&tahun_mulai=2020&tahun_akhir=2023
     */
    Route::get('/tren', [DispendaApiController::class, 'getTren'])
        ->name('dispenda.api.tren');
});
