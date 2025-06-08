<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController; // <-- PASTIKAN USE STATEMENT INI ADA DI ATAS

// Rute Homepage publik
Route::get('/', [HomepageController::class, 'index'])->name('home');

// Rute untuk Guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Rute untuk user yang sudah login
Route::middleware('auth')->group(function () {
    // Logout (bisa diakses semua role)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP RUTE UNTUK USER BIASA ---
    Route::middleware('can:access-user-pages')->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::controller(AttendanceController::class)->group(function () {
            Route::get('/checkin', 'checkinForm')->name('checkin.form');
            Route::post('/checkin', 'storeCheckin')->name('checkin.store');
            Route::get('/checkout', 'checkoutForm')->name('checkout.form');
            Route::post('/checkout', 'storeCheckout')->name('checkout.store');
            Route::get('/my-attendance', 'myAttendance')->name('attendance.my');
            Route::get('/attendance-history', 'history')->name('attendance.history');
            Route::get('/correction-form', 'showCorrectionForm')->name('correction.form');
            Route::post('/correction-request', 'storeCorrectionRequest')->name('correction.store');
        });
        
        Route::get('/my-approval-requests', [AttendanceController::class, 'showApprovalRequests'])->name('user.approval.requests');
    });

    // --- GRUP RUTE UNTUK ADMIN (BISA DIAKSES OLEH ADMIN & SUPERADMIN) ---
    Route::middleware('can:access-admin-pages')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('dashboard');
        
        Route::get('/approval-requests', [AttendanceController::class, 'showApprovalRequests'])->name('approval.requests');
        Route::post('/approval-requests/{correctionRequest}/approve', [AttendanceController::class, 'approveCorrection'])->name('approval.approve');
        Route::post('/approval-requests/{correctionRequest}/reject', [AttendanceController::class, 'rejectCorrection'])->name('approval.reject');

        // ===================================================================
        // === TAMBAHKAN BLOK KODE BARU DI SINI ===
        // ===================================================================
        // Rute untuk Fitur MONITORING User
        Route::get('/monitoring/users', [UserController::class, 'indexMonitoring'])->name('monitoring.users.index');
        Route::get('/monitoring/users/{user}', [UserController::class, 'showMonitoring'])->name('monitoring.users.show');

        // Rute untuk Fitur MANAJEMEN Akun
        Route::get('/manajemen/akun', [UserController::class, 'indexManagement'])->name('management.accounts.index');
        // ===================================================================
        // === AKHIR DARI BLOK KODE BARU ===
        // ===================================================================
    });

    // --- GRUP RUTE KHUSUS SUPERADMIN ---
    Route::middleware('can:access-superadmin-pages')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'superadminDashboard'])->name('dashboard');
    });

    // --- Rute profil yang sudah dilengkapi ---
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        // Rute yang sudah ada
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/update-information', 'updateProfileInformation')->name('update-information');
        
        // --- RUTE BARU UNTUK PROFIL ---
        Route::post('/update-photo', 'updateProfilePhoto')->name('update-photo');
        Route::post('/delete-photo', 'deleteProfilePhoto')->name('delete-photo');
        Route::get('/change-password', 'showChangePasswordForm')->name('change-password'); // <-- Rute yang hilang
        Route::patch('/update-password', 'updatePassword')->name('update-password');
    });
});