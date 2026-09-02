<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Penduduk\PendudukApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Penduduk Module
|--------------------------------------------------------------------------
|
| Base URL: /api/penduduk
|
*/

Route::prefix('penduduk')->group(function () {
    
    /**
     * GET /api/penduduk/years
     * Ambil daftar tahun yang tersedia di database
     */
    Route::get('/years', [PendudukApiController::class, 'getYears'])
        ->name('penduduk.api.years');

    /**
     * GET /api/penduduk/index
     * Ambil summary + detail data penduduk
     * Query params: ?tahun=2023&search=banda&per_page=25
     */
    Route::get('/index', [PendudukApiController::class, 'getIndex'])
        ->name('penduduk.api.index');

    /**
     * GET /api/penduduk/detail/{kode_kabupaten}
     * Ambil detail data per kabupaten/kota
     */
    Route::get('/detail/{kode_kabupaten}', [PendudukApiController::class, 'getDetail'])
        ->name('penduduk.api.detail');

    /**
     * GET /api/penduduk/tren
     * Ambil data tren untuk chart
     * Query params: ?kode_kab=1101&tahun_mulai=2020&tahun_akhir=2023
     */
    Route::get('/tren', [PendudukApiController::class, 'getTren'])
        ->name('penduduk.api.tren');

    // Tambahkan ini di dalam group Route::prefix('penduduk')
    // MAP MAP MAP
    Route::get('/map', [PendudukApiController::class, 'getMapData'])
    ->name('penduduk.api.map');

});