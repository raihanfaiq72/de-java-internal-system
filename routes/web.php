<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPiutangController;
use App\Http\Controllers\DashboardSalesController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserPlotController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::post('/login-proses', [AuthController::class, 'loginProses'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/select-your-outlet', [AuthController::class, 'syo'])->name('syo');
    Route::post('/set-active-outlet', [AuthController::class, 'setOutlet'])->name('set.outlet');
    Route::delete('/delete-outlet/{id}', [AuthController::class, 'destroyOutlet'])->name('syo.destroy');

    // User Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    Route::middleware(['module.access'])->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard-piutang', [DashboardPiutangController::class, 'index'])->name('dashboard.piutang');
        Route::get('dashboard-sales', [DashboardSalesController::class, 'index'])->name('dashboard.sales');
        Route::get('dashboard-sales/detail/{id}', [DashboardSalesController::class, 'detail'])->name('dashboard.sales.detail');

        Route::get('sales', [SalesController::class, 'index'])->name('sales');
        Route::get('sales/export', [SalesController::class, 'export'])->name('sales.export');
        Route::get('sales/{id}', [SalesController::class, 'show'])->name('sales.show');
        Route::get('sales/print/{id}', [SalesController::class, 'printInvoice'])->name('sales.print');
        Route::get('sales-receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
        Route::get('sales-receipt/{id}', [SalesController::class, 'showReceipt'])->name('sales.receipt.show');
        Route::get('sales-receipt/print/{id}', [SalesController::class, 'printReceipt'])->name('sales.receipt.print');

        Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase');
        Route::get('purchase/export', [PurchaseController::class, 'export'])->name('purchase.export');
        Route::get('purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
        Route::get('purchase/print/{id}', [PurchaseController::class, 'printInvoice'])->name('purchase.print');
        Route::get('purchase-receipt', [PurchaseController::class, 'receipt'])->name('purchase.receipt');
        Route::get('purchase-receipt/{id}', [PurchaseController::class, 'showReceipt'])->name('purchase.receipt.show');
        Route::get('purchase-receipt/print/{id}', [PurchaseController::class, 'printReceipt'])->name('purchase.receipt.print');

        Route::get('mitra/export', [MitraController::class, 'export'])->name('mitra.export');
        Route::get('import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('import/stock', [App\Http\Controllers\ImportController::class, 'importStock'])->name('import.stock');
        Route::post('import/mitra', [App\Http\Controllers\ImportController::class, 'importMitra'])->name('import.mitra');
        Route::get('import/template/{type}', [App\Http\Controllers\ImportController::class, 'downloadTemplate'])->name('import.template');
        Route::get('mitra', [MitraController::class, 'index'])->name('mitra');
        Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
        Route::get('barang', [BarangController::class, 'index'])->name('barang');
        Route::get('stok', [StockController::class, 'index'])->name('stok');
        Route::get('stok/export', [StockController::class, 'export'])->name('stok.export');
        Route::get('stok/print', [StockController::class, 'print'])->name('stok.print');
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('fleets', [FleetController::class, 'index'])->name('fleets.index');

        // Finance
        Route::get('finance', [App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
        Route::get('finance/export', [App\Http\Controllers\FinanceController::class, 'exportExcel'])->name('finance.export');
        Route::post('finance/transaction', [App\Http\Controllers\FinanceController::class, 'storeTransaction'])->name('finance.transaction.store');
        Route::post('finance/account', [App\Http\Controllers\FinanceController::class, 'storeAccount'])->name('finance.account.store');
        Route::delete('finance/account/{id}', [App\Http\Controllers\FinanceController::class, 'destroyAccount'])->name('finance.account.destroy');
        Route::get('finance/next-code', [App\Http\Controllers\FinanceController::class, 'getNextCode'])->name('finance.account.next-code');

        Route::get('users', fn () => view('Users.index'))->name('users.index');
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);
        // Absensi dihapus dari sistem

        // Salary & Attendance
        Route::resource('salary-periods', App\Http\Controllers\SalaryPeriodController::class);
        Route::post('salary-periods/{salary_period}/slips/store-one', [App\Http\Controllers\SalaryPeriodController::class, 'storeOne'])->name('salary-periods.slips.store-one');
        Route::resource('salary-slips', App\Http\Controllers\SalarySlipController::class)->only(['update']);
        Route::get('salary-slips/{salary_slip}/print', [App\Http\Controllers\SalarySlipController::class, 'print'])->name('salary-slips.print');
        Route::post('salary-slips/{salary_slip}/publish', [App\Http\Controllers\SalarySlipController::class, 'publish'])->name('salary-slips.publish');

        // Reports
        Route::get('report/invoice', [App\Http\Controllers\Report\InvoiceReportController::class, 'index'])->name('report.invoice');
        Route::get('report/invoice/export', [App\Http\Controllers\Report\InvoiceReportController::class, 'export'])->name('report.invoice.export');
        Route::get('report/sales', [ReportController::class, 'salesReport'])->name('report.sales');
        Route::get('report/purchase', [App\Http\Controllers\Report\PurchaseReportController::class, 'index'])->name('report.purchase');
        Route::get('report/purchase/export', [App\Http\Controllers\Report\PurchaseReportController::class, 'export'])->name('report.purchase.export');
        Route::get('report/stock', [ReportController::class, 'stockReport'])->name('report.stock');
        Route::get('report/stock/export', [ReportController::class, 'stockReportExport'])->name('report.stock.export');
        Route::get('report/ar-aging', [ReportController::class, 'arAging'])->name('report.ar-aging');
        Route::get('report/general-ledger', [ReportController::class, 'generalLedger'])->name('report.general-ledger');
        Route::get('report/cash-book', [ReportController::class, 'cashBook'])->name('report.cash-book');
        Route::get('report/balance-sheet', [ReportController::class, 'balanceSheet'])->name('report.balance-sheet');
        Route::get('report/balance-sheet/export/csv', [ReportController::class, 'balanceSheetExportCSV'])->name('report.balance-sheet.export.csv');
        Route::get('report/profit-loss', [ReportController::class, 'profitAndLoss'])->name('report.profit-loss');
        Route::get('report/coa-management', [ReportController::class, 'coaManagement'])->name('report.coa-management');

        Route::prefix('delivery-order')->name('delivery-order.')->group(function () {
            Route::get('/', [DeliveryOrderController::class, 'index'])->name('index');
            Route::get('/print/{id}', [DeliveryOrderController::class, 'print'])->name('print');
            Route::get('/track/{id}', [DeliveryOrderController::class, 'track'])->name('track');
        });

        Route::prefix('driver')->name('driver.')->group(function () {
            Route::get('/delivery', [App\Http\Controllers\DriverDeliveryController::class, 'index'])->name('delivery.index');
            Route::get('/delivery/{id}', [App\Http\Controllers\DriverDeliveryController::class, 'show'])->name('delivery.show');
            Route::post('/delivery/{id}/start', [App\Http\Controllers\DriverDeliveryController::class, 'startTrip'])->name('delivery.start');
            Route::post('/delivery/{id}/location', [App\Http\Controllers\DriverDeliveryController::class, 'updateLocation'])->name('delivery.location');
            Route::post('/delivery/{id}/invoice/{invoiceId}/arrive', [App\Http\Controllers\DriverDeliveryController::class, 'arriveAtStop'])->name('delivery.arrive');
            Route::get('/delivery/{id}/invoice/{invoiceId}/proof', [App\Http\Controllers\DriverDeliveryController::class, 'getProof'])->name('delivery.proof');
            Route::post('/delivery/{id}/finish', [App\Http\Controllers\DriverDeliveryController::class, 'finishTrip'])->name('delivery.finish');
        });

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

            // Test Notification Route
            Route::get('/test-notification', function () {
                App\Services\NotificationService::notifyByPermission(
                    'dashboard', // Assuming this permission exists
                    'Test Notification',
                    'Ini adalah notifikasi test sistem',
                    url('/dashboard'),
                    'success'
                );

                return 'Notification sent!';
            });
        });
    });
});
