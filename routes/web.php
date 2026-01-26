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
use App\Http\Controllers\ReportController;

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
        Route::get('sales/print/{id}', [SalesController::class, 'printInvoice'])->name('sales.print');
        Route::get('sales-receipt',[SalesController::class,'receipt'])->name('sales.receipt');
        Route::get('sales-receipt/print/{id}', [SalesController::class, 'printReceipt'])->name('sales.receipt.print');
        
        Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase');
        Route::get('purchase/print/{id}', [PurchaseController::class, 'printInvoice'])->name('purchase.print');
        Route::get('purchase-receipt',[PurchaseController::class,'receipt'])->name('purchase.receipt');
        Route::get('purchase-receipt/print/{id}', [PurchaseController::class, 'printReceipt'])->name('purchase.receipt.print');
        Route::get('mitra', [MitraController::class, 'index'])->name('mitra');
        Route::get('barang', [BarangController::class, 'index'])->name('barang');
        Route::get('stok', [StockController::class, 'index'])->name('stok');
        Route::get('users', fn() => view('Users.index'))->name('users.index');

        // Reports
        Route::get('report/sales', [ReportController::class, 'salesReport'])->name('report.sales');
        Route::get('report/purchase', [ReportController::class, 'purchaseReport'])->name('report.purchase');
        Route::get('report/stock', [ReportController::class, 'stockReport'])->name('report.stock');
        Route::get('report/stock/export', [ReportController::class, 'stockReportExport'])->name('report.stock.export');
        Route::get('report/ar-aging', [ReportController::class, 'arAging'])->name('report.ar-aging');
        Route::get('report/general-ledger', [ReportController::class, 'generalLedger'])->name('report.general-ledger');
        Route::get('report/balance-sheet', [ReportController::class, 'balanceSheet'])->name('report.balance-sheet');
        Route::get('report/profit-loss', [ReportController::class, 'profitAndLoss'])->name('report.profit-loss');

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