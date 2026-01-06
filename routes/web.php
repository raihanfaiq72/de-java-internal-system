<?php

use App\Http\Controllers\MitraController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BarangController;

/**
 * route untuk aksi login 
 * kemudian dilanjutkan untuk aksi pilih kantor atau outlet , disini aku mendefinisikan secara program sebagai outlet
 * akan tetapi tampil di user sebagai kantor
 */
Route::get('/',[AuthController::class,'login']);
Route::get('/select-your-outlet',[AuthController::class,'syo'])->name('syo');

/**
 * ini adalah route umum dimana program tidak mengecek apapun kecuali 'auth' 
 * karena sudah di guard oleh json permission
 */
Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');
Route::get('sales',[SalesController::class,'index'])->name('sales');
Route::get('mitra',[MitraController::class,'index'])->name('mitra');
Route::get('barang',[BarangController::class,'index'])->name('barang');

Route::get('users', function () {
    return view('Users.index');
})->name('users.index');