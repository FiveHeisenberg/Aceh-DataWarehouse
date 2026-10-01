<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Kesehatan\KesehatanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::view('/', 'login')->name('login');

// GET /signin tetap view pendaftaran, POST /signin memproses pendaftaran.
Route::view('/signin', 'signin')->name('signin');
Route::post('/signin', [RegisteredUserController::class, 'store']);

Route::view('/index', 'index')->name('index');

Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// VIEW PROFILE PAGE
Route::view('/profile', 'profile')->name('profile');

Route::get('/kesehatan/puskesmas', [KesehatanController::class, 'index']);