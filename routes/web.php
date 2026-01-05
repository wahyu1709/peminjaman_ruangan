<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

// Halaman publik list peminjaman
Route::get('/list-peminjaman', [BookingController::class, 'publicList'])->name('public.list');

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
        Route::match(['PUT', 'PATCH'], '/admin/update/{id}', [AdminController::class, 'update'])->name('adminUpdate');
        Route::delete('/admin/destroy/{id}', [AdminController::class, 'destroy'])->name('adminDestroy');
        
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
        Route::delete('/booking/{id}', [BookingController::class, 'destroy'])->name('bookingDestroy');
        Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');
        Route::get('/booking/history/data', [BookingController::class, 'historyData'])->name('booking.history.data');
});

