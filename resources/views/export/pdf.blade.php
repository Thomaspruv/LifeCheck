<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LifeCheck — Export des données personnelles</title>
    <style>
        @page { margin: 20mm 15mm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.5;
        }
        h1 {
            font-size: 22pt;
            color: #4f46e5;
            margin-bottom: 4px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 8px;
        }
        h2 {
            font-size: 14pt;
            color: #4f46e5;
            margin-top: 24px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ddd;
        }
        h3 {
            font-size: 11pt;
            color: #666;
            margin-top: 16px;
            margin-bottom: 6px;
        }
        .meta {
            color: #888;
            font-size: 9pt;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9pt;
        }
        th {
            background: #4f46e5;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
        }
        tr:nth-child(even) td {
            background: #f9f9f9;
        }
        .section-info {
            background: #f0f0ff;
            border-left: 3px solid #4f46e5;
            padding: 8px 12px;
            margin: 6px 0;
            font-size: 9pt;
        }
        .empty {
            color: #999;
            font-style: italic;
            padding: 6px 0;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    <h1>LifeCheck — Export de données</h1>
    <div class="meta">
        <p>Utilisateur : {{ $user->name }} ({{ $user->email }})</p>
        <p>Généré le : {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Membre depuis : {{ $user->created_at->format('d/m/Y') }}</p>
        @if ($user->telegram_chat_id)
            <p>Telegram : lié (ID: {{ $user->telegram_chat_id }})</p>
        @endif
    </div>

    {{-- PROGRESSION --}}
    @if ($progression)
        <h2>Progression</h2>
        <table>
            <tr><td style="width:200px"><strong>Niveau</strong></td><td>{{ $progression->level }}</td></tr>
            <tr><td><strong>XP total</strong></td><td>{{ number_format($progression->total_xp) }}</td></tr>
            <tr><td><strong>XP Cohérence</strong></td><td>{{ number_format($progression->consistency_xp) }}</td></tr>
            <tr><td><strong>XP Bien-être</strong></td><td>{{ number_format($progression->wellbeing_xp) }}</td></tr>
            <tr><td><strong>XP Présence</strong></td><td>{{ number_format($progression->presence_xp) }}</td></tr>
            <tr><td><strong>XP Engagement</strong></td><td>{{ number_format($progression->engagement_xp) }}</td></tr>
        </table>
    @endif

    {{-- PARAMÈTRES --}}
    @if ($settings)
        <h2>Paramètres</h2>
        <table>
            <tr><td style="width:200px"><strong>Rappel</strong></td><td>{{ $settings->reminder_enabled ? 'Activé' : 'Désactivé' }}</td></tr>
            <tr><td><strong>Heure du rappel</strong></td><td>{{ $settings->checkin_reminder_time ?? '-' }}</td></tr>
            <tr><td><strong>Début de semaine</strong></td><td>{{ $settings->week_start === 'monday' ? 'Lundi' : 'Dimanche' }}</td></tr>
            <tr><td><strong>Thème</strong></td><td>{{ $settings->theme }}</td></tr>
            <tr><td><strong>Fuseau horaire</strong></td><td>{{ $settings->timezone }}</td></tr>
            <tr><td><strong>Langue</strong></td><td>{{ $settings->locale ?? 'fr' }}</td></tr>
        </table>
    @endif

    {{-- ÉMOTIONS / TAGS --}}
    <h2>Émotions / Tags personnalisés</h2>
    @if ($tags->isNotEmpty())
        <table>
            <tr><th>Nom</th><th>Couleur</th><th>Catégorie</th></tr>
            @foreach ($tags as $tag)
                <tr>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->color ?? '-' }}</td>
                    <td>{{ $tag->category ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">Aucun tag personnalisé créé.</p>
    @endif

    {{-- CHECK-INS --}}
    <div class="page-break"></div>
    <h2>Check-ins ({{ $checkins->count() }})</h2>
    @if ($checkins->isNotEmpty())
        <table>
            <tr><th>Date</th><th>Template</th><th>Questions / Réponses</th><th>Émotions</th><th>Notes</th></tr>
            @foreach ($checkins as $checkin)
                <tr>
                    <td style="white-space:nowrap">{{ $checkin->date->format('d/m/Y') }}</td>
                    <td>{{ $checkin->template?->name ?? '—' }}</td>
                    <td>
                        @foreach ($checkin->items as $item)
                            <span style="font-size:8pt">
                                {{ $item->templateItem?->label ?? 'Q#' . $item->template_item_id }}:
                                <strong>{{ $item->value }}</strong>
                            </span><br>
                        @endforeach
                    </td>
                    <td>{{ $checkin->emotionTags->pluck('name')->implode(', ') }}</td>
                    <td>{{ Str::limit($checkin->notes, 80) }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">Aucun check-in enregistré.</p>
    @endif

    {{-- BADGES --}}
    <h2>Badges / Streaks</h2>
    @if ($badges->isNotEmpty())
        <table>
            <tr><th>Nom du badge</th><th>Type</th><th>Obtenu le</th></tr>
            @foreach ($badges as $badge)
                <tr>
                    <td>{{ $badge->badge_name }}</td>
                    <td>{{ $badge->badge_type }}</td>
                    <td>{{ $badge->earned_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">Aucun badge obtenu.</p>
    @endif

    {{-- OBJECTIFS --}}
    <h2>Objectifs</h2>
    @if ($goals->isNotEmpty())
        @foreach ($goals as $goal)
            <h3>{{ $goal->title }}</h3>
            <div class="section-info">
                <strong>Statut :</strong> {{ $goal->status }} |
                <strong>Progression :</strong> {{ $goal->completed_milestones_count }}/{{ $goal->total_milestones_count }}
                ({{ $goal->progress_percent }}%)
                @if ($goal->target_date) | <strong>Cible :</strong> {{ $goal->target_date->format('d/m/Y') }}@endif
            </div>
            @if ($goal->description)
                <p style="margin:4px 0">{{ $goal->description }}</p>
            @endif
            @if ($goal->milestones->isNotEmpty())
                <table>
                    <tr><th>Étape</th><th>Statut</th></tr>
                    @foreach ($goal->milestones as $ms)
                        <tr>
                            <td>{{ $ms->title }}</td>
                            <td>{{ $ms->is_completed ? '✓ Complétée' : '○ En cours' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endforeach
    @else
        <p class="empty">Aucun objectif créé.</p>
    @endif

    {{-- DÉFIS PERSONNELS --}}
    <div class="page-break"></div>
    <h2>Défis personnels</h2>
    @if ($challenges->isNotEmpty())
        @foreach ($challenges as $challenge)
            @php $doneDays = $challenge->progress()->where('is_done', true)->count(); @endphp
            <h3>{{ $challenge->title }}</h3>
            <div class="section-info">
                <strong>Durée :</strong> {{ $challenge->duration_days }} jours |
                <strong>Statut :</strong> {{ $challenge->status }} |
                <strong>Progression :</strong> {{ $doneDays }}/{{ $challenge->duration_days }} jours réussis
            </div>
            @if ($challenge->description)
                <p style="margin:4px 0">{{ $challenge->description }}</p>
            @endif
        @endforeach
    @else
        <p class="empty">Aucun défi personnel créé.</p>
    @endif

    {{-- MÉDITATION --}}
    <h2>Méditation / Respiration</h2>
    @if ($meditations->isNotEmpty())
        <table>
            <tr><th>Exercice</th><th>Type</th><th>Durée (sec)</th><th>Terminé</th><th>Date</th></tr>
            @foreach ($meditations as $meditation)
                <tr>
                    <td>{{ $meditation->exercise_name }}</td>
                    <td>{{ $meditation->type }}</td>
                    <td>{{ $meditation->duration_seconds }}</td>
                    <td>{{ $meditation->completed ? 'Oui' : 'Non' }}</td>
                    <td>{{ $meditation->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">Aucune session de méditation enregistrée.</p>
    @endif

    {{-- INSIGHTS HEBDOMADAIRES --}}
    <h2>Insights hebdomadaires</h2>
    @if ($insights->isNotEmpty())
        <table>
            <tr><th>Semaine</th><th>Moy. humeur</th><th>Émotion dominante</th><th>Check-ins</th><th>Tendance</th><th>Résumé</th></tr>
            @foreach ($insights as $insight)
                <tr>
                    <td>{{ $insight->week_start->format('d/m/Y') }} — {{ $insight->week_end->format('d/m/Y') }}</td>
                    <td>{{ number_format($insight->avg_mood, 1) }}</td>
                    <td>{{ $insight->dominant_emotion ?? '-' }}</td>
                    <td>{{ $insight->checkin_count }}/{{ $insight->total_days }}</td>
                    <td>{{ $insight->trend ?? '-' }}</td>
                    <td>{{ Str::limit($insight->summary, 100) }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">Aucun insight généré.</p>
    @endif

    <div class="footer">
        Généré par LifeCheck le {{ now()->format('d/m/Y à H:i') }} — Données personnelles confidentielles
    </div>

</body>
</html>
