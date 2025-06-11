<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RiwayatController;

// Redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi
Auth::routes();

// Logout route
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Setelah login, route home
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ============================
// ROUTE UNTUK USER BIASA
// ============================
Route::middleware('auth')->group(function () {
    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Riwayat Attendance Routes
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Izin Routes
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
});

// ============================
// ROUTE UNTUK ADMIN
// ============================
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Attendance Admin Routes
        Route::get('/attendances', [AttendanceController::class, 'adminIndex'])->name('attendance.index');
        Route::get('/attendance/export-pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf');
        Route::get('/attendance/export-excel', [AttendanceController::class, 'exportExcel'])->name('attendance.export-excel');

        // Izin Admin Routes
        Route::get('/izin', [IzinController::class, 'adminIndex'])->name('izin.index');
        Route::get('/attendance/data', [AttendanceController::class, 'getData'])->name('attendance.data');
        Route::get('/izin/data', [IzinController::class, 'getData'])->name('izin.data');
        Route::post('/izin/approve/{id}', [IzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/reject/{id}', [IzinController::class, 'reject'])->name('izin.reject');
        Route::get('/izin/{id}', [IzinController::class, 'show'])->name('izin.show');
        Route::get('/izin/{id}/download', [IzinController::class, 'download'])->name('izin.download');
    });
