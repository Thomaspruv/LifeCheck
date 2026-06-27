<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Étape 2 : Personnalise tes dimensions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm">Étape 2/3</span>
                            <span class="text-sm text-gray-400">Choisis le type d'input</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 66%"></div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-2">Comment veux-tu évaluer chaque dimension ?</h3>
                    <p class="text-sm text-gray-500 mb-6">Choisis le type de réponse pour chaque dimension sélectionnée.</p>

                    <form method="POST" action="{{ route('onboarding.postStep2') }}">
                        @csrf

                        <div class="space-y-6">
                            @foreach($dimensions as $key => $label)
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-medium text-gray-700 mb-3">{{ $label }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($inputTypes as $typeKey => $typeLabel)
                                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                                        <input type="radio"
                                               name="input_types[{{ $key }}]"
                                               value="{{ $typeKey }}"
                                               class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">{{ $typeLabel }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('input_types.{{ $key }}')" class="mt-1" />
                            </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between mt-6">
                            <a href="{{ route('onboarding.step1') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                ← Retour
                            </a>
                            <x-primary-button>Étape suivante →</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
