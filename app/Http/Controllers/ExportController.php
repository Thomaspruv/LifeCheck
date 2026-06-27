<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\StreakBadge;
use App\Models\UserSetting;
use App\Models\WeeklyInsight;
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

        return view('export.index', compact('checkinCount', 'badgeCount', 'insightCount'));
    }

    /**
     * Exporte les données personnelles au format CSV.
     */
    public function csv()
    {
        $user = Auth::user();

        // Collecter toutes les données
        $checkins = CheckIn::where('user_id', $user->id)
            ->with(['template', 'items.templateItem'])
            ->orderBy('date', 'desc')
            ->get();

        $badges = StreakBadge::where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->get();

        $insights = WeeklyInsight::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $settings = UserSetting::where('user_id', $user->id)->first();

        $filename = 'lifecheck-export-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($user, $checkins, $badges, $insights, $settings) {
            $handle = fopen('php://output', 'w');

            // BOM pour Excel
            fprintf($handle, "\xEF\xBB\xBF");

            // === SECTION PROFIL ===
            fputcsv($handle, ['=== PROFIL ===']);
            fputcsv($handle, ['Nom', $user->name]);
            fputcsv($handle, ['Email', $user->email]);
            fputcsv($handle, ['Membre depuis', $user->created_at->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            // === SECTION PARAMÈTRES ===
            if ($settings) {
                fputcsv($handle, ['=== PARAMÈTRES ===']);
                fputcsv($handle, ['Rappel activé', $settings->reminder_enabled ? 'Oui' : 'Non']);
                fputcsv($handle, ['Heure du rappel', $settings->checkin_reminder_time ?? '-']);
                fputcsv($handle, ['Début de semaine', $settings->week_start === 'monday' ? 'Lundi' : 'Dimanche']);
                fputcsv($handle, ['Thème', $settings->theme]);
                fputcsv($handle, ['Fuseau horaire', $settings->timezone]);
                fputcsv($handle, []);
            }

            // === SECTION CHECK-INS ===
            fputcsv($handle, ['=== CHECK-INS ===']);
            fputcsv($handle, ['Date', 'Template', 'Notes', 'Questions/Réponses']);

            foreach ($checkins as $checkin) {
                $details = '';
                foreach ($checkin->items as $item) {
                    $label = $item->templateItem?->label ?? 'Question #' . $item->template_item_id;
                    $details .= $label . ': ' . $item->value . ' | ';
                }
                fputcsv($handle, [
                    $checkin->date->format('d/m/Y'),
                    $checkin->template?->name ?? '—',
                    $checkin->notes ?? '',
                    rtrim($details, ' | '),
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
            ->with(['template', 'items.templateItem'])
            ->orderBy('date', 'desc')
            ->get();

        $badges = StreakBadge::where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->get();

        $insights = WeeklyInsight::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $settings = UserSetting::where('user_id', $user->id)->first();

        $pdf = Pdf::loadView('export.pdf', compact(
            'user', 'checkins', 'badges', 'insights', 'settings'
        ));

        $filename = 'lifecheck-export-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
