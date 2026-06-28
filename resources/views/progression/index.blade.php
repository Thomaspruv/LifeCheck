<x-app-layout title="Arbre de progression — LifeCheck"
    seoDescription="Visualise ton niveau, ton XP et tes branches de progression dans LifeCheck. Chaque check-in te fait monter en niveau !"
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Progression')]]">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🌳 Arbre de progression
            </h2>
            <span class="text-sm text-gray-500 bg-white px-3 py-1.5 rounded-full shadow-sm">
                Niveau {{ $currentLevel }} · {{ number_format($totalXp, 0, ',', ' ') }} XP
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ============ LEVEL OVERVIEW CARD ============ --}}
            <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-700 shadow-lg sm:rounded-2xl p-6 md:p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-3xl md:text-4xl font-bold shadow-inner">
                            {{ $currentLevel }}
                        </div>
                        <div>
                            @php
                                $levelNames = ['Débutant·e', 'Apprenti·e', 'Confirmé·e', 'Expert·e', 'Maître·e', 'Légende', 'Mythique'];
                                $titleIndex = min($currentLevel - 1, count($levelNames) - 1);
                            @endphp
                            <p class="text-white/70 text-sm font-medium uppercase tracking-wide">Niveau {{ $currentLevel }}</p>
                            <h3 class="text-2xl md:text-3xl font-bold">{{ $levelNames[$titleIndex] ?? 'Légende absolue' }}</h3>
                            <p class="text-indigo-200 text-sm mt-1">
                                {{ number_format($totalXp, 0, ',', ' ') }} XP cumulés
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 w-full md:w-64">
                        <div class="flex justify-between text-xs text-white/70 mb-1">
                            <span>Niveau {{ $currentLevel }}</span>
                            <span>{{ number_format($totalXp - $xpForCurrentLevel, 0, ',', ' ') }} / {{ number_format($xpForNextLevel, 0, ',', ' ') }} XP</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden shadow-inner">
                            <div class="bg-white h-3 rounded-full transition-all duration-1000 ease-out"
                                 style="width: {{ $levelProgress }}%"></div>
                        </div>
                        <p class="text-xs text-indigo-200 mt-1 text-right">
                            +{{ number_format($xpForNextLevel - ($totalXp - $xpForCurrentLevel), 0, ',', ' ') }} XP → Niveau {{ $currentLevel + 1 }}
                        </p>
                    </div>
                </div>

                {{-- Branch XP summary --}}
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($branches as $branch)
                        <div class="bg-white/10 rounded-xl p-3 text-center backdrop-blur-sm">
                            <span class="text-xl block mb-1">{{ $branch['icon'] }}</span>
                            <p class="text-xs text-indigo-200">{{ $branch['label'] }}</p>
                            <p class="text-sm font-bold mt-0.5">{{ $branch['unlocked_count'] }}/{{ $branch['total_nodes'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============ BRANCHES (PROGRESSION TREE) ============ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($branches as $branch)
                    @php
                        $colorMap = [
                            'orange' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-700', 'dot' => 'bg-orange-400', 'line' => 'border-orange-300', 'hover' => 'hover:border-orange-300', 'unlocked' => 'bg-orange-100 border-orange-300 text-orange-700'],
                            'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-400', 'line' => 'border-indigo-300', 'hover' => 'hover:border-indigo-300', 'unlocked' => 'bg-indigo-100 border-indigo-300 text-indigo-700'],
                            'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-400', 'line' => 'border-emerald-300', 'hover' => 'hover:border-emerald-300', 'unlocked' => 'bg-emerald-100 border-emerald-300 text-emerald-700'],
                            'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'dot' => 'bg-purple-400', 'line' => 'border-purple-300', 'hover' => 'hover:border-purple-300', 'unlocked' => 'bg-purple-100 border-purple-300 text-purple-700'],
                        ];
                        $c = $colorMap[$branch['color']] ?? $colorMap['indigo'];
                        $unlockedCount = $branch['unlocked_count'];
                        $totalNodes = $branch['total_nodes'];
                        $branchProgress = $totalNodes > 0 ? ($unlockedCount / $totalNodes) * 100 : 0;
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 {{ $c['hover'] }} transition-all duration-200">
                        {{-- Branch Header --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $branch['icon'] }}</span>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $branch['label'] }}</h3>
                                    <p class="text-xs text-gray-500">
                                        @if ($branch['key'] === 'wellbeing')
                                            {{ number_format($branch['metric'], 1) }}/10 ·
                                        @elseif ($branch['key'] === 'engagement')
                                            {{ $branch['metric'] }} sem. parfaites ·
                                        @else
                                            {{ number_format($branch['metric'], 0, ',', ' ') }} {{ $branch['units'] }} ·
                                        @endif
                                        {{ $unlockedCount }}/{{ $totalNodes }} débloqués
                                    </p>
                                </div>
                            </div>
                            <span class="text-sm font-bold {{ $c['text'] }} {{ $c['bg'] }} px-3 py-1 rounded-full">
                                {{ (int) round($branchProgress) }}%
                            </span>
                        </div>

                        {{-- Branch progress bar --}}
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-5">
                            <div class="{{ $c['dot'] }} h-1.5 rounded-full transition-all duration-700" style="width: {{ $branchProgress }}%"></div>
                        </div>

                        {{-- Tree Nodes with vertical connecting lines --}}
                        <div class="relative">
                            @foreach($branch['nodes'] as $node)
                                @php
                                    $isUnlocked = $node['is_unlocked'];
                                    $isLast = $loop->last;
                                @endphp
                                <div class="relative flex items-start gap-4 {{ $isLast ? 'pb-0' : 'pb-8' }}">
                                    {{-- Vertical connecting line --}}
                                    @if(!$isLast)
                                        <div class="absolute left-[15px] top-8 bottom-0 w-0.5 bg-gray-200"></div>
                                    @endif

                                    {{-- Node dot on the line --}}
                                    <div class="relative z-10 shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm
                                        {{ $isUnlocked ? $c['unlocked'] . ' shadow-sm' : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                        @if($isUnlocked)
                                            ✅
                                        @else
                                            {{ $node['icon'] }}
                                        @endif
                                    </div>

                                    {{-- Node content --}}
                                    <div class="flex-1 min-w-0 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium {{ $isUnlocked ? 'text-gray-800' : 'text-gray-400' }}">
                                                {{ $node['icon'] }} {{ $node['label'] }}
                                            </span>
                                            <span class="text-xs {{ $isUnlocked ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                                @if($isUnlocked)
                                                    ✅ Débloqué
                                                @else
                                                    @if ($branch['key'] === 'wellbeing')
                                                        {{ number_format($node['threshold'], 1) }}/10
                                                    @elseif ($branch['key'] === 'engagement' && $node['level'] > 3)
                                                        {{ (int) $node['threshold'] }} sem.
                                                    @else
                                                        {{ (int) $node['threshold'] }} {{ $branch['units'] }}
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $node['desc'] }}</p>

                                        {{-- Progress toward this node --}}
                                        @if(!$isUnlocked && $node['progress'] > 0 && $node['progress'] < 100)
                                            <div class="mt-2 w-full bg-gray-100 rounded-full h-1">
                                                <div class="{{ $c['dot'] }} h-1 rounded-full transition-all duration-500" style="width: {{ $node['progress'] }}%"></div>
                                            </div>
                                            <p class="text-xs text-{{ $branch['color'] }}-500 mt-0.5">
                                                {{ $node['progress'] }}% vers le déblocage
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============ LEVEL REWARDS PREVIEW ============ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                        @php $reached = $currentLevel >= $ms['level']; @endphp
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
                                @php $progressTo = $ms['level'] > 0 ? min(100, ($currentLevel / $ms['level']) * 100) : 0; @endphp
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                                    <div class="bg-indigo-400 h-1 rounded-full" style="width: {{ $progressTo }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============ GLOBAL STATS + HOW IT WORKS ============ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Global Stats --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">📊</span>
                        <h3 class="font-semibold text-gray-800">Résumé global</h3>
                    </div>
                    @php
                        $totalUnlocked = 0;
                        $totalMax = 0;
                        foreach($branches as $b) {
                            $totalUnlocked += $b['unlocked_count'];
                            $totalMax += $b['total_nodes'];
                        }
                        $globalPct = $totalMax > 0 ? ($totalUnlocked / $totalMax) * 100 : 0;
                    @endphp
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-indigo-50 rounded-xl">
                            <p class="text-2xl font-bold text-indigo-600">{{ $totalUnlocked }}/{{ $totalMax }}</p>
                            <p class="text-xs text-gray-500 mt-1">Nœuds débloqués</p>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-xl">
                            <p class="text-2xl font-bold text-orange-600">{{ $currentLevel }}</p>
                            <p class="text-xs text-gray-500 mt-1">Niveau actuel</p>
                        </div>
                        <div class="p-4 bg-emerald-50 rounded-xl">
                            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalXp, 0, ',', ' ') }}</p>
                            <p class="text-xs text-gray-500 mt-1">XP total</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-xl">
                            <p class="text-2xl font-bold text-purple-600">{{ (int) round($globalPct) }}%</p>
                            <p class="text-xs text-gray-500 mt-1">Progression globale</p>
                        </div>
                    </div>
                    <div class="mt-5 w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 h-2.5 rounded-full transition-all duration-1000"
                             style="width: {{ $globalPct }}%"></div>
                    </div>
                </div>

                {{-- How it works --}}
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">💡</span>
                        <h3 class="font-semibold text-indigo-800">Comment progresser ?</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold text-lg">🔥</span>
                            <div>
                                <p class="font-medium text-indigo-900">Régularité</p>
                                <p class="text-indigo-600 text-xs">Enchaîne les check-ins quotidiens pour faire grimper ton streak.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-indigo-500 font-bold text-lg">🧘</span>
                            <div>
                                <p class="font-medium text-indigo-900">Bien-être</p>
                                <p class="text-indigo-600 text-xs">Des scores hauts dans tes sliders = un bien-être élevé.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold text-lg">📅</span>
                            <div>
                                <p class="font-medium text-indigo-900">Présence</p>
                                <p class="text-indigo-600 text-xs">Plus tu fais de check-ins, plus ta présence augmente.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-purple-500 font-bold text-lg">🎯</span>
                            <div>
                                <p class="font-medium text-indigo-900">Engagement</p>
                                <p class="text-indigo-600 text-xs">Des semaines complètes te rapportent des points d'engagement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Back to dashboard --}}
            <div class="text-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                    ← Retour au tableau de bord
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
