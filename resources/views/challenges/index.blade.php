<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 Défis personnels
            </h2>
            <a href="{{ route('challenges.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                + Nouveau défi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Actifs</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Terminés</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-gray-500">{{ $stats['failed'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Échoués</p>
                </div>
            </div>

            @if ($challenges->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">🎯</p>
                    <p class="text-lg text-gray-600 mb-2">Aucun défi pour le moment</p>
                    <p class="text-sm text-gray-400 mb-6">Crée ton premier défi personnel !</p>
                    <a href="{{ route('challenges.create') }}"
                       class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        Créer un défi
                    </a>
                </div>
            @else
                <!-- Active Challenges -->
                @php $activeChallenges = $challenges->where('status', 'active'); @endphp
                @if ($activeChallenges->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">🟢 En cours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($activeChallenges as $challenge)
                            <x-challenge-card :challenge="$challenge" />
                        @endforeach
                    </div>
                @endif

                <!-- Paused Challenges -->
                @php $pausedChallenges = $challenges->where('status', 'paused'); @endphp
                @if ($pausedChallenges->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">⏸️ En pause</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($pausedChallenges as $challenge)
                            <x-challenge-card :challenge="$challenge" />
                        @endforeach
                    </div>
                @endif

                <!-- Completed/Failed Challenges -->
                @php $historyChallenges = $challenges->whereIn('status', ['completed', 'failed']); @endphp
                @if ($historyChallenges->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">📜 Historique</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($historyChallenges as $challenge)
                            <x-challenge-card :challenge="$challenge" />
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
