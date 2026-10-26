<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Guest ត្រូវបានបញ្ជូនទៅ My Bookings ក្នុង Controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bookings: គ្រប់តួនាទី
    Route::resource('bookings', BookingController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::resource('rooms', RoomController::class)->except(['show', 'destroy']);

        // Mark as clean
        Route::patch('/rooms/{room}/clean', [RoomController::class, 'markClean'])->name('rooms.clean');
    });

    // លុបបន្ទប់: Admin តែប៉ុណ្ណោះ
    Route::middleware('role:admin')->group(function () {
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // Guests: Admin + Receptionist
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::resource('guests', GuestController::class)->except(['destroy']);
    });

    // លុបភ្ញៀវ: Admin តែប៉ុណ្ណោះ
    Route::middleware('role:admin')->group(function () {
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
    });

    // User Management: Admin ប៉ុណ្ណោះ
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Invoice: គ្រប់តួនាទី (Guest មើលបានតែរបស់ខ្លួន ត្រួតពិនិត្យក្នុង Controller)
    Route::get('/bookings/{booking}/invoice', [PaymentController::class, 'invoice'])->name('bookings.invoice');

    // Payments: Admin + Receptionist
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/bookings/{booking}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/bookings/{booking}/payments', [PaymentController::class, 'store'])->name('payments.store');
    });

    // លុបការទូទាត់: Admin ប៉ុណ្ណោះ
    Route::middleware('role:admin')->group(function () {
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});

require __DIR__.'/auth.php';