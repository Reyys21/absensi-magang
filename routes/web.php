<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomepageController; // <--- TAMBAHKAN IMPORT INI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute untuk Homepage (halaman pertama yang dilihat semua orang)
Route::get('/', [HomepageController::class, 'index'])->name('home'); // <--- UBAH INI

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

    // Semua Rute terkait Absensi, Koreksi, dan Approval Requests
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/checkin', 'checkinForm')->name('checkin.form');
        Route::post('/checkin', 'storeCheckin')->name('checkin.store');

        Route::get('/checkout', 'checkoutForm')->name('checkout.form');
        Route::post('/checkout', 'storeCheckout')->name('checkout.store');

        Route::get('/my-attendance', 'myAttendance')->name('attendance.my');
        Route::get('/attendance-history', 'history')->name('attendance.history');

        Route::get('/correction-form', 'showCorrectionForm')->name('correction.form');
        Route::post('/correction-request', 'storeCorrectionRequest')->name('correction.store');

        Route::get('/approval-requests', 'showApprovalRequests')->name('approval.requests');

        Route::get('/attendance/export', 'export')->name('attendance.export');
        Route::get('/attendance/create', 'create')->name('attendance.create'); // Jika ada
        Route::post('/attendance/store', 'store')->name('attendance.store'); // Jika ada
    });

    // Rute-rute terkait Profil
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

// Hapus rute welcome lama jika sudah digantikan oleh homepage
// Route::get('/welcome-test', function () { // Anda bisa menamainya lain jika masih butuh view welcome
//     return view('welcome');
// })->name('welcome.page');