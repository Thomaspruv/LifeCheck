<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ✏️ Modifier : {{ $goal->title }}
            </h2>
            <a href="{{ route('goals.show', $goal) }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" value="Titre" />
                        <x-text-input id="title"
                            name="title"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('title', $goal->title)"
                            required
                            maxlength="255" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description"
                            name="description"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            maxlength="2000">{{ old('description', $goal->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Target Date -->
                    <div>
                        <x-input-label for="target_date" value="Date cible" />
                        <x-text-input id="target_date"
                            name="target_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('target_date', $goal->target_date?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('goals.show', $goal) }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                            Annuler
                        </a>
                        <x-primary-button>
                            💾 Enregistrer
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
