<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\StreakBadge;
use App\Models\UserSetting;
use App\Models\WeeklyInsight;
use App\Models\Goal;
use App\Models\PersonalChallenge;
use App\Models\MeditationSession;
use App\Models\UserProgression;
use App\Models\EmotionTag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    /**
     * Affiche la page d'export des données personnelles.
     */
    public function index()
    {
        $user = Auth::user();
        $checkinCount = CheckIn::where('user_id', $user->id)->count();
        $badgeCount = StreakBadge::where('user_id', $user->id)->count();
        $insightCount = WeeklyInsight::where('user_id', $user->id)->count();
        $goalCount = Goal::where('user_id', $user->id)->count();
        $challengeCount = PersonalChallenge::where('user_id', $user->id)->count();
        $meditationCount = MeditationSession::where('user_id', $user->id)->count();

        return view('export.index', compact(
            'checkinCount', 'badgeCount', 'insightCount',
            'goalCount', 'challengeCount', 'meditationCount'
        ));
    }

    /**
     * Exporte les données personnelles au format CSV.
     */
    public function csv()
    {
        $user = Auth::user();

        // Collecter toutes les données
        $checkins = CheckIn::where('user_id', $user->id)
            ->with(['template', 'items.templateItem', 'emotionTags'])
            ->orderBy('date', 'desc')
            ->get();

        $badges = StreakBadge::where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->get();

        $insights = WeeklyInsight::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $settings = UserSetting::where('user_id', $user->id)->first();

        $goals = Goal::where('user_id', $user->id)
            ->with('milestones')
            ->orderBy('created_at', 'desc')
            ->get();

        $challenges = PersonalChallenge::where('user_id', $user->id)
            ->with('progress')
            ->orderBy('created_at', 'desc')
            ->get();

        $meditations = MeditationSession::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $progression = UserProgression::where('user_id', $user->id)->first();

        $tags = EmotionTag::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $filename = 'lifecheck-export-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($user, $checkins, $badges, $insights, $settings, $goals, $challenges, $meditations, $progression, $tags) {
            $handle = fopen('php://output', 'w');

            // BOM pour Excel
            fprintf($handle, "\xEF\xBB\xBF");

            // === SECTION PROFIL ===
            fputcsv($handle, ['=== PROFIL ===']);
            fputcsv($handle, ['Nom', $user->name]);
            fputcsv($handle, ['Email', $user->email]);
            fputcsv($handle, ['Membre depuis', $user->created_at->format('d/m/Y H:i')]);
            if ($user->telegram_chat_id) {
                fputcsv($handle, ['Telegram lié', 'Oui (ID: ' . $user->telegram_chat_id . ')']);
            }
            fputcsv($handle, []);

            // === SECTION PROGRESSION (XP / LEVEL) ===
            if ($progression) {
                fputcsv($handle, ['=== PROGRESSION ===']);
                fputcsv($handle, ['Niveau', $progression->level]);
                fputcsv($handle, ['XP total', $progression->total_xp]);
                fputcsv($handle, ['XP Cohérence', $progression->consistency_xp]);
                fputcsv($handle, ['XP Bien-être', $progression->wellbeing_xp]);
                fputcsv($handle, ['XP Présence', $progression->presence_xp]);
                fputcsv($handle, ['XP Engagement', $progression->engagement_xp]);
                fputcsv($handle, []);
            }

            // === SECTION PARAMÈTRES ===
            if ($settings) {
                fputcsv($handle, ['=== PARAMÈTRES ===']);
                fputcsv($handle, ['Rappel activé', $settings->reminder_enabled ? 'Oui' : 'Non']);
                fputcsv($handle, ['Heure du rappel', $settings->checkin_reminder_time ?? '-']);
                fputcsv($handle, ['Début de semaine', $settings->week_start === 'monday' ? 'Lundi' : 'Dimanche']);
                fputcsv($handle, ['Thème', $settings->theme]);
                fputcsv($handle, ['Fuseau horaire', $settings->timezone]);
                fputcsv($handle, ['Langue', $settings->locale ?? 'fr']);
                fputcsv($handle, []);
            }

            // === SECTION ÉMOTIONS / TAGS ===
            fputcsv($handle, ['=== ÉMOTIONS / TAGS ===']);
            fputcsv($handle, ['Nom', 'Couleur', 'Catégorie']);
            foreach ($tags as $tag) {
                fputcsv($handle, [
                    $tag->name,
                    $tag->color ?? '-',
                    $tag->category ?? '-',
                ]);
            }
            if ($tags->isEmpty()) {
                fputcsv($handle, ['Aucun tag personnalisé créé.']);
            }
            fputcsv($handle, []);

            // === SECTION CHECK-INS ===
            fputcsv($handle, ['=== CHECK-INS ===']);
            fputcsv($handle, ['Date', 'Template', 'Notes', 'Questions/Réponses', 'Émotions']);

            foreach ($checkins as $checkin) {
                $details = '';
                foreach ($checkin->items as $item) {
                    $label = $item->templateItem?->label ?? 'Question #' . $item->template_item_id;
                    $details .= $label . ': ' . $item->value . ' | ';
                }
                $emotions = $checkin->emotionTags->pluck('name')->implode(', ');
                fputcsv($handle, [
                    $checkin->date->format('d/m/Y'),
                    $checkin->template?->name ?? '—',
                    $checkin->notes ?? '',
                    rtrim($details, ' | '),
                    $emotions ?: '',
                ]);
            }

            if ($checkins->isEmpty()) {
                fputcsv($handle, ['Aucun check-in enregistré.']);
            }

            fputcsv($handle, []);

            // === SECTION BADGES ===
            fputcsv($handle, ['=== BADGES / STREAKS ===']);
            fputcsv($handle, ['Nom du badge', 'Type', 'Obtenu le']);

            foreach ($badges as $badge) {
                fputcsv($handle, [
                    $badge->badge_name,
                    $badge->badge_type,
                    $badge->earned_at->format('d/m/Y H:i'),
                ]);
            }

            if ($badges->isEmpty()) {
                fputcsv($handle, ['Aucun badge obtenu.']);
            }

            fputcsv($handle, []);

            // === SECTION OBJECTIFS ===
            fputcsv($handle, ['=== OBJECTIFS ===']);
            fputcsv($handle, ['Titre', 'Description', 'Statut', 'Date cible', 'Démarré le', 'Terminé le', 'Progression']);

            foreach ($goals as $goal) {
                $milestoneStr = $goal->milestones->map(function ($ms) {
                    return ($ms->is_completed ? '✓' : '○') . ' ' . $ms->title;
                })->implode(' | ');

                fputcsv($handle, [
                    $goal->title,
                    $goal->description ?? '',
                    $goal->status,
                    $goal->target_date?->format('d/m/Y') ?? '-',
                    $goal->started_at?->format('d/m/Y') ?? '-',
                    $goal->completed_at?->format('d/m/Y H:i') ?? '-',
                    $goal->completed_milestones_count . '/' . $goal->total_milestones_count . ' (' . $goal->progress_percent . '%)',
                ]);
            }

            if ($goals->isEmpty()) {
                fputcsv($handle, ['Aucun objectif créé.']);
            }

            fputcsv($handle, []);

            // === SECTION DÉFIS PERSONNELS ===
            fputcsv($handle, ['=== DÉFIS PERSONNELS ===']);
            fputcsv($handle, ['Titre', 'Description', 'Durée (jours)', 'Statut', 'Démarré le', 'Terminé le', 'Jours réussis']);

            foreach ($challenges as $challenge) {
                $doneDays = $challenge->progress()->where('is_done', true)->count();
                fputcsv($handle, [
                    $challenge->title,
                    $challenge->description ?? '',
                    $challenge->duration_days,
                    $challenge->status,
                    $challenge->started_at?->format('d/m/Y') ?? '-',
                    $challenge->completed_at?->format('d/m/Y H:i') ?? '-',
                    $doneDays . '/' . $challenge->duration_days,
                ]);
            }

            if ($challenges->isEmpty()) {
                fputcsv($handle, ['Aucun défi personnel créé.']);
            }

            fputcsv($handle, []);

            // === SECTION MÉDITATION / RESPIRATION ===
            fputcsv($handle, ['=== MÉDITATION / RESPIRATION ===']);
            fputcsv($handle, ['Exercice', 'Type', 'Durée (sec)', 'Terminé', 'Date']);

            foreach ($meditations as $meditation) {
                fputcsv($handle, [
                    $meditation->exercise_name,
                    $meditation->type,
                    $meditation->duration_seconds,
                    $meditation->completed ? 'Oui' : 'Non',
                    $meditation->created_at->format('d/m/Y H:i'),
                ]);
            }

            if ($meditations->isEmpty()) {
                fputcsv($handle, ['Aucune session de méditation enregistrée.']);
            }

            fputcsv($handle, []);

            // === SECTION INSIGHTS HEBDOMADAIRES ===
            fputcsv($handle, ['=== INSIGHTS HEBDOMADAIRES ===']);
            fputcsv($handle, ['Semaine du', 'Semaine au', 'Moy. humeur', 'Émotion dominante', 'Check-ins', 'Tendance', 'Résumé']);

            foreach ($insights as $insight) {
                fputcsv($handle, [
                    $insight->week_start->format('d/m/Y'),
                    $insight->week_end->format('d/m/Y'),
                    number_format($insight->avg_mood, 1),
                    $insight->dominant_emotion ?? '-',
                    $insight->checkin_count . '/' . $insight->total_days,
                    $insight->trend ?? '-',
                    $insight->summary ?? '',
                ]);
            }

            if ($insights->isEmpty()) {
                fputcsv($handle, ['Aucun insight généré.']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporte les données personnelles au format PDF.
     */
    public function pdf()
    {
        $user = Auth::user();

        $checkins = CheckIn::where('user_id', $user->id)
            ->with(['template', 'items.templateItem', 'emotionTags'])
            ->orderBy('date', 'desc')
            ->get();

        $badges = StreakBadge::where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->get();

        $insights = WeeklyInsight::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $settings = UserSetting::where('user_id', $user->id)->first();

        $goals = Goal::where('user_id', $user->id)
            ->with('milestones')
            ->orderBy('created_at', 'desc')
            ->get();

        $challenges = PersonalChallenge::where('user_id', $user->id)
            ->with('progress')
            ->orderBy('created_at', 'desc')
            ->get();

        $meditations = MeditationSession::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $progression = UserProgression::where('user_id', $user->id)->first();

        $tags = EmotionTag::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('export.pdf', compact(
            'user', 'checkins', 'badges', 'insights', 'settings',
            'goals', 'challenges', 'meditations', 'progression', 'tags'
        ));

        $filename = 'lifecheck-export-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
