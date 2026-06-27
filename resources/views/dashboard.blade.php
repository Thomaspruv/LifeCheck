<x-app-layout title="Tableau de bord — LifeCheck"
    seoDescription="Votre tableau de bord personnel LifeCheck : streak actuel, check-ins quotidiens, tendances et badges de bien-être.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Streak + Check-in CTA -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-4xl font-bold text-indigo-600">{{ $streak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Streak actuel</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-4xl font-bold text-yellow-500">{{ $bestStreak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Meilleur streak</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-4xl font-bold text-green-500">{{ $totalCheckins }}</p>
                    <p class="text-sm text-gray-500 mt-1">Check-ins</p>
                </div>
            </div>

            <!-- CTA Check-in -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg sm:rounded-lg p-8 mb-8 text-center">
                @if($hasTemplate && !$todayDone)
                    <a href="{{ route('checkin.create') }}"
                       class="inline-block px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow hover:bg-gray-100 transition text-lg">
                        ✍️ Faire mon check-in
                    </a>
                    <p class="text-white/80 text-sm mt-3">Prends une minute pour noter ta journée</p>
                @elseif($todayDone)
                    <p class="text-white text-lg font-semibold">✅ Déjà fait aujourd'hui !</p>
                    <p class="text-white/80 text-sm mt-1">Reviens demain pour continuer ta série.</p>
                @else
                    <a href="{{ route('onboarding.step1') }}"
                       class="inline-block px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow hover:bg-gray-100 transition">
                        🚀 Configurer mon template
                    </a>
                    <p class="text-white/80 text-sm mt-3">Crée ton premier template de check-in</p>
                @endif
            </div>

            <!-- Quick links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('history.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition block">
                    <h3 class="font-semibold text-gray-800">📊 Historique</h3>
                    <p class="text-sm text-gray-500 mt-1">Consulte tes check-ins passés</p>
                </a>
                <a href="{{ route('trends') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition block">
                    <h3 class="font-semibold text-gray-800">📈 Tendances</h3>
                    <p class="text-sm text-gray-500 mt-1">Visualise l'évolution de tes dimensions</p>
                </a>
                <a href="{{ route('insights.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition block">
                    <h3 class="font-semibold text-gray-800">🤖 Insight IA</h3>
                    <p class="text-sm text-gray-500 mt-1">Résumé hebdomadaire de ton bien-être</p>
                </a>
                <a href="{{ route('streaks.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition block">
                    <h3 class="font-semibold text-gray-800">🏆 Streaks</h3>
                    <p class="text-sm text-gray-500 mt-1">Badges et paliers de récompense</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
