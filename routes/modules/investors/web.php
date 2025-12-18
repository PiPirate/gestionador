<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\InvestorsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/investors', [InvestorsController::class, 'index'])->name('investors.index');
});
