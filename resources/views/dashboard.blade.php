<x-app-layout title="Tableau de bord — LifeCheck"
    seoDescription="Votre tableau de bord personnel LifeCheck : streak actuel, check-ins quotidiens, tendances et badges de bien-être.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Welcome + Check-in CTA -->
            <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-700 shadow-lg sm:rounded-2xl p-6 md:p-8 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-white text-xl md:text-2xl font-bold">
                            👋 Bonjour, {{ Auth::user()->name }} !
                        </h3>
                        <p class="text-indigo-100 text-sm mt-1">
                            @if($todayDone)
                                Tu as déjà fait ton check-in aujourd'hui. À demain !
                            @elseif($hasTemplate)
                                Prêt pour ton check-in du {{ now()->format('l d F') }} ?
                            @else
                                Configure ton premier template pour commencer.
                            @endif
                        </p>
                    </div>
                    <div class="shrink-0">
                        @if($hasTemplate && !$todayDone)
                            <a href="{{ route('checkin.create') }}"
                               class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-all transform hover:scale-105 text-base">
                                ✍️ Faire mon check-in
                            </a>
                        @elseif($todayDone)
                            <span class="inline-flex items-center px-6 py-3 bg-white/20 text-white font-bold rounded-xl text-base">
                                ✅ Check-in fait !
                            </span>
                        @else
                            <a href="{{ route('onboarding.step1') }}"
                               class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-all transform hover:scale-105 text-base">
                                🚀 Configurer mon template
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Catch-up CTA -->
            @if($hasTemplate && $todayDone && $missedDaysCount > 0)
            <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⏪</span>
                        <div>
                            <p class="font-medium text-amber-800 text-sm">
                                Tu as <strong>{{ $missedDaysCount }} jour{{ $missedDaysCount > 1 ? 's' : '' }}</strong> à rattraper.
                            </p>
                            <p class="text-xs text-amber-600">Fais un check-in différé pour les jours oubliés.</p>
                        </div>
                    </div>
                    <a href="{{ route('checkin.catch-up') }}"
                       class="shrink-0 inline-flex items-center px-4 py-2 bg-amber-500 text-white font-medium rounded-lg hover:bg-amber-600 transition-all text-sm">
                        ⏪ Rattraper
                    </a>
                </div>
            </div>
            @endif

            <!-- Stats Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <!-- Current Streak -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl">🔥</span>
                        <span class="text-xs font-medium text-orange-500 bg-orange-50 px-2 py-1 rounded-full">En cours</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $streak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Streak actuel</p>
                    <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                        @php $streakPct = min(($streak / max($bestStreak, 1)) * 100, 100); @endphp
                        <div class="bg-orange-400 h-1.5 rounded-full transition-all duration-500" style="width: {{ $streakPct }}%"></div>
                    </div>
                </div>

                <!-- Best Streak -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl">🏅</span>
                        <span class="text-xs font-medium text-yellow-500 bg-yellow-50 px-2 py-1 rounded-full">Record</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $bestStreak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Meilleur streak</p>
                </div>

                <!-- Total Check-ins -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl">📊</span>
                        <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">Total</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCheckins }}</p>
                    <p class="text-sm text-gray-500 mt-1">Check-ins</p>
                </div>

                <!-- This Week Progress -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl">📅</span>
                        <span class="text-xs font-medium text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full">{{ $thisWeekCheckins }}/{{ $weekDayCount }}</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $thisWeekCheckins }}</p>
                    <p class="text-sm text-gray-500 mt-1">Cette semaine</p>
                    <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-indigo-400 h-1.5 rounded-full transition-all duration-500" style="width: {{ ($thisWeekCheckins / max($weekDayCount, 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Emotional Weather + Mood Chart -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Emotional Weather Widget -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">🌤️</span>
                        <h3 class="font-semibold text-gray-800">Météo émotionnelle</h3>
                    </div>

                    @if($avgMood !== null)
                        <div class="text-center py-4">
                            @php
                                $weatherEmoji = match (true) {
                                    $avgMood >= 8 => '🌟',
                                    $avgMood >= 6 => '☀️',
                                    $avgMood >= 4 => '⛅',
                                    $avgMood >= 2 => '🌧️',
                                    default => '🌩️',
                                };
                                $weatherLabel = match (true) {
                                    $avgMood >= 8 => 'Radieux',
                                    $avgMood >= 6 => 'Ensoleillé',
                                    $avgMood >= 4 => 'Nuageux',
                                    $avgMood >= 2 => 'Pluvieux',
                                    default => 'Orageux',
                                };
                                $weatherColor = match (true) {
                                    $avgMood >= 8 => 'text-yellow-400',
                                    $avgMood >= 6 => 'text-orange-400',
                                    $avgMood >= 4 => 'text-gray-400',
                                    $avgMood >= 2 => 'text-blue-400',
                                    default => 'text-purple-500',
                                };
                            @endphp
                            <div class="text-6xl mb-3">{{ $weatherEmoji }}</div>
                            <p class="text-xl font-bold text-gray-800">{{ $weatherLabel }}</p>
                            <p class="text-sm text-gray-500 mt-1">Humeur moyenne : <strong>{{ $avgMood }}</strong>/10</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Émotion dominante</span>
                                <span class="text-xl">{{ $dominantEmotion }}</span>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm text-gray-500">Streak actuel</span>
                                <span class="font-semibold text-gray-700">{{ $streak }} jours 🔥</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-5xl mb-3">🌤️</p>
                            <p class="text-gray-400 text-sm">Pas assez de données cette semaine.</p>
                            <p class="text-gray-400 text-xs mt-1">Continue tes check-ins pour voir ta météo.</p>
                        </div>
                    @endif
                </div>

                <!-- Mood Chart -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📈</span>
                            <h3 class="font-semibold text-gray-800">Tendance des 7 jours</h3>
                        </div>
                        <a href="{{ route('trends') }}" class="text-xs text-indigo-600 hover:underline">Voir tout →</a>
                    </div>

                    @if(count(array_filter($moodChartData, fn($v) => $v !== null)) > 0)
                        <canvas id="moodChart" class="w-full" style="max-height: 220px;"></canvas>
                    @else
                        <div class="text-center py-10">
                            <p class="text-gray-400 text-sm">Pas de données numériques récentes.</p>
                            <p class="text-gray-400 text-xs mt-1">Les graphiques apparaissent quand tu utilises des sliders (1-10).</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Streak Progress + Badges Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Streak Progress -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">🎯</span>
                        <h3 class="font-semibold text-gray-800">Progression streak</h3>
                    </div>

                    @php
                        $milestones = [
                            ['days' => 7, 'icon' => '🔥', 'label' => '7 jours'],
                            ['days' => 30, 'icon' => '💪', 'label' => '30 jours'],
                            ['days' => 100, 'icon' => '👑', 'label' => '100 jours'],
                            ['days' => 365, 'icon' => '🏆', 'label' => '365 jours'],
                        ];
                    @endphp

                    <div class="space-y-3">
                        @foreach($milestones as $ms)
                            @php
                                $unlocked = $streak >= $ms['days'];
                                $progressToNext = 0;
                                $prevDays = $loop->first ? 0 : $milestones[$loop->index - 1]['days'];
                                $range = $ms['days'] - $prevDays;
                                $progressToNext = $range > 0 ? min(($streak - $prevDays) / $range * 100, 100) : 0;
                                $progressToNext = max($progressToNext, 0);
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                                    {{ $unlocked ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $unlocked ? '✅' : $ms['icon'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium {{ $unlocked ? 'text-green-700' : 'text-gray-600' }}">
                                            {{ $ms['icon'] }} {{ $ms['label'] }}
                                        </span>
                                        <span class="text-xs {{ $unlocked ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                            @if($unlocked)
                                                ✅ OK
                                            @else
                                                {{ $ms['days'] - $streak }} jours
                                            @endif
                                        </span>
                                    </div>
                                    @if(!$unlocked)
                                    <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-indigo-400 h-1.5 rounded-full transition-all" style="width: {{ $progressToNext }}%"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('streaks.index') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                            Voir le calendrier détaillé →
                        </a>
                    </div>
                </div>

                <!-- Recent Badges -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">🏅</span>
                        <h3 class="font-semibold text-gray-800">Badges récents</h3>
                    </div>

                    @if($badges->count() > 0)
                        <div class="space-y-3">
                            @foreach($badges->take(4) as $badge)
                            <div class="flex items-center gap-3 p-3 rounded-xl {{ $loop->first ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50 border border-gray-100' }}">
                                <span class="text-2xl">{{ explode(' ', $badge->badge_name)[0] }}</span>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800 text-sm">{{ $badge->badge_name }}</p>
                                    <p class="text-xs text-gray-500">Obtenu le {{ $badge->earned_at->format('d/m/Y') }}</p>
                                </div>
                                @if($loop->first)
                                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-medium">Nouveau</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if($badges->count() > 4)
                            <div class="mt-3 text-center">
                                <a href="{{ route('streaks.index') }}" class="text-sm text-indigo-600 hover:underline">
                                    +{{ $badges->count() - 4 }} badge(s) supplémentaire(s)
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <p class="text-4xl mb-2">🏅</p>
                            <p class="text-gray-400 text-sm">Aucun badge pour le moment.</p>
                            <p class="text-gray-400 text-xs mt-1">Atteins 7 jours de streak pour débloquer ton premier badge !</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Today's Tags -->
            @if($todayCheckin && $todayCheckin->emotionTags->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">🏷️</span>
                    <h3 class="font-semibold text-gray-800">Tags du jour</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($todayCheckin->emotionTags as $tag)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium"
                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                        {{ $tag->icon }} {{ $tag->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @elseif($allTags->count() > 0 && $todayDone)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg">🏷️</span>
                    <h3 class="font-semibold text-gray-800">Tags du jour</h3>
                </div>
                <p class="text-sm text-gray-400">
                    Aucun tag associé à ton check-in d'aujourd'hui.
                    <a href="{{ route('tags.index') }}" class="text-indigo-500 hover:underline">Gérer mes tags</a>
                </p>
            </div>
            @endif

            <!-- Breathing & Meditation -->
            @if($breathingTotalSessions > 0)
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-sm border border-purple-100 p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🧘</span>
                        <h3 class="font-semibold text-gray-800">Respiration & Méditation</h3>
                    </div>
                    <a href="{{ route('breathing.index') }}"
                       class="text-xs text-purple-600 hover:underline flex items-center gap-1">
                        Voir les exercices →
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white/80 rounded-xl p-4 text-center">
                        <span class="text-2xl block mb-1">🧘</span>
                        <p class="text-lg font-bold text-gray-800">{{ $breathingTotalSessions }}</p>
                        <p class="text-xs text-gray-500">Séances totales</p>
                    </div>
                    <div class="bg-white/80 rounded-xl p-4 text-center">
                        <span class="text-2xl block mb-1">⏱️</span>
                        <p class="text-lg font-bold text-gray-800">{{ $breathingMinutesThisWeek }}</p>
                        <p class="text-xs text-gray-500">Minutes cette semaine</p>
                    </div>
                    <div class="bg-white/80 rounded-xl p-4 text-center">
                        <span class="text-2xl block mb-1">📅</span>
                        <p class="text-lg font-bold text-gray-800">{{ $breathingSessionsToday }}</p>
                        <p class="text-xs text-gray-500">Aujourd'hui</p>
                    </div>
                    <div class="bg-white/80 rounded-xl p-4 text-center">
                        <span class="text-2xl block mb-1">🔥</span>
                        <p class="text-lg font-bold text-gray-800">{{ $breathingStreak }}</p>
                        <p class="text-xs text-gray-500">Jours consécutifs</p>
                    </div>
                </div>
                @if($breathingSessionsToday === 0)
                <div class="mt-3 text-center">
                    <a href="{{ route('breathing.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-500 text-white font-medium rounded-lg hover:bg-purple-600 transition-all text-sm">
                        🧘 Faire une séance maintenant
                    </a>
                </div>
                @endif
            </div>
            @else
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-sm border border-purple-100 p-6 mb-8">
                <div class="flex items-center gap-4">
                    <span class="text-4xl">🧘</span>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">Respiration & Méditation</h3>
                        <p class="text-sm text-gray-500">Découvre des exercices de respiration et méditation guidée pour te détendre.</p>
                    </div>
                    <a href="{{ route('breathing.index') }}"
                       class="shrink-0 inline-flex items-center px-4 py-2 bg-purple-500 text-white font-medium rounded-lg hover:bg-purple-600 transition-all text-sm">
                        🧘 Découvrir
                    </a>
                </div>
            </div>
            @endif

            <!-- Quick Links -->
            @if($personality)
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl shadow-sm border border-indigo-100 p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-4xl">🧠</span>
                        <div>
                            <h3 class="font-semibold text-gray-800">Ton profil Big Five</h3>
                            <p class="text-sm text-gray-500">
                                Trait dominant : <strong>{{ $personality->dominantTrait() }}</strong>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('personality.results') }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 font-medium rounded-lg border border-indigo-200 hover:bg-indigo-50 transition-all text-sm">
                        Voir mon profil détaillé →
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('history.index') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📊</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Historique</h3>
                    <p class="text-xs text-gray-500 mt-1">Check-ins passés</p>
                </a>
                <a href="{{ route('trends') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📈</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Tendances</h3>
                    <p class="text-xs text-gray-500 mt-1">Évolution des dimensions</p>
                </a>
                <a href="{{ route('insights.index') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">🤖</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Insight IA</h3>
                    <p class="text-xs text-gray-500 mt-1">Résumé hebdomadaire</p>
                </a>
                <a href="{{ route('checkin.catch-up') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-amber-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">⏪</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Rattrapage</h3>
                    <p class="text-xs text-gray-500 mt-1">Check-ins différés</p>
                </a>
                <a href="{{ route('insights.j7') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📊</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Comparaison J-7</h3>
                    <p class="text-xs text-gray-500 mt-1">Jour par jour vs J-7</p>
                </a>
                <a href="{{ route('streaks.index') }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all group">
                    <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">🏆</span>
                    <h3 class="font-semibold text-gray-800 text-sm">Streaks</h3>
                    <p class="text-xs text-gray-500 mt-1">Badges et paliers</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const moodCanvas = document.getElementById('moodChart');
            if (moodCanvas) {
                const labels = {!! json_encode($moodChartLabels) !!};
                const data = {!! json_encode($moodChartData) !!};

                // Filter to valid entries only
                const validData = [];
                const validLabels = [];
                labels.forEach((label, i) => {
                    if (data[i] !== null) {
                        validLabels.push(label);
                        validData.push(data[i]);
                    }
                });

                if (validData.length > 0) {
                    new Chart(moodCanvas, {
                        type: 'line',
                        data: {
                            labels: validLabels,
                            datasets: [{
                                label: 'Humeur moyenne',
                                data: validData,
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.08)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                borderWidth: 2.5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#e2e8f0',
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Humeur : ' + context.parsed.y + '/10';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 10,
                                    grid: { color: 'rgba(0,0,0,0.04)' },
                                    ticks: {
                                        stepSize: 2,
                                        callback: function(value) {
                                            return value + '/10';
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { maxTicksLimit: 7 }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
