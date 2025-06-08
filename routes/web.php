<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

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

    // --- GRUP RUTE UNTUK USER BIASA (MAHASISWA/SISWA) ---
    Route::middleware('can:access-user-pages')->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Rute terkait Absensi
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

        // Rute untuk user biasa melihat status approval mereka
        // Menggunakan method yang sama tapi karena dilindungi Gate, hanya data mereka yang tampil
        Route::get('/my-approval-requests', [AttendanceController::class, 'showApprovalRequests'])->name('user.approval.requests');
    });

    // --- GRUP RUTE UNTUK ADMIN (BISA DIAKSES OLEH ADMIN & SUPERADMIN) ---
    Route::middleware('can:access-admin-pages')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // Rute Approval Khusus untuk Admin (menampilkan semua request)
        Route::get('/approval-requests', [AttendanceController::class, 'showApprovalRequests'])->name('approval.requests');
        // Rute manajemen & monitoring lainnya
    });

    // --- GRUP RUTE KHUSUS SUPERADMIN ---
    Route::middleware('can:access-superadmin-pages')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'superadminDashboard'])->name('dashboard');
        // Rute khusus superadmin lainnya
    });

    // Rute profil ada di luar grup spesifik agar bisa diakses semua role
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/update-information', 'updateProfileInformation')->name('update-information');
        // ... rute profil lainnya
    });
});