<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ProfileController;

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
    // The URL path should ideally be just '/dashboard' and the view returned will be 'fold_dashboard.dashboard'
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absensi Check-in dan Check-out dengan controller grouping
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/checkin', 'checkinForm')->name('checkin.form');
        Route::post('/checkin', 'storeCheckin')->name('checkin.store');

        Route::get('/checkout', 'checkoutForm')->name('checkout.form');
        Route::post('/checkout', 'storeCheckout')->name('checkout.store');

        // Correction Form
        Route::get('/correction-form', 'showCorrectionForm')->name('correction.form');
        Route::post('/correction-request', 'storeCorrectionRequest')->name('correction.store');

        // My Attendance (moved here for consistency with AttendanceController grouping)
        Route::get('/my-attendance', 'myAttendance')->name('attendance.my');

        // History Attendance (moved here for consistency with AttendanceController grouping)
        Route::get('/attendance-history', 'history')->name('attendance.history');
    });


    // Rute-rute terkait Profil
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        // Rute untuk menampilkan form edit informasi umum
        Route::get('/edit', 'edit')->name('edit');
        // Rute untuk proses update informasi umum
        Route::patch('/update-information', 'updateProfileInformation')->name('update-information');

        // Rute untuk menampilkan form ubah password
        Route::get('/change-password', 'showChangePasswordForm')->name('change-password');
        // Rute untuk proses update password
        Route::patch('/update-password', 'updatePassword')->name('update-password');

        // Rute untuk update foto profil
        Route::post('/update-photo', 'updateProfilePhoto')->name('update-photo');
        // Rute untuk hapus foto profil
        Route::post('/delete-photo', 'deleteProfilePhoto')->name('delete-photo');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Routes that don't directly map to the new "fold_" structure but still use existing controllers
    // You might want to reconsider if these are still needed or if they should be integrated
    // into the 'AttendanceController' group or a new controller if they represent a new feature.
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
});