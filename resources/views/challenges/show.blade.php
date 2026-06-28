<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 {{ $challenge->title }}
            </h2>
            <a href="{{ route('challenges.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Tous les défis
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Status Banner -->
            @if ($challenge->status === 'completed')
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
                    🎉 Félicitations ! Ce défi est terminé !
                </div>
            @elseif ($challenge->status === 'failed')
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                    Ce défi a été marqué comme échoué. Tu peux toujours réessayer ! 💪
                </div>
            @elseif ($challenge->status === 'paused')
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                    ⏸️ Ce défi est en pause.
                </div>
            @endif

            <!-- Challenge Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Description -->
                    <div class="md:col-span-2">
                        @if ($challenge->description)
                            <p class="text-gray-600">{{ $challenge->description }}</p>
                        @else
                            <p class="text-gray-400 italic">Aucune description</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-4">
                            Démarré le {{ $challenge->started_at->format('d/m/Y') }}
                            @if ($challenge->completed_at && $challenge->status !== 'active')
                                · Terminé le {{ $challenge->completed_at instanceof \Carbon\Carbon ? $challenge->completed_at->format('d/m/Y') : \Carbon\Carbon::parse($challenge->completed_at)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-3">
                        <div class="text-center p-3 bg-indigo-50 rounded-lg">
                            <p class="text-2xl font-bold text-indigo-600">{{ $progressPercent }}%</p>
                            <p class="text-xs text-gray-500">Progression</p>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $currentStreak }} 🔥</p>
                            <p class="text-xs text-gray-500">Jours consécutifs</p>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <p class="text-2xl font-bold text-orange-600">{{ $challenge->duration_days }}</p>
                            <p class="text-xs text-gray-500">Jours total</p>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                @if (in_array($challenge->status, ['active', 'paused']))
                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-gray-100">
                        <!-- Log today -->
                        @if ($challenge->status === 'active')
                            <form method="POST" action="{{ route('challenges.progress', $challenge) }}" class="inline">
                                @csrf
                                <input type="hidden" name="date" value="{{ now()->toDateString() }}" />
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                    ✅ Marquer aujourd'hui
                                </button>
                            </form>
                        @endif

                        @if ($challenge->status === 'active')
                            <form method="POST" action="{{ route('challenges.pause', $challenge) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition">
                                    ⏸️ Mettre en pause
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('challenges.resume', $challenge) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                    ▶️ Reprendre
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('challenges.fail', $challenge) }}" class="inline"
                              onsubmit="return confirm('Marquer ce défi comme échoué ?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-gray-400 text-white rounded-lg text-sm font-medium hover:bg-gray-500 transition">
                                ❌ Abandonner
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Delete -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('challenges.destroy', $challenge) }}" class="inline"
                          onsubmit="return confirm('Supprimer définitivement ce défi ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Calendar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('challenges.show', ['challenge' => $challenge, 'year' => $prevYear, 'month' => $prevMonth]) }}"
                       class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm transition">
                        ←
                    </a>
                    <h3 class="font-semibold text-gray-800">{{ $monthName }} {{ $year }}</h3>
                    @if ($year < now()->year || ($year == now()->year && $month < now()->month))
                        <a href="{{ route('challenges.show', ['challenge' => $challenge, 'year' => $nextYear, 'month' => $nextMonth]) }}"
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

                <!-- Calendar grid -->
                @php
                    $firstDayOfWeek = \Carbon\Carbon::parse(sprintf('%04d-%02d-01', $year, $month))->dayOfWeek;
                    $padding = ($firstDayOfWeek === 0) ? 6 : $firstDayOfWeek - 1;
                @endphp

                <div class="grid grid-cols-7 gap-1">
                    @for ($i = 0; $i < $padding; $i++)
                        <div></div>
                    @endfor

                    @foreach ($calendar as $cell)
                        @php
                            if ($cell['isFuture']) {
                                $dotStyle = 'bg-gray-100';
                                $textStyle = 'text-gray-300';
                            } elseif ($cell['isDone']) {
                                $dotStyle = 'bg-green-500';
                                $textStyle = 'text-white font-medium';
                            } elseif ($cell['hasEntry']) {
                                $dotStyle = 'bg-red-300';
                                $textStyle = 'text-white';
                            } else {
                                $dotStyle = 'bg-gray-200';
                                $textStyle = 'text-gray-400';
                            }
                            $borderClass = $cell['isToday'] ? 'ring-2 ring-indigo-400' : '';
                        @endphp
                        <div class="aspect-square flex items-center justify-center rounded-full {{ $dotStyle }} {{ $borderClass }} text-xs {{ $textStyle }}"
                             title="{{ $cell['date'] }}">
                            {{ $cell['day'] }}
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Fait
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-300 inline-block"></span> Raté
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-gray-200 inline-block"></span> Non fait
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-gray-100 inline-block"></span> Futur
                    </div>
                </div>
            </div>

            <!-- Progress bar (full width) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">📊 Progression globale</h3>
                <div class="mb-2">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-1">
                        <span>{{ $progressPercent }}% complété</span>
                        <span>{{ $currentStreak }}/{{ $challenge->duration_days }} jours</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700
                            {{ $challenge->status === 'completed' ? 'bg-blue-500' : ($challenge->status === 'failed' ? 'bg-gray-400' : 'bg-green-500') }}"
                            style="width: {{ $progressPercent }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
