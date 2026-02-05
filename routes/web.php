<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

// Halaman publik list peminjaman
Route::get('/list-peminjaman', [BookingController::class, 'publicList'])->name('public.list');

// Halaman publik daftar ruangan
Route::get('/ruangan', [HomeController::class, 'ruangan'])->name('public.ruangan');

Route::middleware('isLogin')->group(function(){
    // Login
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
});

// Register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerProses'])->name('registerProses');

// Forgot Password (tanpa middleware khusus)
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('passwordRequest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('passwordEmail');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('passwordUpdate');

// logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('checkLogin')->group(function(){
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    
    Route::middleware('isAdmin')->group(function(){

        // Manajemen Pengguna (All in One)
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/data', [UserManagementController::class, 'getData'])->name('users.data');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/excel', [UserManagementController::class, 'excel'])->name('users.excel');
        Route::get('/users/pdf', [UserManagementController::class, 'pdf'])->name('users.pdf');
        
        // Room
        Route::get('/room', [RoomController::class, 'index'])->name('room');
        Route::get('/room/create', [RoomController::class, 'create'])->name('roomCreate');
        Route::post('/room/store', [RoomController::class, 'store'])->name('roomStore');
        Route::get('/room/edit/{id}', [RoomController::class, 'edit'])->name('roomEdit');
        Route::match(['PUT', 'PATCH'], '/room/update/{id}', [RoomController::class, 'update'])->name('roomUpdate');
        Route::delete('/room/destroy/{id}', [RoomController::class, 'destroy'])->name('roomDestroy');
        Route::delete('/rooms/{id}/delete-image', [RoomController::class, 'deleteImage'])->name('rooms.delete-image');
    
        // Statistik peminjaman
        Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
        // API Statistik
        Route::get('/api/statistics/booking-per-month', [DashboardController::class, 'bookingPerMonth'])
            ->name('api.statistics.booking.per.month');
        Route::get('/api/statistics/booking-per-day', [DashboardController::class, 'bookingPerDay'])
            ->name('api.statistics.booking.per.day');
        // API Statistik Top Ruangan
        Route::get('/api/statistics/top-rooms', [DashboardController::class, 'topRooms'])
            ->name('api.statistics.top.rooms');
        // API Statistik Waktu
        Route::get('/api/statistics/time-analysis', [DashboardController::class, 'timeAnalysis'])
            ->name('api.statistics.time.analysis');
        // Export PDF Lengkap
        Route::get('/statistics/export-full', [DashboardController::class, 'exportFullPdf'])->name('statistics.export.full');
    });

    // Booking
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::get('/booking/create', [BookingController::class, 'create'])->name('bookingCreate');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookingStore');
    // Untuk admin: approve / reject booking
    Route::patch('/booking/approve/{id}', [BookingController::class, 'approve'])->name('bookingApprove');
    Route::patch('/booking/reject/{id}', [BookingController::class, 'reject'])->name('bookingReject');
    // Opsional: hapus booking (kalau perlu)
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancel'])->name('booking.cancel');
    // History booking
    Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/booking/history/data', [BookingController::class, 'historyData'])->name('booking.history.data');
    // Perpanjangan booking
    Route::get('/booking/extend/{id}', [BookingController::class, 'extendForm'])->name('booking.extend');
    // Upload bukti pembayaran
    Route::get('/booking/{id}/upload-proof', [BookingController::class, 'showUploadProof'])->name('booking.upload.proof.show');
    Route::post('/booking/{id}/upload-proof', [BookingController::class, 'uploadProof'])->name('booking.upload.proof');

    // Daftar ruangan untuk user
    Route::get('/ruangan-user', [RoomController::class, 'userList'])->name('room.user.list');
});