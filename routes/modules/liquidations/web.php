<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\LiquidationsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/liquidations', [LiquidationsController::class, 'index'])->name('liquidations.index');
});
