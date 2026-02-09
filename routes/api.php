<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceItemController;
use App\Http\Controllers\Api\InvoiceItemTaxController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesAttendanceController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockLocationController;
use App\Http\Controllers\Api\OfficeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ChartOfAccountController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\DeliveryOrderFleetController;
use App\Http\Controllers\Api\DeliveryOrderInvoiceController;
use App\Http\Controllers\Api\FinancialAccountController;
use App\Http\Controllers\Api\FleetController;
use App\Http\Controllers\Api\SupplierBrandController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('office-api')
        ->name('office-api.')
        ->group(function () {
            Route::get('/', [OfficeController::class, 'index'])->name('index');
        });

    Route::prefix('mitra-api')
        ->name('mitra-api.')
        ->group(function () {
            Route::get('/', [PartnerController::class, 'index'])->name('index');
            Route::post('/', [PartnerController::class, 'store'])->name('store');
            Route::get('/{id}', [PartnerController::class, 'show'])->name('show');
            Route::put('/{id}', [PartnerController::class, 'update'])->name('update');
            Route::delete('/{id}', [PartnerController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [PartnerController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [PartnerController::class, 'forceDestroy'])->name('force-delete');
            Route::get('/search/{value}', [PartnerController::class, 'search'])->name('search');
        });

    Route::prefix('product-categories-api')
        ->name('product-category-api.')
        ->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
            Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductCategoryController::class, 'show'])->name('show');
            Route::put('/{id}', [ProductCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [ProductCategoryController::class, 'search'])->name('search');
        });

    Route::prefix('product-api')
        ->name('product-api.')
        ->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductController::class, 'show'])->name('show');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/recalculate-stock', [ProductController::class, 'recalculateStock'])->name('recalculate-stock');
            Route::get('/search/{value}', [ProductController::class, 'search'])->name('search');
        });

    Route::get('/product-next-sku-api', [ProductController::class, 'nextSku'])
        ->name('product.next-sku-api');

    Route::prefix('brand-api')
        ->name('brand-api.')
        ->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::get('/suppliers/{supplier_id}', [BrandController::class, 'suppliers'])->name('suppliers');
            Route::post('/', [BrandController::class, 'store'])->name('store');
            Route::get('/{id}', [BrandController::class, 'show'])->name('show');
            Route::put('/{id}', [BrandController::class, 'update'])->name('update');
            Route::delete('/{id}', [BrandController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('supplier-brand-api')
        ->name('supplier-brand-api.')
        ->group(function () {
            Route::get('/', [SupplierBrandController::class, 'index'])->name('index');
            Route::post('/', [SupplierBrandController::class, 'store'])->name('store');
            Route::get('/{id}', [SupplierBrandController::class, 'show'])->name('show');
            Route::put('/{id}', [SupplierBrandController::class, 'update'])->name('update');
            Route::delete('/{id}', [SupplierBrandController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('stock-api')
        ->name('stock-api.')
        ->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');
            Route::get('/mutations', [StockController::class, 'mutations'])->name('mutations');
            Route::post('/opening-stock', [StockController::class, 'openingStock'])->name('opening-stock');
            Route::post('/stock-opname', [StockController::class, 'stockOpname'])->name('stock-opname');
            Route::put('/{id}', [StockController::class, 'updateStock'])->name('update-stock');
            Route::get('/{id}/fifo', [StockController::class, 'fifo'])->name('fifo');
        });

    Route::prefix('stock-location-api')
        ->name('stock-location-api.')
        ->group(function () {
            Route::get('/', [StockLocationController::class, 'index'])->name('index');
            Route::post('/', [StockLocationController::class, 'store'])->name('store');
            Route::get('/{id}', [StockLocationController::class, 'show'])->name('show');
            Route::put('/{id}', [StockLocationController::class, 'update'])->name('update');
            Route::delete('/{id}', [StockLocationController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('tax-api')
        ->name('tax-api.')
        ->group(function () {
            Route::get('/', [TaxController::class, 'index'])->name('index');
            Route::post('/', [TaxController::class, 'store'])->name('store');
            Route::get('/{id}', [TaxController::class, 'show'])->name('show');
            Route::put('/{id}', [TaxController::class, 'update'])->name('update');
            Route::delete('/{id}', [TaxController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [TaxController::class, 'search'])->name('search');
        });

    Route::post('/invoice-create-api', [InvoiceController::class, 'createFullInvoice'])
        ->name('invoice.create-full-api');

    Route::prefix('invoice-api')
        ->name('invoice-api.')
        ->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
            Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [InvoiceController::class, 'search'])->name('search');
        });

    Route::prefix('invoice-item-api')
        ->name('invoice-item-api.')
        ->group(function () {
            Route::get('/', [InvoiceItemController::class, 'index'])->name('index');
            Route::post('/', [InvoiceItemController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceItemController::class, 'show'])->name('show');
            Route::put('/{id}', [InvoiceItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceItemController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [InvoiceItemController::class, 'search'])->name('search');
        });

    Route::prefix('invoice-item-tax-api')
        ->name('invoice-item-tax-api.')
        ->group(function () {
            Route::get('/', [InvoiceItemTaxController::class, 'index'])->name('index');
            Route::post('/', [InvoiceItemTaxController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceItemTaxController::class, 'show'])->name('show');
            Route::put('/{id}', [InvoiceItemTaxController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceItemTaxController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [InvoiceItemTaxController::class, 'search'])->name('search');
        });

    Route::prefix('payment-api')
        ->name('payment-api.')
        ->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::post('/', [PaymentController::class, 'store'])->name('store');
            Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
            Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [PaymentController::class, 'search'])->name('search');
        });

    Route::prefix('expense-api')
        ->name('expense-api.')
        ->group(function () {
            Route::get('/analytics-summary', [ExpenseController::class, 'analyticsSummary'])->name('analytics-summary');
            Route::get('/analytics-trend', [ExpenseController::class, 'analyticsTrend'])->name('analytics-trend');
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
            Route::put('/{id}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [ExpenseController::class, 'search'])->name('search');
        });

    Route::prefix('sales-attendance-api')
        ->name('sales-attendance-api.')
        ->group(function () {
            Route::get('/', [SalesAttendanceController::class, 'index'])->name('index');
            Route::post('/', [SalesAttendanceController::class, 'store'])->name('store');
            Route::get('/{id}', [SalesAttendanceController::class, 'show'])->name('show');
            Route::put('/{id}', [SalesAttendanceController::class, 'update'])->name('update');
        });

    Route::prefix('user-api')
        ->name('user-api.')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/search/{value}', [UserController::class, 'search'])->name('search');
        });

    Route::prefix('coa-api')
        ->name('coa-api.')
        ->group(function () {
            Route::get('/', [ChartOfAccountController::class, 'index'])->name('index');
            Route::post('/', [ChartOfAccountController::class, 'store'])->name('store');
            Route::get('/{id}', [ChartOfAccountController::class, 'show'])->name('show');
            Route::put('/{id}', [ChartOfAccountController::class, 'update'])->name('update');
            Route::delete('/{id}', [ChartOfAccountController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('financial-account-api')
        ->name('financial-account-api.')
        ->group(function () {
            Route::get('/', [FinancialAccountController::class, 'index'])->name('index');
            Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('show');
        });

    Route::prefix('delivery-order-api')->name('delivery-order-api.')->group(function () {
        Route::get('/', [DeliveryOrderController::class, 'index'])->name('index');
        Route::post('/', [DeliveryOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [DeliveryOrderController::class, 'show'])->name('show');
        Route::put('/{id}', [DeliveryOrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [DeliveryOrderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('delivery-order-invoice-api')->name('delivery-order-invoice-api.')->group(function () {
        Route::get('/', [DeliveryOrderInvoiceController::class, 'index'])->name('index');
        Route::post('/', [DeliveryOrderInvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [DeliveryOrderInvoiceController::class, 'show'])->name('show');
        Route::put('/{id}', [DeliveryOrderInvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [DeliveryOrderInvoiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('delivery-order-fleet-api')->name('delivery-order-fleet-api.')->group(function () {
        Route::get('/', [DeliveryOrderFleetController::class, 'index'])->name('index');
        Route::get('/by-do/{doId}', [DeliveryOrderFleetController::class, 'getByDeliveryOrder'])->name('by-do');
        Route::post('/', [DeliveryOrderFleetController::class, 'store'])->name('store');
        Route::get('/{id}', [DeliveryOrderFleetController::class, 'show'])->name('show');
        Route::put('/{id}', [DeliveryOrderFleetController::class, 'update'])->name('update');
        Route::delete('/{id}', [DeliveryOrderFleetController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('fleet-api')
        ->name('fleet-api.')
        ->group(function () {
            Route::get('/', [FleetController::class, 'index'])->name('index');
            Route::post('/', [FleetController::class, 'store'])->name('store');
            Route::get('/{id}', [FleetController::class, 'show'])->name('show');
            Route::put('/{id}', [FleetController::class, 'update'])->name('update');
            Route::delete('/{id}', [FleetController::class, 'destroy'])->name('destroy');
        });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
