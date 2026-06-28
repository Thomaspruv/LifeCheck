<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Widget — Humeur du jour') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Widget Card: Today's Mood at a Glance --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 text-center">
                @if (!$hasTemplate)
                    <div class="py-8">
                        <p class="text-gray-500 text-lg mb-4">{{ __('Vous n\'avez pas encore configuré votre check-in.') }}</p>
                        <a href="{{ route('onboarding.step1') }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ __('Configurer mon check-in') }}
                        </a>
                    </div>
                @elseif ($todayDone)
                    {{-- Today's mood --}}
                    <div class="mb-6">
                        <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">{{ __('Aujourd\'hui') }}</p>
                        <p class="text-6xl mt-2">
                            @if ($moodValue >= 9) 🌟
                            @elseif ($moodValue >= 7) 😊
                            @elseif ($moodValue >= 5) 😐
                            @elseif ($moodValue >= 3) 😕
                            @else 😞
                            @endif
                        </p>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900">{{ $moodLabel }}</h3>
                        @if ($moodValue)
                            <div class="mt-4 flex justify-center">
                                <div class="w-48 bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-red-400 via-yellow-400 to-green-400 h-3 rounded-full transition-all duration-500"
                                         style="width: {{ $moodValue * 10 }}%"></div>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ $moodValue }}/10</p>
                        @endif
                    </div>

                    @if ($dominantEmotion)
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-500">{{ __('Émotion principale') }}</p>
                            <p class="text-4xl mt-1">{{ $dominantEmotion }}</p>
                        </div>
                    @endif

                    {{-- Check-in details --}}
                    @if ($todayCheckin && $todayCheckin->notes)
                        <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ __('Notes') }}</p>
                            <p class="text-gray-700">{{ $todayCheckin->notes }}</p>
                        </div>
                    @endif

                    {{-- Streak --}}
                    @if ($streak > 0)
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-full text-sm font-medium">
                            <span>🔥</span>
                            <span>{{ __('Série : :count jour(s)', ['count' => $streak]) }}</span>
                        </div>
                    @endif

                    <div class="mt-8 flex justify-center gap-4">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition text-sm">
                            {{ __('Tableau de bord') }}
                        </a>
                        <a href="{{ route('history.index') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition text-sm">
                            {{ __('Historique') }}
                        </a>
                    </div>
                @else
                    {{-- Not checked in today --}}
                    <div class="py-6">
                        <p class="text-6xl mb-4">⏰</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Check-in du jour') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('Vous n\'avez pas encore fait votre check-in aujourd\'hui.') }}</p>

                        {{-- Last known mood if available --}}
                        @if ($lastCheckin)
                            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ __('Dernière humeur enregistrée') }}</p>
                                <p class="text-3xl mt-1">
                                    @if ($moodValue >= 9) 🌟
                                    @elseif ($moodValue >= 7) 😊
                                    @elseif ($moodValue >= 5) 😐
                                    @elseif ($moodValue >= 3) 😕
                                    @else 😞
                                    @endif
                                </p>
                                <p class="text-gray-700 font-medium">{{ $moodLabel ?? __('Inconnue') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $lastCheckin->date->format('d/m/Y') }}</p>
                            </div>
                        @endif

                        <a href="{{ route('checkin.create') }}"
                           class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-bold text-lg rounded-2xl hover:bg-indigo-700 transition shadow-lg hover:shadow-xl">
                            {{ __('Faire mon check-in') }} →
                        </a>

                        @if ($streak > 0)
                            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-full text-sm font-medium">
                                <span>🔥</span>
                                <span>{{ __('Série actuelle : :count jour(s)', ['count' => $streak]) }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Widget usage tips --}}
            <div class="mt-6 bg-indigo-50 rounded-xl p-5 text-sm text-indigo-800">
                <p class="font-semibold mb-1">{{ __('💡 Ajouter LifeCheck à votre écran d\'accueil') }}</p>
                <ul class="list-disc list-inside space-y-1 text-indigo-700">
                    <li><strong>iOS (Safari) :</strong> {{ __('Partager → Sur l\'écran d\'accueil') }}</li>
                    <li><strong>Android (Chrome) :</strong> {{ __('Menu → Installer l\'application') }}</li>
                    <li>{{ __('Une fois ajouté, ouvrez LifeCheck depuis l\'écran d\'accueil pour voir votre humeur du jour.') }}</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
