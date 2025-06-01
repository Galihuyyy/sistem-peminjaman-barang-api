<?php

use App\Http\Controllers\Admin\adminController;
use App\Http\Controllers\Admin\alatController;
use App\Http\Controllers\Admin\siswaController;
use App\Http\Controllers\Peminjaman\peminjamanController;
use App\Http\Controllers\Auth\authController;
use App\Http\Middleware\role;
use App\Models\transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = User::with(['profile'])->find(auth()->user()->id);

    $transaksi_pending = transaksi::where('peminjam_id', $user->id)->where('status', 'pending')->with(['peminjaman.alat.foto_alat'])->first();

    return response()->json([
        'user' => $user,
        'transaksi_pending'=> $transaksi_pending
    ]);
    
})->middleware('auth:sanctum');

Route::post('/auth/register', [authController::class, 'register']);
Route::post('/auth/login', [authController::class, 'login']);


Route::get('/kelas', [authController::class, 'getKelas']);
Route::get('/jurusan', [authController::class, 'getJurusan']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [authController::class, 'logout']);

    Route::middleware(role::class . ':admin')->group(function () {
        Route::resource('/home/admin', adminController::class)->middleware(role::class . ':admin');
        Route::resource('/home/siswa', siswaController::class)->middleware(role::class . ':admin');
        Route::resource('/home/alat', alatController::class)->middleware(role::class . ':admin')->except(['index','show']);

        Route::put('/admin/transaksi/konfirmasi/{id}', [peminjamanController::class, 'confirmTransaksi']);
        
        Route::put('/admin/transaksi/kembali/{id}', [peminjamanController::class, 'kembalikan']);
    });
    Route::delete('/admin/transaksi/delete/{id}', [peminjamanController::class, 'transaksiPending']);
    
    Route::get('/home/alat', [alatController::class, 'index']);
    Route::get('/home/alat/{id}', [alatController::class, 'show']);

    Route::post('/peminjaman/transaksi/add', [peminjamanController::class, 'pinjamLangsung']);
    Route::post('/peminjaman', [peminjamanController::class, 'addToCart']);
    Route::get('/peminjaman', [peminjamanController::class, 'viewCart']);
    Route::post('/checkout', [peminjamanController::class, 'checkout']);
    
    Route::post('/ulasan', [peminjamanController::class, 'tambahUlasan']);
    Route::get('/transaksi', [peminjamanController::class, 'riwayatTransaksi']);
    Route::get('/transaksi/get', [peminjamanController::class, 'getTransaksi']);



});