<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Étape 4 : Ton profil de personnalité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm">Étape 4/4</span>
                            <span class="text-sm text-gray-400">Questionnaire de personnalité</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Découvre ton profil Big Five 🧠</h3>
                        <p class="text-sm text-gray-500">
                            Réponds spontanément à ces 15 questions pour découvrir ton profil de personnalité.
                            Il n'y a pas de bonnes ou mauvaises réponses — sois simplement toi-même !
                        </p>
                    </div>

                    <form method="POST" action="{{ route('onboarding.postStep4') }}" x-data="{ activeTrait: null }">
                        @csrf

                        <div class="space-y-8">
                            @foreach($traits as $traitKey => $trait)
                            <div class="border rounded-lg overflow-hidden"
                                 @mouseenter="activeTrait = '{{ $traitKey }}'"
                                 @mouseleave="activeTrait = null"
                                 :class="{ 'ring-2 ring-indigo-300': activeTrait === '{{ $traitKey }}' }">
                                <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-3">
                                    <span class="text-2xl">{{ $trait['icon'] }}</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $trait['name'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ $trait['description'] }}</p>
                                    </div>
                                </div>

                                <div class="px-4 py-4 space-y-5">
                                    @foreach($trait['questions'] as $qIndex => $question)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-3">
                                            {{ $question['text'] }}
                                            @if($question['reversed'])
                                                <span class="text-xs text-gray-400 italic ml-1">(inversé)</span>
                                            @endif
                                        </p>
                                        <div class="flex items-center justify-between gap-1">
                                            @foreach($likertLabels as $value => $label)
                                            <label class="flex flex-col items-center gap-1 cursor-pointer flex-1">
                                                <input type="radio"
                                                       name="answers[{{ $traitKey }}][{{ $qIndex }}]"
                                                       value="{{ $value }}"
                                                       class="peer sr-only"
                                                       {{ $loop->first ? '' : '' }}
                                                       required>
                                                <span class="w-full text-center py-2 px-1 text-xs font-medium rounded border border-gray-200
                                                             peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600
                                                             hover:bg-gray-100 transition-colors">
                                                    {{ $label }}
                                                </span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('answers.*')" class="mt-4" />

                        <div class="flex justify-between mt-8 pt-4 border-t">
                            <a href="{{ route('onboarding.step3') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                ← Retour
                            </a>
                            <x-primary-button>Terminer l'inscription 🚀</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
