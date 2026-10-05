<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Kesehatan\KesehatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManageUserController;

Route::view('/', 'login')->name('login');

// GET /signin tetap view pendaftaran, POST /signin memproses pendaftaran.
Route::view('/signin', 'signin')->name('signin');
Route::post('/signin', [RegisteredUserController::class, 'store']);

Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// UNTUK VIEW INDEX PAGE
Route::view('/index', 'index')->name('index');

// UNTUK FITUR PROFILE DAN MANAJEMEN USER
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/manage-user', [ManageUserController::class, 'index'])->name('manage-user');
    Route::get('/manage-user/{user}/edit', [ManageUserController::class, 'edit'])->name('manage-user.edit');
    Route::put('/manage-user/{user}', [ManageUserController::class, 'update'])->name('manage-user.update');
});