<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;

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
        // User
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::get('/user/create', [UserController::class, 'create'])->name('userCreate');
        Route::post('/user/store', [UserController::class, 'store'])->name('userStore');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('userEdit');
        Route::match(['PUT', 'PATCH'], '/user/update/{id}', [UserController::class, 'update'])->name('userUpdate');
        Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('userDestroy');
        Route::get('/user/excel', [UserController::class, 'excel'])->name('userExcel');
        Route::get('/user/pdf', [UserController::class, 'pdf'])->name('userPdf');
        
        // Admin (Manajemen Admin - staff & dosen)
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::get('/admin/create', [AdminController::class, 'create'])->name('adminCreate');
        Route::post('/admin/store', [AdminController::class, 'store'])->name('adminStore');
        Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('adminEdit');
        Route::match(['PUT', 'PATCH'], '/admin/manage/{id}', [AdminController::class, 'manage'])->name('adminUpdate');
        Route::delete('/admin/destroy/{id}', [AdminController::class, 'destroy'])->name('adminDestroy');
        Route::get('/admin/excel', [AdminController::class, 'excel'])->name('adminExcel');
        Route::get('/admin/pdf', [AdminController::class, 'pdf'])->name('adminPdf');
        
        // Room
        Route::get('/room', [RoomController::class, 'index'])->name('room');
        Route::get('/room/create', [RoomController::class, 'create'])->name('roomCreate');
        Route::post('/room/store', [RoomController::class, 'store'])->name('roomStore');
        Route::get('/room/edit/{id}', [RoomController::class, 'edit'])->name('roomEdit');
        Route::match(['PUT', 'PATCH'], '/room/update/{id}', [RoomController::class, 'update'])->name('roomUpdate');
        Route::delete('/room/destroy/{id}', [RoomController::class, 'destroy'])->name('roomDestroy');
    
       
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

    // Daftar ruangan untuk user
    Route::get('/ruangan-user', [RoomController::class, 'userList'])->name('room.user.list');
});