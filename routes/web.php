<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/dispenda/web.php';

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::view('/', 'login')->name('login');

Route::view('/signin', 'signin')->name('signin');
Route::post('/signin', [RegisteredUserController::class, 'store']);

Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

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
