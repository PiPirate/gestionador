<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SettingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});
