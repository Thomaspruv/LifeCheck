<x-app-layout title="Nouveau tag — LifeCheck">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🏷️ Créer un tag') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tags.store') }}"
                      x-data="{
                        name: '{{ old('name') }}',
                        icon: '{{ old('icon', '🏷️') }}',
                        color: '{{ old('color', '#6366f1') }}',
                        preview: function() {
                            return this.icon || '🏷️';
                        }
                      }">
                    @csrf

                    <!-- Preview -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl border-2"
                             :style="'border-color: ' + color + '; background-color: ' + color + '15'">
                            <span class="text-4xl" x-text="preview()"></span>
                            <div>
                                <p class="font-semibold text-sm" :style="'color: ' + color" x-text="name || 'Nom du tag'"></p>
                                <span class="inline-block mt-1 w-8 h-1.5 rounded-full"
                                      :style="'background-color: ' + color"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nom du tag')" />
                            <x-text-input id="name"
                                name="name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ex: Travail, Famille, Santé..."
                                x-model="name"
                                maxlength="50"
                                autocomplete="off" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Icon / Emoji -->
                        <div>
                            <x-input-label for="icon" :value="__('Icône (emoji)')" />
                            <div class="mt-1 flex gap-2 flex-wrap">
                                @foreach(['🏷️','😊','😢','😡','😴','💪','❤️','🧠','💼','🏠','🎮','📚','🍎','☀️','🌧️','🎯','🌟','🧘','🏃','🎨'] as $emoji)
                                <button type="button"
                                        @click="icon = '{{ $emoji }}'"
                                        :class="icon === '{{ $emoji }}' ? 'ring-2 ring-indigo-500 scale-110' : 'hover:scale-110'"
                                        class="text-2xl p-2 rounded-lg transition transform border border-gray-200 hover:border-indigo-300">
                                    {{ $emoji }}
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="icon" x-model="icon">
                            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                        </div>

                        <!-- Color -->
                        <div>
                            <x-input-label for="color" :value="__('Couleur')" />
                            <div class="mt-1 flex items-center gap-3">
                                <input id="color"
                                    name="color"
                                    type="color"
                                    class="h-10 w-16 rounded-md border border-gray-300 cursor-pointer"
                                    x-model="color">
                                <span class="text-sm text-gray-500" x-text="color"></span>
                            </div>
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <a href="{{ route('tags.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            ← Retour
                        </a>
                        <x-primary-button>
                            🏷️ Créer le tag
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
