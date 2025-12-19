<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CashController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
    Route::post('/cash/movements', [CashController::class, 'storeMovement'])->name('cash.movements.store');
    Route::put('/cash/movements/{movement}', [CashController::class, 'updateMovement'])->name('cash.movements.update');
    Route::delete('/cash/movements/{movement}', [CashController::class, 'destroyMovement'])->name('cash.movements.destroy');

    Route::post('/cash/accounts', [CashController::class, 'storeAccount'])->name('cash.accounts.store');
    Route::put('/cash/accounts/{account}', [CashController::class, 'updateAccount'])->name('cash.accounts.update');
    Route::delete('/cash/accounts/{account}', [CashController::class, 'destroyAccount'])->name('cash.accounts.destroy');
});
