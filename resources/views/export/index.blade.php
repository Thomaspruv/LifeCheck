<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Export des données personnelles')]]">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Export des données personnelles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        {{ __('Téléchargez l\'ensemble de vos données LifeCheck. Vous pouvez exporter au format CSV (tableur) ou PDF (rapport lisible).') }}
                    </p>

                    {{-- Stats des données --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $checkinCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Check-ins') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $badgeCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Badges') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $goalCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Objectifs') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $challengeCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Défis') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $meditationCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Méditations') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $insightCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Insights') }}</p>
                        </div>
                    </div>

                    {{-- Boutons d'export --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('export.csv') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-semibold">{{ __('Télécharger en CSV') }}</span>
                            <span class="text-xs text-primary-200">(.csv)</span>
                        </a>

                        <a href="{{ route('export.pdf') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-semibold">{{ __('Télécharger en PDF') }}</span>
                            <span class="text-xs text-red-200">(.pdf)</span>
                        </a>
                    </div>

                    {{-- Info RGPD --}}
                    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p class="font-medium mb-1">{{ __('Vos données vous appartiennent') }}</p>
                                <p>{{ __('Ce fichier contient l\'intégralité de vos données personnelles enregistrées dans LifeCheck. Aucune information n\'est partagée avec des tiers.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
