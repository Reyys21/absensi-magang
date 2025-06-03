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
    // DashboardController mungkin tidak menggunakan struktur 'fold_dashboard', jadi biarkan terpisah jika DashboardController beda
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semua Rute terkait Absensi, Koreksi, dan Approval Requests dikelompokkan di sini
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/checkin', 'checkinForm')->name('checkin.form');
        Route::post('/checkin', 'storeCheckin')->name('checkin.store');

        Route::get('/checkout', 'checkoutForm')->name('checkout.form');
        Route::post('/checkout', 'storeCheckout')->name('checkout.store');

        // My Attendance
        Route::get('/my-attendance', 'myAttendance')->name('attendance.my');

        // History Attendance
        Route::get('/attendance-history', 'history')->name('attendance.history');

        // Correction Form
        Route::get('/correction-form', 'showCorrectionForm')->name('correction.form');
        Route::post('/correction-request', 'storeCorrectionRequest')->name('correction.store');

        // Approval Requests for User
        Route::get('/approval-requests', 'showApprovalRequests')->name('approval.requests');

        // Export, Create, Store (Jika ini juga di AttendanceController)
        Route::get('/attendance/export', 'export')->name('attendance.export');
        Route::get('/attendance/create', 'create')->name('attendance.create');
        Route::post('/attendance/store', 'store')->name('attendance.store');
    });

    // Rute-rute terkait Profil (menggunakan prefix dan name group)
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/update-information', 'updateProfileInformation')->name('update-information');
        Route::get('/change-password', 'showChangePasswordForm')->name('change-password');
        Route::patch('/update-password', 'updatePassword')->name('update-password');
        Route::post('/update-photo', 'updateProfilePhoto')->name('update-photo');
        Route::post('/delete-photo', 'deleteProfilePhoto')->name('delete-photo');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Jika Anda memiliki halaman landing page tanpa otentikasi
Route::get('/', function () {
    return view('welcome');
});