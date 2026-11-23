<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

/* ---------------------------- Publik ---------------------------- */
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/kamar', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/kamar/{room:slug}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/kamar/{room:slug}/pesan', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('inquiry.store');

/* ----------------------------- Admin ---------------------------- */
Route::prefix(config('kos.admin_path'))->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('kamar/tambah', [AdminRoomController::class, 'create'])->name('rooms.create');
        Route::post('kamar', [AdminRoomController::class, 'store'])->name('rooms.store');
        Route::get('kamar/{room}/ubah', [AdminRoomController::class, 'edit'])->name('rooms.edit');
        Route::put('kamar/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
        Route::patch('kamar/{room}/status', [AdminRoomController::class, 'toggleStatus'])->name('rooms.status');
        Route::delete('kamar/{room}', [AdminRoomController::class, 'destroy'])->name('rooms.destroy');

        Route::post('pesan/baca-semua', [AdminInquiryController::class, 'markAllRead'])->name('inquiries.read-all');
        Route::patch('pesan/{inquiry}/baca', [AdminInquiryController::class, 'toggleRead'])->name('inquiries.read');
        Route::delete('pesan/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');
    });
});
