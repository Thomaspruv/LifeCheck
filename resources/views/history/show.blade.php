<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Historique'), 'url' => route('history.index')], ['label' => __('Détail du check-in')]]">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du check-in') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-2xl font-bold text-gray-800">{{ $checkin->date->format('l d F Y') }}</p>
                        @if($checkin->template)
                        <p class="text-sm text-gray-500">Template : {{ $checkin->template->name }}</p>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @foreach($checkin->items as $item)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-700 text-sm mb-2">{{ $item->templateItem?->label ?? '?' }}</p>
                            <div class="text-lg">
                                @if($item->templateItem && $item->templateItem->input_type === 'slider')
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">{{ ['😢','😟','😐','🙂','😊','😄','😁','🥳','🤩','💖'][(int)$item->value - 1] ?? '😐' }}</span>
                                        <span class="text-gray-800 font-bold">{{ $item->value }}/10</span>
                                    </div>
                                @elseif($item->templateItem && $item->templateItem->input_type === 'emoji')
                                    <span class="text-3xl">{{ $item->value }}</span>
                                @elseif($item->templateItem && $item->templateItem->input_type === 'checkbox')
                                    @php $vals = json_decode($item->value, true) ?: [$item->value]; @endphp
                                    <ul class="list-disc list-inside text-sm text-gray-600">
                                        @foreach($vals as $v)
                                        <li>{{ $v }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-800">{{ $item->value }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($checkin->notes)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                        <p class="font-medium text-gray-700 text-sm mb-1">Notes</p>
                        <p class="text-gray-600">{{ $checkin->notes }}</p>
                    </div>
                    @endif

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('history.index') }}" class="text-indigo-600 hover:underline">← Retour à l'historique</a>
                        <a href="{{ route('trends') }}" class="text-indigo-600 hover:underline">Voir les tendances →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
