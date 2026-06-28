<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-primary-600 font-medium">📔 {{ __('Journal intime') }}</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ $journal->date->translatedFormat('l d F Y') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('journal.edit', $journal) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('journal.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <div class="prose prose-sm max-w-none dark:prose-invert whitespace-pre-line text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ $journal->content }}
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs text-gray-400">
                <span>{{ __('Écrit le') }} {{ $journal->created_at->translatedFormat('l d F Y à H:i') }}</span>
                @if($journal->created_at != $journal->updated_at)
                    <span>{{ __('Modifié le') }} {{ $journal->updated_at->translatedFormat('l d F Y à H:i') }}</span>
                @endif
            </div>
        </x-card>

        <div class="mt-6">
            <form method="POST" action="{{ route('journal.destroy', $journal) }}"
                  onsubmit="return confirm('{{ __('Supprimer cette note ?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('Supprimer') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
