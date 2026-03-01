<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppEnhancementController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\AiEnhanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/enhancements', [AppEnhancementController::class, 'index'])->name('admin.enhancements');
    Route::get('/api-logs', [ApiLogController::class, 'index'])->name('admin.apilogs');
    Route::get('/api-logs/{log}', [ApiLogController::class, 'show'])->name('admin.apilogs.show');

    // Settings Pages
    Route::get('/settings/system', [AdminController::class, 'systemSettings'])->name('admin.settings.system');
    Route::post('/settings/system', [AdminController::class, 'updateSettings'])->name('admin.settings.system.update');

    Route::get('/settings/admob', [AdminController::class, 'admobSettings'])->name('admin.settings.admob');
    Route::post('/settings/admob', [AdminController::class, 'updateSettings'])->name('admin.settings.admob.update');

    Route::get('/settings/ai', [AdminController::class, 'aiSettings'])->name('admin.settings.ai');
    Route::post('/settings/ai', [AdminController::class, 'updateSettings'])->name('admin.settings.ai.update');

    Route::get('/settings/features', [AdminController::class, 'featuresSettings'])->name('admin.settings.features');
    Route::post('/settings/features', [AdminController::class, 'updateSettings'])->name('admin.settings.features.update');

    // AI Test Page
    Route::get('/ai-test', [AiEnhanceController::class, 'testPage'])->name('admin.ai.test');
    Route::post('/ai-test', [AiEnhanceController::class, 'testRun'])->name('admin.ai.test.run');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
