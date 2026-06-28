<x-app-layout title="Mon historique — Respiration & Méditation — LifeCheck"
    seoDescription="Consulte l'historique de tes séances de respiration et méditation.">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('breathing.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    ← Retour
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📊 Mon historique
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <span class="text-3xl block mb-1">🧘</span>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Séances</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <span class="text-3xl block mb-1">⏱️</span>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_minutes'], 0) }}</p>
                    <p class="text-xs text-gray-500">Minutes totales</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <span class="text-3xl block mb-1">📅</span>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['this_week'] }}</p>
                    <p class="text-xs text-gray-500">Cette semaine</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <span class="text-3xl block mb-1">🔥</span>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['streak'] }}</p>
                    <p class="text-xs text-gray-500">Jours consécutifs</p>
                </div>
            </div>

            @if ($sessions->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-6xl mb-4">🧘</p>
                    <p class="text-lg text-gray-500 mb-2">Pas encore de séances</p>
                    <p class="text-sm text-gray-400 mb-6">Commence par un exercice de respiration ou une méditation.</p>
                    <a href="{{ route('breathing.index') }}"
                       class="inline-block px-6 py-3 rounded-xl font-medium text-white shadow-lg hover:shadow-xl transition"
                       style="background-color: #6366f1;">
                        🧘 Voir les exercices
                    </a>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        @foreach ($sessions as $session)
                            <div class="p-4 md:p-5 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="text-2xl shrink-0">{{ $session->type === 'breathing' ? '🌬️' : '🧘' }}</span>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 text-sm truncate">
                                            {{ $session->exercise_name }}
                                        </p>
                                        <div class="flex items-center gap-3 text-xs text-gray-400 mt-0.5">
                                            <span>{{ $session->created_at->format('d/m/Y H:i') }}</span>
                                            <span>·</span>
                                            <span>{{ gmdate('i:s', $session->duration_seconds) }}</span>
                                            <span>·</span>
                                            <span class="capitalize">{{ $session->type === 'breathing' ? 'Respiration' : 'Méditation' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm {{ $session->completed ? 'text-green-500' : 'text-red-400' }}">
                                    {{ $session->completed ? '✅' : '⏹️' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
