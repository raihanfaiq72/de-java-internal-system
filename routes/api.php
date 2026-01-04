<?php

use App\Http\Controllers\Api\MitraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

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
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
