<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un template') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('templates.store') }}" x-data="templateForm()">
                        @csrf

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
                                <span class="ml-2 text-sm text-gray-600">Définir comme template par défaut</span>
                            </label>
                        </div>

                        <!-- Dimensions selection -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-2">Dimensions à inclure</h3>
                            <p class="text-sm text-gray-500 mb-4">Sélectionne les dimensions que tu souhaites suivre.</p>

                            <div class="space-y-2">
                                @foreach ($dimensions as $key => $label)
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                           :class="{ 'border-indigo-500 bg-indigo-50': selectedDimensions.includes('{{ $key }}') }">
                                        <input type="checkbox"
                                               value="{{ $key }}"
                                               x-model="selectedDimensions"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <!-- Input type configuration (shown when dimensions are selected) -->
                        <template x-if="selectedDimensions.length > 0">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-2">Type de réponse par dimension</h3>
                                <p class="text-sm text-gray-500 mb-4">Choisis comment évaluer chaque dimension.</p>

                                <div class="space-y-4">
                                    <template x-for="(dim, idx) in selectedDimensions" :key="dim">
                                        <div class="p-4 border rounded-lg">
                                            <h4 class="font-medium text-gray-700 mb-3" x-text="dimensionsLabels[dim]"></h4>
                                            <div class="grid grid-cols-2 gap-2">
                                                <template x-for="(typeLabel, typeKey) in inputTypes" :key="typeKey">
                                                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50"
                                                           :class="{ 'border-indigo-500 bg-indigo-50': getInputType(dim) === typeKey }">
                                                        <input type="radio"
                                                               :name="'items[' + idx + '][input_type]'"
                                                               :value="typeKey"
                                                               x-model="inputTypeSelections[dim]"
                                                               class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                        <span class="ml-2 text-sm text-gray-600" x-text="typeLabel"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-between mt-6">
                            <a href="{{ route('templates.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                ← Retour
                            </a>
                            <x-primary-button>Créer le template</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function templateForm() {
            return {
                name: '',
                isDefault: false,
                selectedDimensions: [],
                inputTypeSelections: {},
                dimensionsLabels: @json($dimensions),
                inputTypes: @json($inputTypes),
                getInputType(dim) {
                    return this.inputTypeSelections[dim] || 'slider';
                },
                init() {
                    this.$watch('selectedDimensions', (newVal) => {
                        // Remove selections for unselected dimensions
                        Object.keys(this.inputTypeSelections).forEach(key => {
                            if (!newVal.includes(key)) {
                                delete this.inputTypeSelections[key];
                            }
                        });
                        // Auto-set default input type for new dimensions
                        newVal.forEach(dim => {
                            if (!this.inputTypeSelections[dim]) {
                                this.inputTypeSelections[dim] = 'slider';
                            }
                        });
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
