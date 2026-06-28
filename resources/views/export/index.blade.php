<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Export des données personnelles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Info card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Téléchargez vos données') }}
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    {{ __("Vous pouvez exporter toutes vos données LifeCheck dans un format CSV (lisible par Excel) ou PDF. L'export inclut votre profil, vos check-ins, vos badges, vos objectifs, vos défis personnels, vos sessions de méditation, vos émotions, vos paramètres et vos insights hebdomadaires.") }}
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                    <div class="bg-indigo-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $checkinCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Check-ins') }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $badgeCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Badges') }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-amber-600">{{ $insightCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Insights') }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $goalCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Objectifs') }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $challengeCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Défis') }}</p>
                    </div>
                    <div class="bg-pink-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-pink-600">{{ $meditationCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Méditations') }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('export.csv') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('Export CSV') }}
                    </a>
                    <a href="{{ route('export.pdf') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        {{ __('Export PDF') }}
                    </a>
                </div>
            </div>

            <!-- Data included -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                <h4 class="font-medium text-gray-900 mb-3">{{ __('📦 Données incluses dans l\'export') }}</h4>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Profil utilisateur (nom, email, date d\'inscription)') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Progression — niveau, XP total et par catégorie') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Paramètres personnels (rappel, thème, fuseau horaire, langue)') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Émotions / Tags personnalisés') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Check-ins quotidiens avec réponses et émotions associées') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Badges et streaks obtenus') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Objectifs personnels avec jalons et progression') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Défis personnels avec historique de progression') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Sessions de méditation / respiration') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">✓</span>
                        <span>{{ __('Insights hebdomadaires (moyenne humeur, tendances, résumés)') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Privacy note -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                <p class="font-medium mb-1">{{ __('🔒 Vos données vous appartiennent') }}</p>
                <p>{{ __("Cet export génère un fichier contenant toutes vos données personnelles. Aucune donnée n'est conservée sur le serveur après la génération du fichier. Téléchargez-le et conservez-le en lieu sûr.") }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
