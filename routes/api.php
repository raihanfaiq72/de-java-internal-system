<?php

use App\Http\Controllers\Api\MitraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UnitCategoryController;
use App\Http\Controllers\Api\UnitController;

Route::prefix('admin')->group(function() {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
});

Route::prefix('mitra-api')->group(function() {
    Route::get('/', [MitraController::class, 'index']);
    Route::post('/', [MitraController::class, 'store']);
    Route::get('/{id}', [MitraController::class, 'show']);
    Route::put('/{id}', [MitraController::class, 'update']);
    Route::delete('/{id}', [MitraController::class, 'destroy']);
    Route::get('/search/{value}', [MitraController::class, 'search']);
});

Route::prefix('unit-categories-api')->group(function () {
    Route::get('/', [UnitCategoryController::class, 'index']);
    Route::post('/', [UnitCategoryController::class, 'store']);
    Route::get('/{id}', [UnitCategoryController::class, 'show']);
    Route::put('/{id}', [UnitCategoryController::class, 'update']);
    Route::delete('/{id}', [UnitCategoryController::class, 'destroy']);
    Route::get('/search/{value}', [UnitCategoryController::class, 'search']);
});

Route::prefix('unit-api')->group(function () {
    Route::get('/', [UnitController::class, 'index']);
    Route::post('/', [UnitController::class, 'store']);
    Route::get('/{id}', [UnitController::class, 'show']);
    Route::put('/{id}', [UnitController::class, 'update']);
    Route::delete('/{id}', [UnitController::class, 'destroy']);
    Route::get('/search/{value}', [UnitController::class, 'search']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
