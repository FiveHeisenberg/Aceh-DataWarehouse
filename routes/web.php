<?php

require __DIR__.'/penduduk/web.php';
require __DIR__.'/dispenda/web.php';

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

use App\Http\Controllers\Kesehatan\KesehatanController;

Route::get('/kesehatan/puskesmas', [KesehatanController::class, 'index']);
