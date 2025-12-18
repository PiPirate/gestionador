<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\InvestmentsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/investments', [InvestmentsController::class, 'index'])->name('investments.index');
});
