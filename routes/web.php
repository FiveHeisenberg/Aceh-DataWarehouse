<?php

use App\Http\Controllers\Kesehatan\KesehatanController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'login')->name('login');
Route::view('/signin', 'signin')->name('signin');

Route::get('/kesehatan/puskesmas', [KesehatanController::class, 'index']);