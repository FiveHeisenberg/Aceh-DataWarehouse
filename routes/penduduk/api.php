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
     * GET /api/penduduk/tahun
     * Daftar tahun yang tersedia (untuk filter tahun)
     */
    Route::get('/tahun', [PendudukApiController::class, 'getTahun'])
        ->name('penduduk.api.tahun');

    /**
     * GET /api/penduduk/jumlah-penduduk
     * Jumlah penduduk (COUNT) per tahun
     */
    Route::get('/jumlah-penduduk', [PendudukApiController::class, 'getJumlahPenduduk'])
        ->name('penduduk.api.jumlah-penduduk');

    /**
     * GET /api/penduduk/map-jumlah-penduduk
     * Data per kabupaten untuk peta choropleth
     */
    Route::get('/map-jumlah-penduduk', [PendudukApiController::class, 'getMapData'])
        ->name('penduduk.api.map-jumlah-penduduk');

    /**
     * GET /api/penduduk/trend-pertumbuhan
     * List kabupaten (tanpa param) atau tren per tahun (dengan ?wilayah=)
     */
    Route::get('/trend-pertumbuhan', [PendudukApiController::class, 'getTrendPertumbuhan'])
        ->name('penduduk.api.trend-pertumbuhan');

    Route::get('/detail-penduduk', [PendudukApiController::class, 'getDetailPenduduk'])
        ->name('penduduk.api.detail-penduduk');

    /**
     * GET /api/penduduk/struktur-umur
     * Struktur kelompok umur berdasarkan range umur
     */
    Route::get('/struktur-umur', [PendudukApiController::class, 'getStrukturUmur'])
        ->name('penduduk.api.struktur-umur');
});