<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PengaduanController;
use Illuminate\Support\Facades\Route;

// ================= HALAMAN PUBLIK (SISWA, TANPA LOGIN) =================
Route::get('/', [PengaduanController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
Route::get('/lacak', [PengaduanController::class, 'track'])
    ->middleware('throttle:20,1')
    ->name('pengaduan.track.form');

// ================= AUTENTIKASI ADMIN =================
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ================= AREA ADMIN (WAJIB LOGIN) =================
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/pengaduan/rekap/excel', [DashboardController::class, 'export'])->name('admin.pengaduan.export');
    Route::get('/pengaduan/{pengaduan}', [DashboardController::class, 'show'])->name('admin.pengaduan.show');
    Route::put('/pengaduan/{pengaduan}', [DashboardController::class, 'updateStatus'])->name('admin.pengaduan.update');
});
