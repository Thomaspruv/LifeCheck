<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le template') }} : {{ $template->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('templates.update', $template) }}" x-data="editForm()">
                        @csrf
                        @method('PUT')

                        <!-- Template name -->
                        <div class="mb-6">
                            <x-input-label for="name" value="Nom du template" />
                            <x-text-input id="name"
                                          name="name"
                                          type="text"
                                          class="mt-1 block w-full"
                                          x-model="name"
                                          placeholder="Ex: Mon check-in matinal" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Default checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_default"
                                       value="1"
                                       x-model="isDefault"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Template par défaut</span>
                            </label>
                        </div>

                        <!-- Current / new items -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-medium">Dimensions</h3>
                                <button type="button"
                                        @click="addItem()"
                                        class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                    + Ajouter
                                </button>
                            </div>

                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="p-4 border rounded-lg mb-3">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-medium text-gray-700">
                                            Dimension <span x-text="idx + 1"></span>
                                        </h4>
                                        <button type="button"
                                                @click="removeItem(idx)"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium">
                                            Supprimer
                                        </button>
                                    </div>

                                    <input type="hidden" :name="'items[' + idx + '][id]'" x-model="item.id" x-show="item.id">

                                    <!-- Label -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                                        <input type="text"
                                               :name="'items[' + idx + '][label]'"
                                               x-model="item.label"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <p class="mt-1 text-xs text-gray-400" x-text="dimensionsLabels[item.label] ? (dimensionsLabels[item.label] + ' (dimension prédéfinie)') : 'Dimension personnalisée'"></p>
                                    </div>

                                    <!-- Input type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'input</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <template x-for="(typeLabel, typeKey) in inputTypes" :key="typeKey">
                                                <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50"
                                                       :class="{ 'border-indigo-500 bg-indigo-50': item.input_type === typeKey }">
                                                    <input type="radio"
                                                           :name="'items[' + idx + '][input_type]'"
                                                           :value="typeKey"
                                                           x-model="item.input_type"
                                                           class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-600" x-text="typeLabel"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="items.length === 0">
                                <p class="text-sm text-gray-400 italic">Aucune dimension. Clique sur « + Ajouter » pour ajouter une dimension.</p>
                            </template>

                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <div class="flex justify-between mt-6">
                            <a href="{{ route('templates.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                ← Retour
                            </a>
                            <x-primary-button>Enregistrer les modifications</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editForm() {
            return {
                name: '{{ $template->name }}',
                isDefault: {{ $template->is_default ? 'true' : 'false' }},
                items: @json($template->items->map(function($item) {
                    return ['id' => $item->id, 'label' => $item->label, 'input_type' => $item->input_type];
                })),
                dimensionsLabels: @json($dimensions),
                inputTypes: @json($inputTypes),
                addItem() {
                    this.items.push({ id: null, label: '', input_type: 'slider' });
                },
                removeItem(idx) {
                    this.items.splice(idx, 1);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
