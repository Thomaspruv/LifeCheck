<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-primary-600 font-medium">📔 {{ __('Journal intime') }}</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ __('Mes notes du journal') }}
                </h2>
            </div>
            <a href="{{ route('journal.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-200 hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Écrire une note') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-4">
        @forelse($entries as $entry)
            <x-card class="transition-all duration-200 hover:shadow-card-hover group">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $entry->date->translatedFormat('l d F Y') }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $entry->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 whitespace-pre-line">
                            {{ $entry->content }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                        <a href="{{ route('journal.show', $entry) }}"
                           class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 transition-colors"
                           title="{{ __('Voir') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('journal.edit', $entry) }}"
                           class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700 transition-colors"
                           title="{{ __('Modifier') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </x-card>
        @empty
            <x-card class="text-center py-12">
                <div class="text-5xl mb-4">📔</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('Pas encore de notes') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    {{ __('Écris tes pensées, réflexions et moments marquants de chaque journée. Ton journal intime t\'accompagne au fil du temps.') }}
                </p>
                <a href="{{ route('journal.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-200 hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Écrire ma première note') }}
                </a>
            </x-card>
        @endforelse

        <div class="pb-8">
            {{ $entries->links() }}
        </div>
    </div>
</x-app-layout>
