<x-app-layout title="Arbre de progression — LifeCheck"
    seoDescription="Visualise ton niveau et débloque des branches de progression dans LifeCheck. Chaque check-in te fait monter en niveau !">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🌳 Arbre de progression
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ── Level Card ── -->
            <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 shadow-lg sm:rounded-2xl p-6 md:p-8 mb-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-3xl md:text-4xl font-bold shadow-inner">
                            {{ $currentLevel }}
                        </div>
                        <div>
                            <p class="text-white/70 text-sm font-medium uppercase tracking-wide">Niveau</p>
                            <h3 class="text-2xl md:text-3xl font-bold">{{ $totalXp }} XP</h3>
                            <p class="text-amber-100 text-sm mt-1">
                                @php
                                    $levelNames = ['Débutant·e', 'Apprenti·e', 'Confirmé·e', 'Expert·e', 'Maître·e', 'Légende', 'Mythique'];
                                    $titleIndex = min($currentLevel - 1, count($levelNames) - 1);
                                @endphp
                                {{ $levelNames[$titleIndex] ?? 'Légende absolue' }}
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0 w-full md:w-64">
                        <div class="flex justify-between text-xs text-white/70 mb-1">
                            <span>Niveau {{ $currentLevel }}</span>
                            <span>{{ number_format($xpInCurrentLevel) }} / {{ number_format($xpForNextLevel) }} XP</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-3">
                            <div class="bg-white h-3 rounded-full transition-all duration-700 ease-out"
                                 style="width: {{ $levelProgress }}%"></div>
                        </div>
                        <p class="text-xs text-amber-100 mt-1 text-right">
                            +{{ number_format($xpForNextLevel - $xpInCurrentLevel) }} XP jusqu'au niveau {{ $currentLevel + 1 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── Branches Tree ── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($branches as $branch)
                    @php
                        $colorMap = [
                            'orange' => ['bg' => 'bg-orange-500', 'light' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200', 'ring' => 'ring-orange-400'],
                            'indigo' => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-400'],
                            'emerald' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'ring' => 'ring-emerald-400'],
                            'purple'  => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200', 'ring' => 'ring-purple-400'],
                        ];
                        $colors = $colorMap[$branch['color']] ?? $colorMap['indigo'];
                        $branchPct = $branch['total_nodes'] > 0
                            ? round(($branch['unlocked_count'] / $branch['total_nodes']) * 100)
                            : 0;
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow"
                         x-data="{ expanded: false }">
                        <!-- Branch Header -->
                        <div class="flex items-center justify-between mb-3 cursor-pointer"
                             @@click="expanded = !expanded">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $colors['light'] }} flex items-center justify-center text-xl">
                                    {{ $branch['icon'] }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $branch['label'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $branch['unlocked_count'] }}/{{ $branch['total_nodes'] }} débloqués</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold {{ $colors['text'] }}">{{ $branchPct }}%</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform"
                                     :class="expanded ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Branch Progress Bar -->
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                            <div class="{{ $colors['bg'] }} h-2 rounded-full transition-all duration-700"
                                 style="width: {{ $branchPct }}%"></div>
                        </div>

                        <!-- Current metric value -->
                        <div class="flex items-center gap-2 mb-3 text-sm text-gray-500">
                            <span>Valeur actuelle :</span>
                            <span class="font-semibold {{ $colors['text'] }}">
                                @if ($branch['key'] === 'wellbeing')
                                    {{ number_format($branch['metric'], 1) }}/10
                                @elseif ($branch['key'] === 'engagement')
                                    {{ $branch['metric'] }} semaine(s) parfaite(s)
                                @else
                                    {{ $branch['metric'] }} {{ $branch['units'] }}
                                @endif
                            </span>
                        </div>

                        <!-- Nodes (collapsible) -->
                        <div class="space-y-2 overflow-hidden transition-all duration-300"
                             x-show="expanded"
                             x-collapse>
                            @foreach ($branch['nodes'] as $node)
                                @php
                                    $isUnlocked = $node['is_unlocked'];
                                @endphp
                                <div class="flex items-start gap-3 p-3 rounded-xl {{ $isUnlocked ? 'bg-green-50 border border-green-100' : 'bg-gray-50 border border-gray-100' }}">
                                    <!-- Node icon / status -->
                                    <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg
                                        {{ $isUnlocked ? 'bg-green-200 text-green-700' : 'bg-gray-200 text-gray-400' }}">
                                        @if ($isUnlocked)
                                            ✅
                                        @else
                                            {{ $node['icon'] }}
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium {{ $isUnlocked ? 'text-green-700' : 'text-gray-600' }}">
                                                {{ $node['icon'] }} {{ $node['label'] }}
                                            </span>
                                            <span class="text-xs {{ $isUnlocked ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                                @if ($isUnlocked)
                                                    ✅
                                                @else
                                                    @if ($branch['key'] === 'wellbeing')
                                                        {{ number_format($node['threshold'], 1) }}/10
                                                    @elseif ($branch['key'] === 'engagement' && $node['level'] > 3)
                                                        {{ (int) $node['threshold'] }} semaines
                                                    @else
                                                        {{ (int) $node['threshold'] }} {{ $branch['units'] }}
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $node['desc'] }}</p>

                                        @if (!$isUnlocked)
                                            <div class="mt-1.5 w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="{{ $colors['bg'] }} h-1.5 rounded-full transition-all"
                                                     style="width: {{ $node['progress'] }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Expand/collapse hint -->
                        <button @@click="expanded = !expanded"
                                class="mt-3 text-xs {{ $colors['text'] }} hover:underline w-full text-center">
                            <span x-show="!expanded">Voir les détails</span>
                            <span x-show="expanded">Réduire</span>
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- ── Level Rewards Preview ── -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">🎁</span>
                    <h3 class="font-semibold text-gray-800">Prochains paliers</h3>
                </div>

                @php
                    $nextMilestones = [
                        ['level' => 5, 'reward' => 'Débloque toutes les branches', 'icon' => '🌳'],
                        ['level' => 10, 'reward' => 'Badge « Explorateur »', 'icon' => '🧭'],
                        ['level' => 25, 'reward' => 'Thème visuel personnalisé', 'icon' => '🎨'],
                        ['level' => 50, 'reward' => 'Badge « Légende vivante »', 'icon' => '🏆'],
                        ['level' => 100, 'reward' => 'Statut « Maître du bien-être »', 'icon' => '👑'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach ($nextMilestones as $ms)
                        @php
                            $reached = $currentLevel >= $ms['level'];
                            $progressTo = $ms['level'] > 0
                                ? min(100, ($currentLevel / $ms['level']) * 100)
                                : 0;
                        @endphp
                        <div class="text-center p-4 rounded-xl {{ $reached ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-100' }}">
                            <div class="text-3xl mb-2">{{ $ms['icon'] }}</div>
                            <p class="text-xs {{ $reached ? 'text-green-700 font-medium' : 'text-gray-500' }}">
                                Niveau {{ $ms['level'] }}
                            </p>
                            <p class="text-sm font-medium {{ $reached ? 'text-green-700' : 'text-gray-700' }} mt-1">
                                {{ $ms['reward'] }}
                            </p>
                            @if ($reached)
                                <span class="text-xs text-green-600 font-medium mt-1 inline-block">✅ Débloqué</span>
                            @else
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                                    <div class="bg-indigo-400 h-1 rounded-full" style="width: {{ $progressTo }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ── How it works ── -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mt-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">💡</span>
                    <h3 class="font-semibold text-indigo-800">Comment progresser ?</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-orange-500 font-bold">🔥</span>
                        <div>
                            <p class="font-medium text-indigo-900">Régularité</p>
                            <p class="text-indigo-600 text-xs">Enchaîne les check-ins quotidiens pour faire grimper ton streak.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-indigo-500 font-bold">🧘</span>
                        <div>
                            <p class="font-medium text-indigo-900">Bien-être</p>
                            <p class="text-indigo-600 text-xs">Des scores hauts dans tes sliders = un bien-être élevé.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">📅</span>
                        <div>
                            <p class="font-medium text-indigo-900">Présence</p>
                            <p class="text-indigo-600 text-xs">Plus tu fais de check-ins, plus ta présence augmente.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-purple-500 font-bold">🎯</span>
                        <div>
                            <p class="font-medium text-indigo-900">Engagement</p>
                            <p class="text-indigo-600 text-xs">Des semaines complètes te rapportent des points d'engagement.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Back button ── -->
            <div class="mt-8 text-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                    ← Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
