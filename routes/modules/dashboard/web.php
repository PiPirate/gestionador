<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Modules\NotificationController;
use App\Http\Controllers\Modules\SearchController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', SearchController::class)->name('search');
    Route::post('/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.read');
});
