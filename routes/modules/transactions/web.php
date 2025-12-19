<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\TransactionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionsController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{transaction}', [TransactionsController::class, 'update'])->name('transactions.update');
});
