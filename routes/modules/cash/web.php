<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CashController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
});
