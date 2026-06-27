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
            width: 120px;
        }
        .empty {
            color: #9ca3af;
            font-style: italic;
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
    </div>

    <!-- Paramètres -->
    <h2>Paramètres</h2>
    @if ($settings)
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Rappel :</span> {{ $settings->reminder_enabled ? 'Activé' : 'Désactivé' }}</div>
        <div class="info-row"><span class="info-label">Heure du rappel :</span> {{ $settings->checkin_reminder_time ?? '—' }}</div>
        <div class="info-row"><span class="info-label">Début de semaine :</span> {{ $settings->week_start === 'monday' ? 'Lundi' : 'Dimanche' }}</div>
        <div class="info-row"><span class="info-label">Thème :</span> {{ $settings->theme }}</div>
        <div class="info-row"><span class="info-label">Fuseau horaire :</span> {{ $settings->timezone }}</div>
    </div>
    @else
    <p class="empty">Aucun paramètre configuré.</p>
    @endif

    <!-- Check-ins -->
    <h2>Check-ins</h2>
    @if ($checkins->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Template</th>
                <th>Notes</th>
                <th>Réponses</th>
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

    <!-- Insights -->
    @if ($insights->isNotEmpty())
    <div class="page-break"></div>
    <h2>Insights hebdomadaires</h2>
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
