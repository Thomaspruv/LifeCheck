<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LifeCheck - Export {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.6;
        }
        h1 {
            font-size: 20px;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 5px;
        }
        h2 {
            font-size: 14px;
            color: #4f46e5;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 20px;
        }
        h3 {
            font-size: 11px;
            color: #374151;
            margin-top: 12px;
            margin-bottom: 4px;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .info-grid {
            margin: 8px 0;
        }
        .info-row {
            padding: 3px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }
        .empty {
            color: #9ca3af;
            font-style: italic;
        }
        .badge-progress {
            display: inline-block;
            background: #e0e7ff;
            color: #4338ca;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        .page-break {
            page-break-before: always;
        }
        .section-summary {
            margin: 6px 0;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <h1>LifeCheck</h1>
    <p class="subtitle">Export de vos données personnelles — Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <!-- Profil -->
    <h2>Profil</h2>
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Nom :</span> {{ $user->name }}</div>
        <div class="info-row"><span class="info-label">Email :</span> {{ $user->email }}</div>
        <div class="info-row"><span class="info-label">Membre depuis :</span> {{ $user->created_at->format('d/m/Y H:i') }}</div>
        @if ($user->telegram_chat_id)
        <div class="info-row"><span class="info-label">Telegram lié :</span> Oui</div>
        @endif
    </div>

    <!-- Progression -->
    <h2>Progression</h2>
    @if ($progression)
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Niveau :</span> {{ $progression->level }}</div>
        <div class="info-row"><span class="info-label">XP total :</span> {{ $progression->total_xp }}</div>
        <div class="info-row"><span class="info-label">XP Cohérence :</span> {{ $progression->consistency_xp }}</div>
        <div class="info-row"><span class="info-label">XP Bien-être :</span> {{ $progression->wellbeing_xp }}</div>
        <div class="info-row"><span class="info-label">XP Présence :</span> {{ $progression->presence_xp }}</div>
        <div class="info-row"><span class="info-label">XP Engagement :</span> {{ $progression->engagement_xp }}</div>
    </div>
    @else
    <p class="empty">Aucune donnée de progression disponible.</p>
    @endif

    <!-- Paramètres -->
    <h2>Paramètres</h2>
    @if ($settings)
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Rappel :</span> {{ $settings->reminder_enabled ? 'Activé' : 'Désactivé' }}</div>
        <div class="info-row"><span class="info-label">Heure du rappel :</span> {{ $settings->checkin_reminder_time ?? '—' }}</div>
        <div class="info-row"><span class="info-label">Début de semaine :</span> {{ $settings->week_start === 'monday' ? 'Lundi' : 'Dimanche' }}</div>
        <div class="info-row"><span class="info-label">Thème :</span> {{ $settings->theme }}</div>
        <div class="info-row"><span class="info-label">Fuseau horaire :</span> {{ $settings->timezone }}</div>
        <div class="info-row"><span class="info-label">Langue :</span> {{ $settings->locale ?? 'fr' }}</div>
    </div>
    @else
    <p class="empty">Aucun paramètre configuré.</p>
    @endif

    <!-- Émotions / Tags -->
    <h2>Émotions / Tags personnalisés</h2>
    @if ($tags->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Couleur</th>
                <th>Catégorie</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->color ?? '—' }}</td>
                <td>{{ $tag->category ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucun tag personnalisé créé.</p>
    @endif

    <!-- Check-ins -->
    <h2>Check-ins</h2>
    @if ($checkins->isNotEmpty())
    <p class="section-summary">{{ $checkins->count() }} check-ins enregistrés.</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Template</th>
                <th>Notes</th>
                <th>Réponses</th>
                <th>Émotions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($checkins as $checkin)
            <tr>
                <td>{{ $checkin->date->format('d/m/Y') }}</td>
                <td>{{ $checkin->template?->name ?? '—' }}</td>
                <td>{{ $checkin->notes ?? '—' }}</td>
                <td>
                    @php
                        $answers = $checkin->items->map(function ($item) {
                            $label = $item->templateItem?->label ?? 'Question #' . $item->template_item_id;
                            return $label . ': ' . $item->value;
                        })->implode('; ');
                    @endphp
                    {{ $answers ?: '—' }}
                </td>
                <td>
                    @php
                        $emotions = $checkin->emotionTags->pluck('name')->implode(', ');
                    @endphp
                    {{ $emotions ?: '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucun check-in enregistré.</p>
    @endif

    <!-- Badges -->
    <h2>Badges et Streaks</h2>
    @if ($badges->isNotEmpty())
    <p class="section-summary">{{ $badges->count() }} badges obtenus.</p>
    <table>
        <thead>
            <tr>
                <th>Nom du badge</th>
                <th>Type</th>
                <th>Obtenu le</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($badges as $badge)
            <tr>
                <td>{{ $badge->badge_name }}</td>
                <td>{{ $badge->badge_type }}</td>
                <td>{{ $badge->earned_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucun badge obtenu.</p>
    @endif

    <!-- Objectifs -->
    <h2>Objectifs personnels</h2>
    @if ($goals->isNotEmpty())
    <p class="section-summary">{{ $goals->count() }} objectifs créés.</p>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Statut</th>
                <th>Progression</th>
                <th>Jalons</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($goals as $goal)
            <tr>
                <td>{{ $goal->title }}</td>
                <td>
                    @switch($goal->status)
                        @case('active') Actif @break
                        @case('completed') Terminé @break
                        @case('abandoned') Abandonné @break
                        @default {{ $goal->status }}
                    @endswitch
                </td>
                <td>
                    <span class="badge-progress">{{ $goal->progress_percent }}%</span>
                    ({{ $goal->completed_milestones_count }}/{{ $goal->total_milestones_count }})
                </td>
                <td style="font-size: 8px;">
                    @foreach ($goal->milestones as $ms)
                        {{ $ms->is_completed ? '✓' : '○' }} {{ $ms->title }}@if (!$loop->last)<br>@endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucun objectif créé.</p>
    @endif

    <!-- Défis personnels -->
    <h2>Défis personnels</h2>
    @if ($challenges->isNotEmpty())
    <p class="section-summary">{{ $challenges->count() }} défis créés.</p>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Durée</th>
                <th>Statut</th>
                <th>Progression</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($challenges as $challenge)
            @php
                $doneDays = $challenge->progress()->where('is_done', true)->count();
            @endphp
            <tr>
                <td>{{ $challenge->title }}</td>
                <td>{{ $challenge->duration_days }} jours</td>
                <td>
                    @switch($challenge->status)
                        @case('active') Actif @break
                        @case('completed') Terminé @break
                        @case('paused') En pause @break
                        @case('failed') Échoué @break
                        @default {{ $challenge->status }}
                    @endswitch
                </td>
                <td>
                    <span class="badge-progress">{{ $doneDays }}/{{ $challenge->duration_days }}</span>
                    ({{ $challenge->duration_days > 0 ? round(($doneDays / $challenge->duration_days) * 100) : 0 }}%)
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucun défi personnel créé.</p>
    @endif

    <!-- Méditation / Respiration -->
    <h2>Méditation &amp; Respiration</h2>
    @if ($meditations->isNotEmpty())
    <p class="section-summary">{{ $meditations->count() }} sessions enregistrées.</p>
    <table>
        <thead>
            <tr>
                <th>Exercice</th>
                <th>Type</th>
                <th>Durée</th>
                <th>Terminé</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meditations as $meditation)
            <tr>
                <td>{{ $meditation->exercise_name }}</td>
                <td>{{ $meditation->type }}</td>
                <td>{{ gmdate('i:s', $meditation->duration_seconds) }}</td>
                <td>{{ $meditation->completed ? 'Oui' : 'Non' }}</td>
                <td>{{ $meditation->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Aucune session de méditation enregistrée.</p>
    @endif

    <!-- Insights hebdomadaires -->
    @if ($insights->isNotEmpty())
    <div class="page-break"></div>
    <h2>Insights hebdomadaires</h2>
    <p class="section-summary">{{ $insights->count() }} semaines analysées.</p>
    <table>
        <thead>
            <tr>
                <th>Semaine</th>
                <th>Moy. humeur</th>
                <th>Émotion</th>
                <th>Check-ins</th>
                <th>Tendance</th>
                <th>Résumé</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($insights as $insight)
            <tr>
                <td>{{ $insight->week_start->format('d/m/Y') }} — {{ $insight->week_end->format('d/m/Y') }}</td>
                <td>{{ number_format($insight->avg_mood, 1) }}</td>
                <td>{{ $insight->dominant_emotion ?? '—' }}</td>
                <td>{{ $insight->checkin_count }}/{{ $insight->total_days }}</td>
                <td>{{ $insight->trend ?? '—' }}</td>
                <td>{{ $insight->summary ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Généré par LifeCheck le {{ now()->format('d/m/Y à H:i:s') }}
    </div>
</body>
</html>
