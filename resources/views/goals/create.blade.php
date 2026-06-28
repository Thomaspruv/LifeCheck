<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 Nouvel objectif
            </h2>
            <a href="{{ route('goals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <form method="POST" action="{{ route('goals.store') }}" class="space-y-6"
                    x-data="{
                        milestones: [{ title: '', description: '' }],
                        addMilestone() {
                            this.milestones.push({ title: '', description: '' });
                        },
                        removeMilestone(index) {
                            this.milestones.splice(index, 1);
                        }
                    }">
                    @csrf

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" value="Titre de l'objectif" />
                        <x-text-input id="title"
                            name="title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="ex: Apprendre le piano"
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
                            placeholder="Décris ton objectif en détail..."
                            maxlength="2000">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Target Date -->
                    <div>
                        <x-input-label for="target_date" value="Date cible (optionnelle)" />
                        <x-text-input id="target_date"
                            name="target_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('target_date')"
                            min="{{ now()->format('Y-m-d') }}" />
                        <p class="text-xs text-gray-400 mt-1">Quand souhaites-tu accomplir cet objectif ?</p>
                        <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                    </div>

                    <!-- Milestones -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <x-input-label value="Jalons (étapes clés)" />
                            <button type="button" @click="addMilestone()"
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                                + Ajouter un jalon
                            </button>
                        </div>

                        <template x-for="(milestone, index) in milestones" :key="index">
                            <div class="flex items-start gap-2 mb-3 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1 space-y-2">
                                    <input type="text"
                                        :name="'milestones[' + index + '][title]'"
                                        x-model="milestone.title"
                                        placeholder="Titre du jalon"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        maxlength="255"
                                        required />
                                    <input type="text"
                                        :name="'milestones[' + index + '][description]'"
                                        x-model="milestone.description"
                                        placeholder="Description (optionnelle)"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        maxlength="1000" />
                                </div>
                                <button type="button"
                                    @click="removeMilestone(index)"
                                    x-show="milestones.length > 1"
                                    class="shrink-0 mt-1 text-red-400 hover:text-red-600 transition text-lg">
                                    ✕
                                </button>
                            </div>
                        </template>

                        <p class="text-xs text-gray-400 mt-1">Ex: "Apprendre les accords majeurs", "Jouer ma première chanson"...</p>
                        <x-input-error :messages="$errors->get('milestones')" class="mt-2" />
                    </div>

                    <!-- Examples -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">💡 Exemples d'objectifs</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                onclick="document.getElementById('title').value = 'Apprendre le piano'; document.getElementById('description').value = 'Apprendre à jouer du piano, des bases aux morceaux intermédiaires';"
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-full hover:bg-indigo-100 transition">🎹 Piano</button>
                            <button type="button"
                                onclick="document.getElementById('title').value = 'Lire 12 livres dans l\\'année'; document.getElementById('description').value = 'Lire un livre par mois pour enrichir ma culture';"
                                class="px-3 py-1 bg-purple-50 text-purple-600 text-xs rounded-full hover:bg-purple-100 transition">📚 Lecture</button>
                            <button type="button"
                                onclick="document.getElementById('title').value = 'Courir un semi-marathon'; document.getElementById('description').value = 'Préparation progressive pour courir 21 km';"
                                class="px-3 py-1 bg-green-50 text-green-600 text-xs rounded-full hover:bg-green-100 transition">🏃‍♂️ Course</button>
                            <button type="button"
                                onclick="document.getElementById('title').value = 'Apprendre le développement web'; document.getElementById('description').value = 'Maîtriser les fondamentaux du développement web full-stack';"
                                class="px-3 py-1 bg-orange-50 text-orange-600 text-xs rounded-full hover:bg-orange-100 transition">💻 Code</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('goals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                            Annuler
                        </a>
                        <x-primary-button>
                            🎯 Créer l'objectif
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
