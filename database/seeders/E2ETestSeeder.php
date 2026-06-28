<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\UserProgression;
use App\Models\Template;
use App\Models\TemplateItem;
use App\Models\CheckIn;
use App\Models\CheckInItem;
use App\Models\EmotionTag;
use App\Models\Goal;
use App\Models\GoalMilestone;
use App\Models\PersonalChallenge;
use App\Models\ChallengeProgress;
use App\Models\BreathingExercise;
use App\Models\StreakBadge;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class E2ETestSeeder extends Seeder
{
    /**
     * Seed the application's database with deterministic test data.
     */
    public function run(): void
    {
        // ── Utilisateur de test ──
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@lifecheck.app',
            'password' => bcrypt('Password123!'),
        ]);

        // ── Settings ──
        UserSetting::create([
            'user_id' => $user->id,
            'locale' => 'fr',
            'week_start' => 'monday',
            'reminder_enabled' => true,
            'checkin_reminder_time' => '20:00',
            'theme' => 'light',
            'timezone' => 'Europe/Paris',
        ]);

        // ── Progression ──
        UserProgression::create([
            'user_id' => $user->id,
            'total_xp' => 450,
            'level' => 3,
            'consistency_xp' => 200,
            'wellbeing_xp' => 150,
            'presence_xp' => 50,
            'engagement_xp' => 50,
        ]);

        // ── Template de check-in par défaut ──
        $template = Template::create([
            'user_id' => $user->id,
            'name' => 'Journal de base',
            'is_default' => true,
        ]);

        $items = [
            ['label' => 'Mood', 'input_type' => 'emoji', 'position' => 0],
            ['label' => 'Sleep', 'input_type' => 'slider', 'position' => 1],
            ['label' => 'Notes', 'input_type' => 'text', 'position' => 2],
        ];

        foreach ($items as $item) {
            TemplateItem::create([
                'template_id' => $template->id,
                'label' => $item['label'],
                'input_type' => $item['input_type'],
                'position' => $item['position'],
            ]);
        }

        // ── Check-in aujourd'hui ──
        $today = Carbon::today();
        $checkin = CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'date' => $today->subDay()->format('Y-m-d'), // hier
            'notes' => 'Bonne journée !',
            'sentiment_score' => 0.7,
            'sentiment_label' => 'positive',
        ]);

        CheckInItem::create([
            'check_in_id' => $checkin->id,
            'template_item_id' => $items[0]['position'] === 0 ? $template->items()->first()->id : null,
            'value' => '4',
        ]);

        // ── Emotion Tags ──
        $tag = EmotionTag::create([
            'user_id' => $user->id,
            'name' => 'Heureux',
            'color' => '#10B981',
            'icon' => '😊',
        ]);

        $checkin->emotionTags()->attach($tag->id);

        // ── Streak de 5 jours ──
        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::today()->subDays($i + 1);
            $pastCheckin = CheckIn::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'date' => $date->format('Y-m-d'),
                'sentiment_score' => 0.5 + ($i * 0.05),
                'sentiment_label' => 'positive',
            ]);
            $pastCheckin->emotionTags()->attach($tag->id);
        }

        StreakBadge::create([
            'user_id' => $user->id,
            'badge_type' => 'streak',
            'badge_name' => '5 jours de suite',
            'earned_at' => Carbon::today()->subDay(),
        ]);

        // ── Goal actif ──
        $goal = Goal::create([
            'user_id' => $user->id,
            'title' => 'Méditer 10 min par jour',
            'description' => 'Atteindre 30 jours de méditation continue',
            'target_date' => Carbon::today()->addDays(30),
            'status' => 'active',
        ]);

        GoalMilestone::create([
            'goal_id' => $goal->id,
            'title' => '7 jours',
            'description' => 'Première semaine',
            'order' => 1,
        ]);
        GoalMilestone::create([
            'goal_id' => $goal->id,
            'title' => '30 jours',
            'description' => 'Objectif atteint !',
            'order' => 2,
        ]);

        // ── Challenge actif ──
        $challenge = PersonalChallenge::create([
            'user_id' => $user->id,
            'title' => 'Pas de sucre pendant 7 jours',
            'description' => 'Éviter les sucres ajoutés',
            'duration_days' => 7,
            'status' => 'active',
        ]);

        ChallengeProgress::create([
            'personal_challenge_id' => $challenge->id,
            'date' => Carbon::today()->subDay()->format('Y-m-d'),
            'is_done' => true,
        ]);

        // ── Exercises de respiration ──
        BreathingExercise::create([
            'name' => 'Respiration 4-7-8',
            'benefits' => 'Calme le système nerveux',
            'type' => 'guided',
            'category' => 'relaxation',
            'pattern_data' => json_encode([
                'inhale' => 4,
                'hold' => 7,
                'exhale' => 8,
                'cycles' => 4,
            ]),
            'duration_options' => json_encode([60, 120, 300]),
            'icon' => 'lung',
            'color' => '#818CF8',
        ]);
    }
}
