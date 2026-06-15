<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimatorController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\AiAdvisoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RatesController;
use App\Http\Controllers\Admin\EscalationController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HistoricalEnrichmentController;
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

    // How It Works - explanatory walkthrough, accessible to all authenticated users
    Route::get('/how-it-works', fn() => view('pages.how-it-works'))->name('how-it-works');

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

    // Forecast approval workflow - accessible to cost_manager and admin
    Route::post('/project/{projectId}/approve', [ProjectReportController::class, 'approve'])
        ->middleware('role:cost_manager,admin')
        ->name('project.approve');
    Route::post('/project/{projectId}/unapprove', [ProjectReportController::class, 'unapprove'])
        ->middleware('role:cost_manager,admin')
        ->name('project.unapprove');

    // Stage 3: AI Advisory tools for the Project Report - accessible to cost_manager and admin
    Route::post('/project/{projectId}/ai/summary', [AiAdvisoryController::class, 'summary'])
        ->middleware('role:cost_manager,admin')
        ->name('project.ai.summary');
    Route::post('/project/{projectId}/ai/similar-projects', [AiAdvisoryController::class, 'similarProjects'])
        ->middleware('role:cost_manager,admin')
        ->name('project.ai.similar-projects');
    Route::post('/project/{projectId}/ai/explain', [AiAdvisoryController::class, 'explain'])
        ->middleware('role:cost_manager,admin')
        ->name('project.ai.explain');
    Route::post('/project/{projectId}/ai/sense-check', [AiAdvisoryController::class, 'senseCheck'])
        ->middleware('role:cost_manager,admin')
        ->name('project.ai.sense-check');

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

        // Projects (Historical) - admin only
        Route::get('/projects/historical', [ProjectController::class, 'historical'])->name('admin.projects.historical');

        // Historical Project Reports (AC/PD code/transaction drill-down) - admin only
        Route::get('/reports/historical', [ProjectReportController::class, 'historicalIndex'])->name('project.reports-historical');
        Route::get('/project/{projectId}/report-historical', [ProjectReportController::class, 'historical'])->name('project.report-historical');

        // Rates
        Route::get('/rates', [RatesController::class, 'index'])->name('admin.rates.index');
        Route::post('/rates/recalculate', [RatesController::class, 'recalculate'])->name('admin.rates.recalculate');

        // Regions
        Route::put('/regions/{id}', [RegionController::class, 'update'])->name('admin.regions.update');

        // Escalation rates
        Route::get('/escalation', [EscalationController::class, 'index'])->name('admin.escalation.index');
        Route::post('/escalation', [EscalationController::class, 'store'])->name('admin.escalation.store');

        // Historical Enrichment
        Route::get('/historical-enrichment', [HistoricalEnrichmentController::class, 'index'])->name('admin.historical-enrichment.index');
        Route::get('/historical-enrichment/{id}', [HistoricalEnrichmentController::class, 'show'])->name('admin.historical-enrichment.show');
        Route::patch('/historical-enrichment/{id}', [HistoricalEnrichmentController::class, 'update'])->name('admin.historical-enrichment.update');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Forecast Projects - accessible to cost_manager and admin
    Route::middleware('role:cost_manager,admin')->prefix('admin')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('admin.projects.update');
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
