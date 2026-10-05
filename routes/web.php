<?php

require __DIR__.'/penduduk/web.php';
require __DIR__.'/dispenda/web.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Kesehatan\KesehatanController;
use App\Http\Controllers\ProfileController;

Route::view('/', 'login')->name('login');

Route::view('/signin', 'signin')->name('signin');
Route::post('/signin', [RegisteredUserController::class, 'store']);

Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::view('/index', 'index')->name('index');

Route::get('/kesehatan/puskesmas', [KesehatanController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
