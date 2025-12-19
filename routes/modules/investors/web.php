<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\InvestorsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/investors', [InvestorsController::class, 'index'])->name('investors.index');
    Route::post('/investors', [InvestorsController::class, 'store'])->name('investors.store');
    Route::put('/investors/{investor}', [InvestorsController::class, 'update'])->name('investors.update');
    Route::delete('/investors/{investor}', [InvestorsController::class, 'destroy'])->name('investors.destroy');
});
