<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bienvenue ! Étape 1') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm">Étape 1/3</span>
                            <span class="text-sm text-gray-400">Choisis tes dimensions</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 33%"></div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-2">Quelles dimensions veux-tu suivre ?</h3>
                    <p class="text-sm text-gray-500 mb-6">Sélectionne au moins une dimension pour créer ton template de check-in.</p>

                    <form method="POST" action="{{ route('onboarding.postStep1') }}">
                        @csrf

                        <div x-data="{ selected: [] }" class="space-y-3">
                            @foreach($dimensions as $key => $label)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                   :class="{ 'border-indigo-500 bg-indigo-50': selected.includes('{{ $key }}') }">
                                <input type="checkbox"
                                       name="dimensions[]"
                                       value="{{ $key }}"
                                       x-model="selected"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                            <x-input-error :messages="$errors->get('dimensions')" class="mt-2" />
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button>Étape suivante →</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
