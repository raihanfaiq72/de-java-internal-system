<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPiutangController;
use App\Http\Controllers\DashboardSalesController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::post('/login-proses', [AuthController::class, 'loginProses'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public App Release Page (no auth required)
Route::get('/app-release3', [App\Http\Controllers\AppReleaseController::class, 'publicIndex']);
Route::get('/app-release/download/{appRelease}', [App\Http\Controllers\AppReleaseController::class, 'publicDownload'])->name('public.download');

Route::middleware(['auth'])->group(function () {

    Route::get('/select-your-outlet', [AuthController::class, 'syo'])->name('syo');
    Route::post('/set-active-outlet', [AuthController::class, 'setOutlet'])->name('set.outlet');
    Route::delete('/delete-outlet/{id}', [AuthController::class, 'destroyOutlet'])->name('syo.destroy');

    // User Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Api\ProfileController::class, 'update'])->name('profile.update');

    // Finance Preview & Export
    Route::get('finance/preview', [App\Http\Controllers\Api\FinanceController::class, 'previewReport'])->name('finance.preview');
    Route::get('finance/export', [App\Http\Controllers\Api\FinanceController::class, 'exportExcel'])->name('finance.export');

    Route::middleware(['module.access'])->group(function () {
        // Notifications (moved inside middleware group)
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'recent'])->name('notifications.recent');

        // App Releases CRUD
        Route::resource('app-releases', App\Http\Controllers\AppReleaseController::class);
        Route::get('app-releases/{appRelease}/download', [App\Http\Controllers\AppReleaseController::class, 'download'])->name('app-releases.download');
        Route::post('app-releases/{appRelease}/toggle-latest', [App\Http\Controllers\AppReleaseController::class, 'toggleLatest'])->name('app-releases.toggle-latest');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard-piutang', [DashboardPiutangController::class, 'index'])->name('dashboard.piutang');
        Route::get('dashboard-sales', [DashboardSalesController::class, 'index'])->name('dashboard.sales');

        Route::get('pdf-to-excel', [App\Http\Controllers\Api\PdfToExcelController::class, 'index'])->name('pdf.to.excel');
        Route::get('pdf-to-excel/client', [App\Http\Controllers\Api\PdfToExcelController::class, 'clientSide'])->name('pdf.to.excel.client');
        Route::post('pdf-to-excel/convert', [App\Http\Controllers\Api\PdfToExcelController::class, 'convert'])->name('pdf.to.excel.convert');

        Route::get('sales', [App\Http\Controllers\Api\SalesController::class, 'index'])->name('sales');
        Route::get('sales/export', [App\Http\Controllers\Api\SalesController::class, 'export'])->name('sales.export');
        Route::get('sales/mass-print', [App\Http\Controllers\Api\SalesController::class, 'massPrint'])->name('sales.mass-print');
        Route::get('sales/approval', [App\Http\Controllers\Api\SalesController::class, 'approval'])->name('sales.approval');
        Route::get('sales/approval-overdue', [App\Http\Controllers\Api\SalesController::class, 'approvalOverdue'])->name('sales.approval.overdue');
        Route::get('sales/approval/{id}', [App\Http\Controllers\Api\SalesController::class, 'approvalDetail'])->name('sales.approval.show');
        Route::get('sales/approval-overdue/{id}', [App\Http\Controllers\Api\SalesController::class, 'approvalOverdueDetail'])->name('sales.approval.overdue.show');
        Route::get('sales/{id}', [SalesController::class, 'show'])->name('sales.show');
        Route::get('sales/print/{id}', [SalesController::class, 'printInvoice'])->name('sales.print');
        Route::get('sales-receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
        Route::get('sales-receipt/{id}', [SalesController::class, 'showReceipt'])->name('sales.receipt.show');
        Route::get('sales-receipt/print/{id}', [SalesController::class, 'printReceipt'])->name('sales.receipt.print');

        Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase');
        Route::get('purchase/export', [App\Http\Controllers\Api\PurchaseController::class, 'export'])->name('purchase.export');
        Route::get('purchase/mass-print', [App\Http\Controllers\Api\PurchaseController::class, 'massPrint'])->name('purchase.mass-print');
        Route::get('purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
        Route::get('purchase/print/{id}', [App\Http\Controllers\Api\PurchaseController::class, 'printInvoice'])->name('purchase.print');
        Route::get('purchase-receipt', [App\Http\Controllers\Api\PurchaseController::class, 'receipt'])->name('purchase.receipt');
        Route::get('purchase-receipt/{id}', [PurchaseController::class, 'showReceipt'])->name('purchase.receipt.show');
        Route::get('purchase-receipt/print/{id}', [App\Http\Controllers\Api\PurchaseController::class, 'printReceipt'])->name('purchase.receipt.print');

        Route::get('mitra/export', [App\Http\Controllers\Api\MitraExportController::class, 'export'])->name('mitra.export');
        Route::get('import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('import/stock', [App\Http\Controllers\Api\ImportController::class, 'importStock'])->name('import.stock');
        Route::post('import/mitra', [App\Http\Controllers\Api\ImportController::class, 'importMitra'])->name('import.mitra');
        Route::post('import/sales', [App\Http\Controllers\Api\ImportController::class, 'importSales'])->name('import.sales');
        Route::post('import/purchase', [App\Http\Controllers\Api\ImportController::class, 'importPurchase'])->name('import.purchase');
        Route::post('import/receipt', [App\Http\Controllers\Api\ImportController::class, 'importReceipt'])->name('import.receipt');
        Route::post('import/purchase-receipt', [App\Http\Controllers\Api\ImportController::class, 'importPurchaseReceipt'])->name('import.purchase.receipt');
        Route::post('import/employee', [App\Http\Controllers\Api\ImportController::class, 'importEmployee'])->name('import.employee');
        Route::get('import/template/{type}', [App\Http\Controllers\Api\ImportController::class, 'downloadTemplate'])->name('import.template');
        Route::get('export/receipt', [App\Http\Controllers\Api\ImportController::class, 'exportReceiptPdf'])->name('export.receipt');
        Route::get('mitra', [MitraController::class, 'index'])->name('mitra');
        Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
        Route::get('barang', [BarangController::class, 'index'])->name('barang');
        Route::get('stok', [StockController::class, 'index'])->name('stok');
        Route::get('stok/export', [App\Http\Controllers\Api\StockReportController::class, 'export'])->name('stok.export');
        Route::get('stok/print', [App\Http\Controllers\Api\StockReportController::class, 'print'])->name('stok.print');
        Route::get('expenses', [App\Http\Controllers\Api\ExpensePageController::class, 'index'])->name('expenses.index');
        Route::get('fleets', [FleetController::class, 'index'])->name('fleets.index');

        // Finance
        Route::get('finance', [App\Http\Controllers\Api\FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance/transaction', [App\Http\Controllers\Api\FinanceController::class, 'storeTransaction'])->name('finance.transaction.store');
        Route::post('finance/account', [App\Http\Controllers\Api\FinanceController::class, 'storeAccount'])->name('finance.account.store');
        Route::delete('finance/account/{id}', [App\Http\Controllers\Api\FinanceController::class, 'destroyAccount'])->name('finance.account.destroy');
        Route::get('finance/next-code', [App\Http\Controllers\Api\FinanceController::class, 'getNextCode'])->name('finance.account.next-code');

        Route::get('users', fn () => view('Users.index'))->name('users.index');
        Route::resource('employees', App\Http\Controllers\Api\EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);
        // Absensi dihapus dari sistem

        // Salary & Attendance
        Route::resource('salary-periods', App\Http\Controllers\Api\SalaryPeriodController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::post('salary-periods/{salary_period}/slips/store-one', [App\Http\Controllers\Api\SalaryPeriodController::class, 'storeOne'])->name('salary-periods.slips.store-one');
        Route::get('salary-periods/{salary_period}/bulk-print', [App\Http\Controllers\Api\SalaryPeriodController::class, 'bulkPrint'])->name('salary-periods.bulk-print');
        Route::resource('salary-slips', App\Http\Controllers\Api\SalarySlipController::class)->only(['update', 'destroy']);
        Route::get('salary-slips/{salary_slip}/print', [App\Http\Controllers\Api\SalarySlipController::class, 'print'])->name('salary-slips.print');
        Route::post('salary-slips/{salary_slip}/publish', [App\Http\Controllers\Api\SalarySlipController::class, 'publish'])->name('salary-slips.publish');

        // Bulk Reports
        Route::get('bulk-reports', [App\Http\Controllers\Api\BulkReportController::class, 'index'])->name('bulk-reports.index');
        Route::post('bulk-reports/period', [App\Http\Controllers\Api\BulkReportController::class, 'storePeriod'])->name('bulk-reports.store-period');
        Route::get('bulk-reports/{bulkReport}/preview', [App\Http\Controllers\Api\BulkReportController::class, 'preview'])->name('bulk-reports.preview');
        Route::get('bulk-reports/{bulkReport}/detail', [App\Http\Controllers\Api\BulkReportController::class, 'detail'])->name('bulk-reports.detail');
        Route::get('bulk-reports/{bulkReport}/generate-pdf', [App\Http\Controllers\Api\BulkReportController::class, 'generatePDF'])->name('bulk-reports.generate-pdf');
        Route::post('bulk-reports/{bulkReport}/mark-printed', [App\Http\Controllers\Api\BulkReportController::class, 'markAsPrinted'])->name('bulk-reports.mark-printed');
        Route::delete('bulk-reports/{bulkReport}', [App\Http\Controllers\Api\BulkReportController::class, 'destroy'])->name('bulk-reports.destroy');

        // Reports
        Route::get('report/invoice', [App\Http\Controllers\Report\InvoiceReportController::class, 'index'])->name('report.invoice');
        Route::get('report/invoice/export', [App\Http\Controllers\Report\InvoiceReportController::class, 'export'])->name('report.invoice.export');
        Route::get('report/sales', [App\Http\Controllers\Api\ReportController::class, 'salesReport'])->name('report.sales');
        Route::get('report/purchase', [App\Http\Controllers\Report\PurchaseReportController::class, 'index'])->name('report.purchase');
        Route::get('report/purchase/export', [App\Http\Controllers\Report\PurchaseReportController::class, 'export'])->name('report.purchase.export');
        Route::get('report/stock', [App\Http\Controllers\Api\ReportController::class, 'stockReport'])->name('report.stock');
        Route::get('report/stock/export', [App\Http\Controllers\Api\ReportController::class, 'stockReportExport'])->name('report.stock.export');
        Route::get('report/ar-aging', [App\Http\Controllers\Api\ReportController::class, 'arAging'])->name('report.ar-aging');
        Route::get('report/supplier-invoices', [App\Http\Controllers\Api\ReportController::class, 'supplierInvoices'])->name('report.supplier-invoices');
        Route::get('report/supplier-invoices/{id}/detail', [App\Http\Controllers\Api\ReportController::class, 'supplierInvoicesDetail'])->name('report.supplier-invoices.detail');
        Route::get('report/general-ledger', [App\Http\Controllers\Api\ReportController::class, 'generalLedger'])->name('report.general-ledger');
        Route::get('report/cash-book', [App\Http\Controllers\Api\ReportController::class, 'cashBook'])->name('report.cash-book');
        Route::get('report/balance-sheet', [App\Http\Controllers\Api\ReportController::class, 'balanceSheet'])->name('report.balance-sheet');
        Route::get('report/balance-sheet/export/csv', [App\Http\Controllers\Api\ReportController::class, 'balanceSheetExportCSV'])->name('report.balance-sheet.export.csv');
        Route::get('report/profit-loss', [App\Http\Controllers\Api\ReportController::class, 'profitAndLoss'])->name('report.profit-loss');
        Route::get('report/coa-management', [App\Http\Controllers\Api\ReportController::class, 'coaManagement'])->name('report.coa-management');

        Route::prefix('delivery-order')->name('delivery-order.')->group(function () {
            Route::get('/', [DeliveryOrderController::class, 'index'])->name('index');
            Route::get('/print/{id}', [DeliveryOrderController::class, 'print'])->name('print');
            Route::get('/track/{id}', [DeliveryOrderController::class, 'track'])->name('track');
        });

        Route::prefix('driver')->name('driver.')->group(function () {
            Route::get('/delivery', [App\Http\Controllers\DriverDeliveryController::class, 'index'])->name('delivery.index');
            Route::get('/delivery/{id}', [App\Http\Controllers\DriverDeliveryController::class, 'show'])->name('delivery.show');
        });

        Route::prefix('admin')->group(function () {
            Route::get('/permissions', [App\Http\Controllers\Api\AdminPermissionController::class, 'index'])->name('permissions.index');
            Route::put('/permissions/{id}', [App\Http\Controllers\Api\AdminPermissionController::class, 'update'])->name('permissions.update');
            Route::resource('roles', App\Http\Controllers\Api\RoleController::class)->except(['edit']);

            // Offices
            Route::get('/offices', [App\Http\Controllers\Api\AdminOfficeController::class, 'index'])->name('offices.index');
            Route::post('/offices', [App\Http\Controllers\Api\AdminOfficeController::class, 'store'])->name('offices.store');
            Route::get('/offices/{id}', [App\Http\Controllers\Api\AdminOfficeController::class, 'show'])->name('offices.show');
            Route::put('/offices/{id}', [App\Http\Controllers\Api\AdminOfficeController::class, 'update'])->name('offices.update');
            Route::delete('/offices/{id}', [App\Http\Controllers\Api\AdminOfficeController::class, 'destroy'])->name('offices.destroy');

            // User Plots
            Route::get('/user-plots', [App\Http\Controllers\Api\UserPlotController::class, 'index'])->name('user_plots.index');
            Route::post('/user-plots', [App\Http\Controllers\Api\UserPlotController::class, 'store'])->name('user_plots.store');
            Route::get('/user-plots/{id}', [App\Http\Controllers\Api\UserPlotController::class, 'show'])->name('user_plots.show');
            Route::put('/user-plots/{id}', [App\Http\Controllers\Api\UserPlotController::class, 'update'])->name('user_plots.update');
            Route::delete('/user-plots/{id}', [App\Http\Controllers\Api\UserPlotController::class, 'destroy'])->name('user_plots.destroy');

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
