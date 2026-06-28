<x-app-layout title="Rattrapage — LifeCheck"
    seoDescription="Rattrape les check-ins que tu as oubliés les jours précédents.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('⏪ Rattrapage de check-in') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-6">
                        <span class="text-5xl block mb-3">📋</span>
                        <h3 class="text-lg font-semibold">Jours à rattraper</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Tu as oublié de faire ton check-in certains jours ?<br>
                            Choisis un jour ci-dessous pour le rattraper.
                        </p>
                    </div>

                    <div class="space-y-3">
                        @foreach($missedDates as $date)
                            @php
                                $carbon = \Carbon\Carbon::parse($date);
                                $dayName = $carbon->translatedFormat('l');
                                $formatted = $carbon->translatedFormat('d F Y');
                                $daysAgo = $carbon->diffInDays(now());
                            @endphp
                            <a href="{{ route('checkin.create', ['date' => $date]) }}"
                               class="flex items-center justify-between p-4 border-2 border-dashed border-amber-200 rounded-xl hover:border-amber-400 hover:bg-amber-50 transition-all group">
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl group-hover:scale-110 transition-transform">📝</span>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $dayName }} {{ $formatted }}</p>
                                        <p class="text-xs text-gray-400">
                                            @if($daysAgo === 1)
                                                Hier
                                            @else
                                                Il y a {{ $daysAgo }} jours
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-amber-600 group-hover:underline">
                                    Rattraper →
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            ← Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
