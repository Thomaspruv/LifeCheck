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
                    {{ __('Vous pouvez exporter toutes vos données LifeCheck dans un format CSV (lisible par Excel) ou PDF. L\'export inclut votre profil, vos check-ins, vos badges, vos paramètres et vos insights hebdomadaires.') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-indigo-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $checkinCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Check-ins') }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $badgeCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Badges') }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-amber-600">{{ $insightCount }}</p>
                        <p class="text-xs text-gray-600">{{ __('Insights') }}</p>
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

            <!-- Privacy note -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                <p class="font-medium mb-1">{{ __('🔒 Vos données vous appartiennent') }}</p>
                <p>{{ __('Cet export génère un fichier contenant toutes vos données personnelles. Aucune donnée n\'est conservée sur le serveur après la génération du fichier. Téléchargez-le et conservez-le en lieu sûr.') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
