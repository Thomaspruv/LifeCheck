<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 Nouveau défi
            </h2>
            <a href="{{ route('challenges.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <form method="POST" action="{{ route('challenges.store') }}" class="space-y-6">
                    @csrf

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" value="Titre du défi" />
                        <x-text-input id="title"
                            name="title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="ex: 7 jours sans sucre"
                            :value="old('title')"
                            required
                            maxlength="255" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="Description (optionnelle)" />
                        <textarea id="description"
                            name="description"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="ex: Ne pas consommer de sucre ajouté pendant 7 jours"
                            maxlength="1000">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Duration -->
                    <div>
                        <x-input-label for="duration_days" value="Durée (en jours)" />
                        <x-text-input id="duration_days"
                            name="duration_days"
                            type="number"
                            class="mt-1 block w-full"
                            min="1"
                            max="365"
                            :value="old('duration_days', 7)"
                            required />
                        <p class="text-xs text-gray-400 mt-1">Suggestions : 7, 14, 21, 30, 60, 100 jours</p>
                        <x-input-error :messages="$errors->get('duration_days')" class="mt-2" />
                    </div>

                    <!-- Examples -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">💡 Idées de défis</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="document.getElementById('title').value = '7 jours sans sucre'; document.getElementById('duration_days').value = 7; document.getElementById('description').value = 'Éviter tous les aliments avec sucre ajouté pendant une semaine';"
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-full hover:bg-indigo-100 transition">🍬 Sans sucre (7j)</button>
                            <button type="button" onclick="document.getElementById('title').value = '30 jours de méditation'; document.getElementById('duration_days').value = 30; document.getElementById('description').value = 'Méditer au moins 10 minutes chaque jour';"
                                class="px-3 py-1 bg-purple-50 text-purple-600 text-xs rounded-full hover:bg-purple-100 transition">🧘 Méditation (30j)</button>
                            <button type="button" onclick="document.getElementById('title').value = '21 jours de sport quotidien'; document.getElementById('duration_days').value = 21; document.getElementById('description').value = 'Faire au moins 20 minutes d\'exercice par jour';"
                                class="px-3 py-1 bg-green-50 text-green-600 text-xs rounded-full hover:bg-green-100 transition">🏃‍♂️ Sport (21j)</button>
                            <button type="button" onclick="document.getElementById('title').value = '14 jours sans réseaux sociaux'; document.getElementById('duration_days').value = 14; document.getElementById('description').value = 'Ne pas utiliser les réseaux sociaux personnels';"
                                class="px-3 py-1 bg-orange-50 text-orange-600 text-xs rounded-full hover:bg-orange-100 transition">📵 Digital detox (14j)</button>
                            <button type="button" onclick="document.getElementById('title').value = 'Lire 20 pages par jour'; document.getElementById('duration_days').value = 30; document.getElementById('description').value = 'Lire au moins 20 pages d\'un livre chaque jour';"
                                class="px-3 py-1 bg-blue-50 text-blue-600 text-xs rounded-full hover:bg-blue-100 transition">📚 Lecture (30j)</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('challenges.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                            Annuler
                        </a>
                        <x-primary-button>
                            🚀 Lancer le défi
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
