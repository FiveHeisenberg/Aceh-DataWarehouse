<?php

require __DIR__.'/dispenda/web.php';

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Kesehatan\KesehatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManageUserController;

Route::view('/', 'login')->name('login');

Route::view('/signin', 'signin')->name('signin');
Route::post('/signin', [RegisteredUserController::class, 'store']);

Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// UNTUK VIEW INDEX PAGE
Route::view('/index', 'index')->name('index');

// UNTUK FITUR PROFILE DAN MANAJEMEN USER
Route::middleware('auth')->group(function () {
    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // MANAGE USER
    Route::get('/manage-user', [ManageUserController::class, 'index'])->name('manage-user');
    Route::get('/manage-user/{user}/edit', [ManageUserController::class, 'edit'])->name('manage-user.edit');
    Route::put('/manage-user/{user}', [ManageUserController::class, 'update'])->name('manage-user.update');
    Route::delete('/manage-user/{user}', [ManageUserController::class, 'destroy'])->name('manage-user.destroy');
    Route::post('/manage-user', [ManageUserController::class, 'store'])->name('manage-user.store');
    Route::get('/manage-user/export', [ManageUserController::class, 'export'])->name('manage-user.export');
});
