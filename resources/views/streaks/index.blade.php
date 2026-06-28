<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Streaks')]]">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏆 Streaks & Badges
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (count($awarded) > 0)
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    @foreach ($awarded as $badge)
                        <p>🎉 Nouveau badge débloqué : <strong>{{ $badge['badge_name'] }}</strong> !</p>
                    @endforeach
                </div>
            @endif

            <!-- Current Streak + Best Streak -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <p class="text-6xl font-bold text-orange-500">{{ $currentStreak }}</p>
                    <p class="text-4xl mt-2">🔥</p>
                    <p class="text-sm text-gray-500 mt-2">Streak actuel</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <p class="text-6xl font-bold text-yellow-500">{{ $bestStreak }}</p>
                    <p class="text-4xl mt-2">🏅</p>
                    <p class="text-sm text-gray-500 mt-2">Meilleur streak</p>
                </div>
            </div>

            <!-- Calendar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('streaks.index', ['year' => $prevYear, 'month' => $prevMonth]) }}"
                       class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm transition">
                        ←
                    </a>
                    <h3 class="font-semibold text-gray-800">{{ $monthName }} {{ $year }}</h3>
                    @if ($year < now()->year || ($year == now()->year && $month < now()->month))
                        <a href="{{ route('streaks.index', ['year' => $nextYear, 'month' => $nextMonth]) }}"
                           class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm transition">
                            →
                        </a>
                    @else
                        <span class="px-3 py-1 bg-gray-100 rounded text-sm text-gray-400 cursor-default">→</span>
                    @endif
                </div>

                <!-- Day-of-week headers -->
                <div class="grid grid-cols-7 gap-1 text-center text-xs text-gray-400 mb-2">
                    <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
                </div>

                <!-- Calendar grid with proper day-of-week alignment -->
                @php
                    $firstDayOfWeek = \Carbon\Carbon::parse(sprintf('%04d-%02d-01', $year, $month))->dayOfWeek;
                    // Carbon: 0=Sun, 1=Mon...6=Sat. We want Mon=0...Sun=6
                    $padding = ($firstDayOfWeek === 0) ? 6 : $firstDayOfWeek - 1;
                @endphp

                <div class="grid grid-cols-7 gap-1">
                    @for ($i = 0; $i < $padding; $i++)
                        <div></div>
                    @endfor

                    @foreach ($calendar as $cell)
                        @php
                            $dotColor = $cell['isFuture'] ? 'bg-gray-100' : ($cell['isChecked'] ? 'bg-green-500' : 'bg-gray-200');
                            $borderClass = $cell['isToday'] ? 'ring-2 ring-indigo-400' : '';
                        @endphp
                        <div class="aspect-square flex items-center justify-center rounded-full {{ $dotColor }} {{ $borderClass }} text-xs {{ $cell['isFuture'] ? 'text-gray-300' : ($cell['isChecked'] ? 'text-white font-medium' : 'text-gray-400') }}">
                            {{ $cell['day'] }}
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Checké
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-gray-200 inline-block"></span> Non-checké
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-gray-100 inline-block"></span> Futur
                    </div>
                </div>
            </div>

            <!-- Milestones Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-gray-800 mb-4">🎯 Paliers de récompense</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 px-3 text-gray-500 font-medium">Palier</th>
                                <th class="text-left py-2 px-3 text-gray-500 font-medium">Badge</th>
                                <th class="text-left py-2 px-3 text-gray-500 font-medium">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($milestones as $milestone)
                                @php
                                    $earned = $badges->firstWhere('badge_type', $milestone['days'] . '_day');
                                    $isUnlocked = $currentStreak >= $milestone['days'];
                                @endphp
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-3 font-medium">{{ $milestone['days'] }} jours</td>
                                    <td class="py-3 px-3">{{ $milestone['icon'] }} {{ $milestone['label'] }}</td>
                                    <td class="py-3 px-3">
                                        @if ($earned)
                                            <span class="text-green-600 font-medium">✅ Débloqué</span>
                                        @elseif ($isUnlocked)
                                            <span class="text-yellow-500 font-medium">⏳ En cours...</span>
                                        @else
                                            <span class="text-gray-400">🔒 {{ $milestone['days'] - $currentStreak }} jours restants</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Earned Badges -->
            @if (count($badges) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">🏅 Badges obtenus</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($badges as $badge)
                            <div class="flex items-center gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <span class="text-2xl">{{ explode(' ', $badge->badge_name)[0] }}</span>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $badge->badge_name }}</p>
                                    <p class="text-xs text-gray-500">Obtenu le {{ $badge->earned_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
