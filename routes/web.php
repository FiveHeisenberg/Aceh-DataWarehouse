<?php

use App\Http\Controllers\Kesehatan\KesehatanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/kesehatan/puskesmas', [KesehatanController::class, 'index']);