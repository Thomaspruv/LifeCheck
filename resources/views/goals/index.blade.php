<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 Objectifs & jalons
            </h2>
            <a href="{{ route('goals.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                + Nouvel objectif
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
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">En cours</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Accomplis</p>
                </div>
            </div>

            @if ($goals->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">🎯</p>
                    <p class="text-lg text-gray-600 mb-2">Aucun objectif pour le moment</p>
                    <p class="text-sm text-gray-400 mb-6">Définis ton premier objectif avec ses jalons !</p>
                    <a href="{{ route('goals.create') }}"
                       class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        Créer un objectif
                    </a>
                </div>
            @else
                <!-- Active Goals -->
                @php $activeGoals = $goals->where('status', 'active'); @endphp
                @if ($activeGoals->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">🟢 En cours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($activeGoals as $goal)
                            <x-goal-card :goal="$goal" />
                        @endforeach
                    </div>
                @endif

                <!-- Completed Goals -->
                @php $doneGoals = $goals->where('status', 'completed'); @endphp
                @if ($doneGoals->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">✅ Accomplis</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($doneGoals as $goal)
                            <x-goal-card :goal="$goal" />
                        @endforeach
                    </div>
                @endif

                <!-- Abandoned Goals -->
                @php $abandonedGoals = $goals->where('status', 'abandoned'); @endphp
                @if ($abandonedGoals->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mb-4">📦 Abandonnés</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($abandonedGoals as $goal)
                            <x-goal-card :goal="$goal" />
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
