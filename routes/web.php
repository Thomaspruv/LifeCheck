<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Check-in
    Route::get('/checkin', [CheckInController::class, 'create'])->name('checkin.create');
    Route::post('/checkin', [CheckInController::class, 'store'])->name('checkin.store');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{check_in}', [HistoryController::class, 'show'])->name('history.show');
    Route::get('/trends', [HistoryController::class, 'trends'])->name('trends');

    // Templates
    Route::resource('templates', TemplateController::class)->except(['show']);
    Route::post('/templates/{template}/set-default', [TemplateController::class, 'setDefault'])->name('templates.setDefault');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Streaks
    Route::get('/streaks', [StreakController::class, 'index'])->name('streaks.index');

    // Insights
    Route::get('/insights', [InsightsController::class, 'index'])->name('insights.index');
    Route::get('/insights/history', [InsightsController::class, 'history'])->name('insights.history');
    Route::get('/insights/j7-comparison', [InsightsController::class, 'j7Comparison'])->name('insights.j7');

    // Export
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');

    // Onboarding
    Route::get('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'postStep1'])->name('onboarding.postStep1');
    Route::get('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'postStep2'])->name('onboarding.postStep2');
    Route::get('/onboarding/step3', [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::post('/onboarding/store', [OnboardingController::class, 'store'])->name('onboarding.store');
});

require __DIR__.'/auth.php';
