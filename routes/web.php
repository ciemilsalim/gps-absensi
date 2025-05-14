<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('/auth/login');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {

    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])
         ->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])
         ->name('attendance.checkout');
});

Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/admin/attendances', [AttendanceController::class, 'adminIndex'])->name('admin.attendance.index');
    Route::get('/admin/attendance/export-pdf', [AttendanceController::class, 'exportPdf'])->name('admin.attendance.export-pdf');
    Route::get('/admin/attendance/export-excel', [AttendanceController::class, 'exportExcel'])->name('admin.attendance.export-excel');
});
