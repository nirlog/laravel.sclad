<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\App\AnalyticsWebController;
use App\Http\Controllers\Web\App\ContractorWebController;
use App\Http\Controllers\Web\App\DashboardController;
use App\Http\Controllers\Web\App\InventoryWebController;
use App\Http\Controllers\Web\App\MaterialPurchaseWebController;
use App\Http\Controllers\Web\App\MaterialWebController;
use App\Http\Controllers\Web\App\MaterialWriteOffWebController;
use App\Http\Controllers\Web\App\OperationsController;
use App\Http\Controllers\Web\App\ServiceEntryWebController;
use App\Http\Controllers\Web\App\SettingsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/app');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth'])
    ->prefix('app')
    ->name('app.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/dashboard', DashboardController::class)->name('dashboard.alias');

        Route::get('operations', OperationsController::class)->name('operations.index');
        Route::get('operations/create', [OperationsController::class, 'create'])->name('operations.create');

        Route::resource('materials', MaterialWebController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::resource('purchases', MaterialPurchaseWebController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::resource('write-offs', MaterialWriteOffWebController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])->parameters(['write-offs' => 'writeOff']);
        Route::resource('services', ServiceEntryWebController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])->parameters(['services' => 'service']);
        Route::resource('contractors', ContractorWebController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::get('inventory', InventoryWebController::class)->name('inventory.index');
        Route::get('inventory-adjustments/create', [InventoryWebController::class, 'createAdjustment'])->name('inventory.adjustments.create');
        Route::post('inventory-adjustments', [InventoryWebController::class, 'storeAdjustment'])->name('inventory.adjustments.store');

        Route::get('analytics', AnalyticsWebController::class)->name('analytics.index');
        Route::get('settings', SettingsController::class)->name('settings.index');
    });
