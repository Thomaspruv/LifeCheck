<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\EmotionTagController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\BreathingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\HomeScreenWidgetController;
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

    // Home Screen Widget
    Route::get('/widget', [HomeScreenWidgetController::class, 'widget'])->name('widget');
    Route::get('/api/widget-data', [HomeScreenWidgetController::class, 'apiData'])->name('widget.api-data');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{check_in}', [HistoryController::class, 'show'])->name('history.show');
    Route::get('/trends', [HistoryController::class, 'trends'])->name('trends');

    // Timeline
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');

    // Templates
    Route::resource('templates', TemplateController::class)->except(['show']);
    Route::post('/templates/{template}/set-default', [TemplateController::class, 'setDefault'])->name('templates.setDefault');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Streaks
    Route::get('/streaks', [StreakController::class, 'index'])->name('streaks.index');

    // Progression Tree
    Route::get('/progression', ProgressionController::class)->name('progression.index');

    // Insights
    Route::get('/insights', [InsightsController::class, 'index'])->name('insights.index');
    Route::get('/insights/history', [InsightsController::class, 'history'])->name('insights.history');
    Route::get('/insights/j7-comparison', [InsightsController::class, 'j7Comparison'])->name('insights.j7');

    // Export
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');

    // Challenges
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{challenge}/progress', [ChallengeController::class, 'progress'])->name('challenges.progress');
    Route::post('/challenges/{challenge}/pause', [ChallengeController::class, 'pause'])->name('challenges.pause');
    Route::post('/challenges/{challenge}/resume', [ChallengeController::class, 'resume'])->name('challenges.resume');
    Route::post('/challenges/{challenge}/fail', [ChallengeController::class, 'fail'])->name('challenges.fail');
    Route::delete('/challenges/{challenge}', [ChallengeController::class, 'destroy'])->name('challenges.destroy');

    // Goals (Objectifs)
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');

    // Emotion Tags
    Route::resource('tags', EmotionTagController::class)->except(['show']);
    Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
    Route::get('/goals/{goal}/edit', [GoalController::class, 'edit'])->name('goals.edit');
    Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    Route::post('/goals/{goal}/complete', [GoalController::class, 'complete'])->name('goals.complete');
    Route::post('/goals/{goal}/abandon', [GoalController::class, 'abandon'])->name('goals.abandon');
    Route::post('/goals/{goal}/reactivate', [GoalController::class, 'reactivate'])->name('goals.reactivate');
    Route::post('/goals/{goal}/milestones', [GoalController::class, 'addMilestone'])->name('goals.milestones.add');
    Route::post('/goals/{goal}/milestones/{milestone}/toggle', [GoalController::class, 'toggleMilestone'])->name('goals.milestones.toggle');
    Route::delete('/goals/{goal}/milestones/{milestone}', [GoalController::class, 'deleteMilestone'])->name('goals.milestones.delete');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');

    // Onboarding
    Route::get('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'postStep1'])->name('onboarding.postStep1');
    Route::get('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'postStep2'])->name('onboarding.postStep2');
    Route::get('/onboarding/step3', [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::get('/onboarding/step4', [OnboardingController::class, 'step4'])->name('onboarding.step4');
    Route::post('/onboarding/step4', [OnboardingController::class, 'postStep4'])->name('onboarding.postStep4');
    Route::post('/onboarding/store', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Breathing & Meditation
    Route::get('/breathing', [BreathingController::class, 'index'])->name('breathing.index');
    Route::get('/breathing/{exercise}', [BreathingController::class, 'show'])->name('breathing.show');
    Route::post('/breathing/complete', [BreathingController::class, 'complete'])->name('breathing.complete');
    Route::get('/breathing/history', [BreathingController::class, 'history'])->name('breathing.history');

    // Language Switch
    Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

    // Telegram linking
    Route::post('/telegram/generate-token', [\App\Http\Controllers\TelegramLinkController::class, 'generateToken'])
        ->name('telegram.generateToken');
    Route::post('/telegram/revoke', [\App\Http\Controllers\TelegramLinkController::class, 'revoke'])
        ->name('telegram.revoke');
});

require __DIR__.'/auth.php';
