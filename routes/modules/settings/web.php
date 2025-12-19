<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SettingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/rates', [SettingsController::class, 'updateRates'])->name('settings.rates');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::put('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
});
