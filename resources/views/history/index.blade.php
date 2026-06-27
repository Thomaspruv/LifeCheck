<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historique des check-ins') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($grouped->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-gray-400 text-lg">Aucun check-in pour le moment.</p>
                    <a href="{{ route('checkin.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Faire mon premier check-in →</a>
                </div>
            @else
                @foreach($grouped as $month => $checkins)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ $month }}</h3>
                    <div class="space-y-3">
                        @foreach($checkins as $checkin)
                        <a href="{{ route('history.show', $checkin) }}"
                           class="block bg-white shadow-sm sm:rounded-lg p-4 hover:shadow-md transition border-l-4 border-indigo-400">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-800">
                                    {{ $checkin->date->format('l d') }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $checkin->items->count() }} réponses
                                </span>
                            </div>
                            @if($checkin->notes)
                            <p class="text-sm text-gray-500 mt-1 truncate">{{ $checkin->notes }}</p>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
