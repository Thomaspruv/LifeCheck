<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-primary-600 font-medium">📔 {{ __('Journal intime') }}</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ __('Modifier la note du') }} {{ $journal->date->translatedFormat('l d F Y') }}
                </h2>
            </div>
            <a href="{{ route('journal.show', $journal) }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Annuler') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('journal.update', $journal) }}"
              x-data="{ content: {{ json_encode($journal->content) }} }">
            @csrf
            @method('PUT')

            <x-card>
                <div class="space-y-4">
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Que s\'est-il passé ce jour-là ?') }}
                        </label>
                        <textarea name="content" id="content" rows="12"
                                  x-model="content"
                                  class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 shadow-sm transition-all duration-150 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-y text-sm"
                                  placeholder="{{ __('Écris librement...') }}">{{ old('content', $journal->content) }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span x-text="content.length + '/10000'"></span>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-between gap-4 mt-6">
                <a href="{{ route('journal.show', $journal) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Annuler') }}
                </a>
                <x-primary-button size="lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Enregistrer les modifications') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
