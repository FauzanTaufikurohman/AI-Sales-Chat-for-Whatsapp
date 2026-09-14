<?php

use App\Http\Controllers\Blade\AuthController;
use App\Http\Controllers\Blade\DashboardController;
use App\Http\Controllers\WhatsappBotController;
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

    Route::get('/verify-email', [
        AuthController::class,
        'verificationNotice',
    ])->name('verification.notice');

    Route::post('/verify-email/resend', [
        AuthController::class,
        'resendVerification',
    ])->name('verification.resend');

    Route::get('/verify-email/{id}/{hash}', [
        AuthController::class,
        'verifyEmail',
    ])->middleware('signed')->name('verification.verify');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/whatsapp/connect', [WhatsappBotController::class, 'connect'])
        ->name('whatsapp.connect');

    Route::get('/whatsapp/qr', [WhatsappBotController::class, 'qr'])
        ->name('whatsapp.qr');

    Route::get('/whatsapp/status', [WhatsappBotController::class, 'status'])
        ->name('whatsapp.status');

    Route::post('/whatsapp/activate', [WhatsappBotController::class, 'activate'])
        ->name('whatsapp.activate');

    Route::get('/dashboard/conversations/{conversation}', [
        DashboardController::class,
        'conversation',
    ])->name('dashboard.conversation');

    Route::post('/dashboard/conversations/{conversation}/reply', [
        DashboardController::class,
        'reply',
    ])->name('dashboard.conversation.reply');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
