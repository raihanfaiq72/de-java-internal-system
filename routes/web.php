<?php

use App\Http\Controllers\MitraController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesController;

// auth
Route::get('/',[AuthController::class,'login']);

// select outlet
Route::get('/select-your-outlet',[AuthController::class,'syo']);

// route per outlet

Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');
Route::get('sales',[SalesController::class,'index'])->name('sales');
Route::get('mitra',[MitraController::class,'index'])->name('mitra');

Route::get('users', function () {
    return view('Users.index');
})->name('users.index');