<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimatorController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RatesController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Public pages — no authentication required
Route::get('/help',    fn() => view('pages.help'))->name('help');
Route::get('/terms',   fn() => view('pages.terms'))->name('terms');
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Estimator - accessible to cost_manager and admin
    Route::get('/estimator', [EstimatorController::class, 'index'])
        ->middleware('role:cost_manager,admin')
        ->name('estimator.index');
    Route::get('/estimator/{projectId}', [EstimatorController::class, 'show'])
        ->middleware('role:cost_manager,admin')
        ->name('estimator.show');
    Route::post('/estimator/{projectId}/save', [EstimatorController::class, 'saveEstimate'])
        ->middleware('role:cost_manager,admin')
        ->name('estimator.save');

    // Project Report (Forecast) - accessible to cost_manager and admin
    Route::get('/project/{projectId}/report', [ProjectReportController::class, 'show'])
        ->middleware('role:cost_manager,admin')
        ->name('project.report');

    // Project Reports (Historical) - index - accessible to cost_manager and admin
    Route::get('/reports/historical', [ProjectReportController::class, 'historicalIndex'])
        ->middleware('role:cost_manager,admin')
        ->name('project.reports-historical');

    // Project Report (Historical) - detail - accessible to cost_manager and admin
    Route::get('/project/{projectId}/report-historical', [ProjectReportController::class, 'historical'])
        ->middleware('role:cost_manager,admin')
        ->name('project.report-historical');

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
        Route::get('/projects/historical', [ProjectController::class, 'historical'])->name('admin.projects.historical');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('admin.projects.update');

        // Rates
        Route::get('/rates', [RatesController::class, 'index'])->name('admin.rates.index');
        Route::post('/rates/recalculate', [RatesController::class, 'recalculate'])->name('admin.rates.recalculate');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
