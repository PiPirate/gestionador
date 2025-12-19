<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\LiquidationsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/liquidations', [LiquidationsController::class, 'index'])->name('liquidations.index');
    Route::post('/liquidations', [LiquidationsController::class, 'store'])->name('liquidations.store');
    Route::put('/liquidations/{liquidation}', [LiquidationsController::class, 'update'])->name('liquidations.update');
    Route::post('/liquidations/{liquidation}/process', [LiquidationsController::class, 'process'])->name('liquidations.process');
    Route::delete('/liquidations/{liquidation}', [LiquidationsController::class, 'destroy'])->name('liquidations.destroy');
});
