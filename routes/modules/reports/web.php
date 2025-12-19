<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ReportsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/transactions', [ReportsController::class, 'exportTransactions'])->name('reports.export.transactions');
});
