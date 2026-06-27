<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Étape 3 : Récapitulatif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm">Étape 3/3</span>
                            <span class="text-sm text-gray-400">Vérifie et confirme</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-2">Ton template de check-in</h3>
                    <p class="text-sm text-gray-500 mb-6">Vérifie les dimensions et leurs types avant de confirmer.</p>

                    <div class="space-y-3 mb-6">
                        @foreach($items as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                            <span class="text-sm px-3 py-1 bg-white rounded-full border">
                                {{ $inputTypeLabels[$item['input_type']] ?? $item['input_type'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('onboarding.store') }}">
                        @csrf
                        <div class="flex justify-between">
                            <a href="{{ route('onboarding.step2') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                ← Modifier
                            </a>
                            <x-primary-button>C'est parti ! 🚀</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
