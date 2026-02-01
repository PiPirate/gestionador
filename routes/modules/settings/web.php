<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ProfitRulesController;
use App\Http\Controllers\Modules\SettingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/rates', [SettingsController::class, 'updateRates'])->name('settings.rates');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::put('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::post('/settings/profit-rules', [ProfitRulesController::class, 'store'])->name('settings.profit-rules.store');
    Route::post('/settings/profit-rules/{profitRule}/activate', [ProfitRulesController::class, 'activate'])->name('settings.profit-rules.activate');
    Route::post('/settings/profit-rules/{profitRule}/deactivate', [ProfitRulesController::class, 'deactivate'])->name('settings.profit-rules.deactivate');
    Route::put('/settings/profit-rules/{profitRule}', [ProfitRulesController::class, 'update'])->name('settings.profit-rules.update');
    Route::delete('/settings/profit-rules/{profitRule}', [ProfitRulesController::class, 'destroy'])->name('settings.profit-rules.destroy');
});
