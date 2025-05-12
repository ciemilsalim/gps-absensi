<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {

    Route::get('/attendance', function () {
        return view('attendance.index');
    })->name('attendance.index');
    
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])
         ->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])
         ->name('attendance.checkout');
});
