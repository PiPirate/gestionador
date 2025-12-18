<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\AuditController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
