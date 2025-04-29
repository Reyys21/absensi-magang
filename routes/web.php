<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;

// Rute yang membutuhkan autentikasi
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absensi (Check-in dan Check-out)
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/checkin', 'checkinForm')->name('checkin.form');
        Route::post('/checkin', 'storeCheckin')->name('checkin.store');
        Route::get('/checkout', 'checkoutForm')->name('checkout.form');
        Route::post('/checkout', 'storeCheckout')->name('checkout.store');
    });

    // Helpdesk
    Route::view('/helpdesk', 'helpdesk')->name('helpdesk');
});

// Rute autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');