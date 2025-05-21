<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;

// Routes untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Routes untuk user yang sudah login
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absensi Check-in dan Check-out dengan controller grouping
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/checkin', 'checkinForm')->name('checkin.form');
        Route::post('/checkin', 'storeCheckin')->name('checkin.store');

        Route::get('/checkout', 'checkoutForm')->name('checkout.form');
        Route::post('/checkout', 'storeCheckout')->name('checkout.store');
    });

    // Attendance lainnya
    Route::get('/attendance/my', [AttendanceController::class, 'myAttendance'])->name('attendance.my');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
}); 