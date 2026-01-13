<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPiutangController;
use App\Http\Controllers\DashboardSalesController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\UserPlotController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login-proses', [AuthController::class, 'loginProses'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/select-your-outlet', [AuthController::class, 'syo'])->name('syo');
    Route::post('/set-active-outlet', [AuthController::class, 'setOutlet'])->name('set.outlet');

    Route::middleware(['module.access'])->group(function () {
        
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard-piutang', [DashboardPiutangController::class, 'index'])->name('dashboard.piutang');
        Route::get('dashboard-sales', [DashboardSalesController::class, 'index'])->name('dashboard.sales');
        Route::get('dashboard-sales/detail/{id}', [DashboardSalesController::class, 'detail'])->name('dashboard.sales.detail');
        
        Route::get('sales', [SalesController::class, 'index'])->name('sales');
        Route::get('sales-receipt',[SalesController::class,'receipt'])->name('sales.receipt');
        Route::get('purchase', [purchaseController::class, 'index'])->name('purchase');
        Route::get('purchase-receipt',[PurchaseController::class,'receipt'])->name('purchase.receipt');
        Route::get('mitra', [MitraController::class, 'index'])->name('mitra');
        Route::get('barang', [BarangController::class, 'index'])->name('barang');
        Route::get('stok', [StockController::class, 'index'])->name('stok');
        Route::get('users', fn() => view('Users.index'))->name('users.index');

        Route::prefix('admin')->group(function () {
            Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::resource('roles', RoleController::class);
            
            // Offices
            Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');
            Route::post('/offices', [OfficeController::class, 'store'])->name('offices.store');
            Route::get('/offices/{id}', [OfficeController::class, 'show'])->name('offices.show');
            Route::put('/offices/{id}', [OfficeController::class, 'update'])->name('offices.update');
            Route::delete('/offices/{id}', [OfficeController::class, 'destroy'])->name('offices.destroy');
            
            // User Plots
            Route::get('/user-plots', [UserPlotController::class, 'index'])->name('user_plots.index');
            Route::post('/user-plots', [UserPlotController::class, 'store'])->name('user_plots.store');
            Route::get('/user-plots/{id}', [UserPlotController::class, 'show'])->name('user_plots.show');
            Route::put('/user-plots/{id}', [UserPlotController::class, 'update'])->name('user_plots.update');
            Route::delete('/user-plots/{id}', [UserPlotController::class, 'destroy'])->name('user_plots.destroy');
        });
    });
});