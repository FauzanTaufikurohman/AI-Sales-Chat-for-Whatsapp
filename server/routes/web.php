<?php

use App\Http\Controllers\Blade\AuthController;
use App\Http\Controllers\Blade\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [
        AuthController::class,
        'registerForm',
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register',
    ])->name('register.store');

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [
        AuthController::class,
        'loginForm',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.store');

    /*
    |--------------------------------------------------------------------------
    | OTP
    |--------------------------------------------------------------------------
    */

    Route::get('/verify-otp', [
        AuthController::class,
        'otpForm',
    ])->name('otp.form');

    Route::post('/verify-otp', [
        AuthController::class,
        'verifyOtp',
    ])->name('otp.verify');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    Route::get('/dashboard/conversations/{conversation}', [
        DashboardController::class,
        'conversation',
    ])->name('dashboard.conversation');

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');
});
