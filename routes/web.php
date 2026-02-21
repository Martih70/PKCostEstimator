<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimatorController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RatesController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Estimator - accessible to cost_manager and admin
    Route::get('/estimator', [EstimatorController::class, 'index'])
        ->middleware('role:cost_manager,admin')
        ->name('estimator.index');

    // Analytics - accessible to reviewer and admin
    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('role:reviewer,admin')
        ->name('analytics.index');

    // Admin routes - accessible only to admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('admin.transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('admin.transactions.store');
        Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('admin.transactions.edit');
        Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('admin.transactions.update');
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('admin.transactions.destroy');
        Route::get('/transactions/import', [TransactionController::class, 'importForm'])->name('admin.transactions.import');
        Route::post('/transactions/import', [TransactionController::class, 'processImport'])->name('admin.transactions.process-import');

        // Projects
        Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('admin.projects.update');

        // Rates
        Route::get('/rates', [RatesController::class, 'index'])->name('admin.rates.index');
        Route::post('/rates/recalculate', [RatesController::class, 'recalculate'])->name('admin.rates.recalculate');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
