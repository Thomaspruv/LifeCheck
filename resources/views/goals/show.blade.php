<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('goals.index') }}" class="text-gray-400 hover:text-gray-600 transition text-lg">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🎯 {{ $goal->title }}
                </h2>
                @php
                    $statusBadge = match($goal->status) {
                        'active' => ['bg-green-100', 'text-green-700', '🟢 En cours'],
                        'completed' => ['bg-blue-100', 'text-blue-700', '✅ Accompli'],
                        'abandoned' => ['bg-gray-100', 'text-gray-500', '📦 Abandonné'],
                        default => ['bg-gray-100', 'text-gray-500', $goal->status],
                    };
                @endphp
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                    {{ $statusBadge[2] }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                @if ($goal->status === 'active')
                    <form method="POST" action="{{ route('goals.complete', $goal) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg font-medium hover:bg-green-700 transition">
                            ✅ Accomplir
                        </button>
                    </form>
                    <a href="{{ route('goals.edit', $goal) }}"
                        class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg font-medium hover:bg-indigo-700 transition">
                        ✏️ Modifier
                    </a>
                    <form method="POST" action="{{ route('goals.abandon', $goal) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 bg-gray-200 text-gray-600 text-xs rounded-lg font-medium hover:bg-gray-300 transition"
                            onclick="return confirm('Abandonner cet objectif ? Tu pourras le réactiver plus tard.')">
                            📦 Abandonner
                        </button>
                    </form>
                @elseif ($goal->status === 'abandoned')
                    <form method="POST" action="{{ route('goals.reactivate', $goal) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg font-medium hover:bg-green-700 transition">
                            🚀 Réactiver
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('goals.destroy', $goal) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-3 py-1.5 bg-red-100 text-red-600 text-xs rounded-lg font-medium hover:bg-red-200 transition"
                        onclick="return confirm('Supprimer définitivement cet objectif ?')">
                        🗑️
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Goal Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                @if ($goal->description)
                    <p class="text-gray-600 mb-4">{{ $goal->description }}</p>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-indigo-600">{{ $goal->milestones->count() }}</p>
                        <p class="text-xs text-gray-500">Jalons</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $goal->completed_milestones_count }}</p>
                        <p class="text-xs text-gray-500">Complétés</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $progressPercent }}%</p>
                        <p class="text-xs text-gray-500">Progression</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-600">
                            @if ($goal->target_date)
                                {{ $goal->target_date->format('d/m') }}
                            @else
                                —
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">Date cible</p>
                    </div>
                </div>

                @if ($goal->milestones->count() > 0)
                    <div class="mt-4 w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 ease-out
                            {{ $progressPercent === 100 ? 'bg-blue-500' : 'bg-green-500' }}"
                            style="width: {{ $progressPercent }}%">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Milestones List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">🏁 Jalons</h3>
                    @if ($goal->status === 'active')
                        <button type="button"
                            onclick="document.getElementById('addMilestoneForm').classList.toggle('hidden')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                            + Ajouter un jalon
                        </button>
                    @endif
                </div>

                <!-- Add Milestone Form -->
                <form id="addMilestoneForm"
                    method="POST" action="{{ route('goals.milestones.add', $goal) }}"
                    class="hidden mb-6 p-4 bg-gray-50 rounded-lg space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" placeholder="Titre du jalon"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            maxlength="255" required />
                    </div>
                    <div>
                        <input type="text" name="description" placeholder="Description (optionnelle)"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            maxlength="1000" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg font-medium hover:bg-indigo-700 transition">
                            ➕ Ajouter
                        </button>
                    </div>
                </form>

                @forelse ($milestones as $milestone)
                    <div class="flex items-start gap-4 p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 rounded-lg transition group">
                        <!-- Toggle button -->
                        <div class="mt-0.5">
                            @if ($goal->status === 'active')
                                <form method="POST" action="{{ route('goals.milestones.toggle', [$goal, $milestone]) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition
                                        {{ $milestone->is_completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 hover:border-green-400' }}">
                                        @if ($milestone->is_completed)
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            @else
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                    {{ $milestone->is_completed ? 'bg-blue-500 border-blue-500 text-white' : 'border-gray-300' }}">
                                    @if ($milestone->is_completed)
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Milestone content -->
                        <div class="flex-1 min-w-0">
                            <p class="font-medium {{ $milestone->is_completed ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                {{ $milestone->title }}
                            </p>
                            @if ($milestone->description)
                                <p class="text-sm text-gray-500 mt-0.5">{{ $milestone->description }}</p>
                            @endif
                            @if ($milestone->completed_at)
                                <p class="text-xs text-gray-400 mt-1">✅ Complété le {{ $milestone->completed_at->format('d/m/Y à H:i') }}</p>
                            @endif
                        </div>

                        <!-- Delete button -->
                        @if ($goal->status === 'active')
                            <form method="POST" action="{{ route('goals.milestones.delete', [$goal, $milestone]) }}"
                                class="opacity-0 group-hover:opacity-100 transition">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 hover:text-red-600 transition text-sm"
                                    onclick="return confirm('Supprimer ce jalon ?')">
                                    ✕
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-4xl mb-2">🏁</p>
                        <p class="text-gray-400 text-sm">Aucun jalon pour le moment.</p>
                        @if ($goal->status === 'active')
                            <p class="text-gray-400 text-xs mt-1">Ajoute des jalons pour suivre ta progression !</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Info card -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <p class="text-sm text-indigo-700 font-medium mb-1">💡 Astuce</p>
                <p class="text-sm text-indigo-600">
                    Décompose ton objectif en petites étapes (jalons). Coche-les au fur et à mesure pour voir ta progression !
                    Quand tous les jalons sont complétés, l'objectif est automatiquement marqué comme accompli. 🎉
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
