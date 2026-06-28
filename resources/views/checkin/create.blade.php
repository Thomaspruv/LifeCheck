<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Check-in quotidien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-6">
                        @if($isCatchUp)
                            <p class="text-xs text-amber-500 font-medium mb-1">⏪ Rattrapage</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($targetDate)->translatedFormat('l d F Y') }}</p>
                            <h3 class="text-lg font-semibold mt-1">Comment ça allait ce jour-là ?</h3>
                        @else
                            <p class="text-sm text-gray-500">{{ now()->format('l d F Y') }}</p>
                            <h3 class="text-lg font-semibold mt-1">Comment ça va aujourd'hui ?</h3>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('checkin.store') }}"
                          x-data="{
                            values: {},
                            emojis: {1:'😢',2:'😟',3:'😐',4:'🙂',5:'😊',6:'😄',7:'😁',8:'🥳',9:'🤩',10:'💖'},
                            getEmoji(val) { return this.emojis[val] || '😐' }
                          }">
                        @csrf

                        @if($isCatchUp)
                            <input type="hidden" name="date" value="{{ $targetDate }}">
                        @endif

                        <div class="space-y-6">
                            @foreach($template->items as $item)
                            <div class="p-4 border rounded-lg" x-data="{ open: false }">
                                <label class="block font-medium text-gray-700 mb-2">{{ $item->label }}</label>

                                @switch($item->input_type)
                                    @case('slider')
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm text-gray-400 w-8 text-center">1</span>
                                            <input type="range"
                                                   name="value_{{ $item->id }}"
                                                   min="1" max="10"
                                                   class="w-full accent-indigo-600"
                                                   x-model="values['{{ $item->id }}']">
                                            <span class="text-sm text-gray-400 w-8 text-center">10</span>
                                            <span class="text-2xl ml-2" x-text="getEmoji(values['{{ $item->id }}'])">😐</span>
                                        </div>
                                        @break

                                    @case('emoji')
                                        <div x-data="{ selected: @js(old('value_'.$item->id, '')) }" class="flex gap-3 flex-wrap justify-center">
                                            @foreach(['😢','😟','😐','🙂','😊','😄','😁','🥳'] as $emoji)
                                            <button type="button"
                                                    @click="selected = '{{ $emoji }}'"
                                                    :class="selected === '{{ $emoji }}' ? 'ring-2 ring-indigo-500 scale-110' : 'hover:scale-110'"
                                                    class="text-3xl p-2 rounded-lg transition transform">
                                                {{ $emoji }}
                                            </button>
                                            @endforeach
                                            <input type="hidden" name="value_{{ $item->id }}" x-model="selected">
                                        </div>
                                        @break

                                    @case('text')
                                        <textarea name="value_{{ $item->id }}"
                                                  rows="2"
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Écris ce que tu ressens...">{{ old('value_'.$item->id) }}</textarea>
                                        @break

                                    @case('checkbox')
                                        <div x-data="{ checked: @js(old('value_'.$item->id, [])) }" class="space-y-2">
                                            @foreach(['✅ Oui', '❌ Non', '🤷 Pas sûr'] as $opt)
                                            <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                                                <input type="checkbox"
                                                       name="value_{{ $item->id }}[]"
                                                       value="{{ $opt }}"
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm">{{ $opt }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                        @break
                                @endswitch

                                <x-input-error :messages="$errors->get('value_'.$item->id)" class="mt-1" />
                            </div>
                            @endforeach
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label class="block font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                            <textarea name="notes" rows="2"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Un petit mot sur ta journée...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Tags -->
                        @if($tags->count() > 0)
                        <div class="mt-6 p-4 border rounded-lg">
                            <label class="block font-medium text-gray-700 mb-3">🏷️ Tags émotionnels</label>
                            <p class="text-xs text-gray-400 mb-3">Associe des tags à ton humeur du jour.</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 border-2 rounded-full cursor-pointer transition-all hover:scale-105"
                                       style="border-color: {{ $tag->color }}; {{ old('tags') && in_array($tag->id, old('tags')) ? 'background-color: ' . $tag->color . '20' : '' }}"
                                       x-data="{ checked: {{ in_array($tag->id, old('tags', [])) ? 'true' : 'false' }} }"
                                       :class="checked ? 'shadow-sm' : ''"
                                       @click="checked = !checked">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           class="hidden"
                                           {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                           x-model="checked">
                                    <span>{{ $tag->icon }}</span>
                                    <span class="text-sm font-medium" style="color: {{ $tag->color }}">{{ $tag->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('tags.index') }}" class="text-xs text-indigo-500 hover:underline">
                                    ✏️ Gérer mes tags
                                </a>
                            </div>
                        </div>
                        @else
                        <div class="mt-6 p-4 border border-dashed rounded-lg text-center">
                            <p class="text-xs text-gray-400">
                                🏷️ Tu peux <a href="{{ route('tags.create') }}" class="text-indigo-500 hover:underline">créer des tags personnalisés</a>
                                pour catégoriser tes émotions.
                            </p>
                        </div>
                        @endif

                        <div class="flex justify-end mt-6">
                            <x-primary-button class="px-8 py-3 text-lg">
                                ✅ Valider mon check-in
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
