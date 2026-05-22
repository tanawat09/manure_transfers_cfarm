<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferOutController;
use App\Http\Controllers\TransferInController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\ManurePileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect welcome page to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. บันทึกขาออกจากฟาร์ม (Admin, Staff)
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/transfers/out', [TransferOutController::class, 'create'])->name('transfers.out');
        Route::post('/transfers/out', [TransferOutController::class, 'store'])->name('transfers.out.store');
        Route::get('/transfers/out/{id}/success', [TransferOutController::class, 'success'])->name('transfers.out_success');
    });

    // 2. ตรวจรับเข้ากองปลายทาง (Admin, Staff)
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/transfers/in', [TransferInController::class, 'index'])->name('transfers.in');
        Route::post('/transfers/in/{id}/receive', [TransferInController::class, 'receive'])->name('transfers.receive');
    });

    // 3. รายงานและ Export (ทุกคนมีสิทธิ์ดูได้)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // 4. ข้อมูลพื้นฐาน CRUD (Admin เท่านั้น)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('farms', FarmController::class)->except(['show']);
        Route::resource('piles', ManurePileController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
