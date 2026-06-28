<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Check-in')]]">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-primary-600 font-medium">
                    @if($isCatchUp)
                        ⏪ {{ __('Rattrapage') }}
                    @else
                        📅 {{ now()->translatedFormat('l d F Y') }}
                    @endif
                </p>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">
                    @if($isCatchUp)
                        {{ __('Comment ça allait ce jour-là ?') }}
                    @else
                        {{ __('Comment ça va aujourd\'hui ?') }}
                    @endif
                </h2>
            </div>
            @if($isCatchUp)
                <x-badge variant="warning" dot="true">
                    {{ \Carbon\Carbon::parse($targetDate)->translatedFormat('d F Y') }}
                </x-badge>
            @endif
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <form method="POST" action="{{ route('checkin.store') }}"
              x-data="{
                values: {},
                completedItems: new Set(),
                emojis: {1:'😢',2:'😟',3:'😐',4:'🙂',5:'😊',6:'😄',7:'😁',8:'🥳',9:'🤩',10:'💖'},
                getEmoji(val) { return this.emojis[val] || '😐' },
                totalItems: {{ $template->items->count() }},
                get completedCount() {
                    let c = 0;
                    @foreach($template->items as $item)
                        @if($item->input_type === 'slider' || $item->input_type === 'emoji')
                            if(this.values['{{ $item->id }}']) c++;
                        @elseif($item->input_type === 'text')
                            if(this.values['{{ $item->id }}'] && this.values['{{ $item->id }}'].trim()) c++;
                        @elseif($item->input_type === 'checkbox')
                            if(this.values['{{ $item->id }}'] && this.values['{{ $item->id }}'].length > 0) c++;
                        @endif
                    @endforeach
                    return c;
                },
                get progressPercent() {
                    return this.totalItems > 0 ? Math.round((this.completedCount / this.totalItems) * 100) : 0;
                },
                get progressLabel() {
                    if(this.progressPercent === 0) return '{{ __("Commence ton check-in") }}';
                    if(this.progressPercent < 50) return '{{ __("Bien, continue !") }}';
                    if(this.progressPercent < 100) return '{{ __("Presque fini !") }}';
                    return '{{ __("Complété ! 🎉") }}';
                }
              }"
              @submit="document.querySelector('#submit-btn').disabled = true; document.querySelector('#submit-btn').classList.add('opacity-75');">

            @csrf

            @if($isCatchUp)
                <input type="hidden" name="date" value="{{ $targetDate }}">
            @endif

            {{-- Progress Bar --}}
            <x-card class="!p-4">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700" x-text="progressLabel"></span>
                            <span class="text-primary-600 font-semibold" x-text="completedCount + '/' + totalItems"></span>
                        </div>
                        <div class="progress-bar h-2.5">
                            <div class="progress-bar-fill bg-primary-500" :style="'width: ' + progressPercent + '%'"
                                 :class="progressPercent === 100 ? '!bg-emerald-500' : ''"></div>
                        </div>
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
                    <div class="text-3xl" x-show="progressPercent === 100">🎉</div>
                </div>
            </x-card>

            {{-- Form Items --}}
            <div class="space-y-4">
                @foreach($template->items as $index => $item)
                <x-card class="transition-all duration-200 hover:shadow-card-hover"
                        x-data="{ itemOpen: true }">
                    <div class="flex items-start gap-3">
                        {{-- Step number --}}
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-bold">
                            {{ $index + 1 }}
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Label --}}
                            <label class="block font-semibold text-gray-900 mb-3">{{ $item->label }}</label>

                            @switch($item->input_type)
                                @case('slider')
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-xs text-gray-400">
                                            <span>1</span>
                                            <span class="font-medium text-primary-600 text-sm" x-text="getEmoji(values['{{ $item->id }}']) + ' ' + (values['{{ $item->id }}'] || '—')"></span>
                                            <span>10</span>
                                        </div>
                                        <input type="range"
                                               name="value_{{ $item->id }}"
                                               min="1" max="10"
                                               class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-primary-500
                                                      [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-primary-500 [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:cursor-pointer
                                                      [&::-moz-range-thumb]:w-5 [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-primary-500 [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:shadow-md [&::-moz-range-thumb]:cursor-pointer"
                                               x-model="values['{{ $item->id }}']">
                                        <div class="flex justify-between px-1">
                                            @foreach([1,2,3,4,5,6,7,8,9,10] as $v)
                                                <button type="button"
                                                        @click="values['{{ $item->id }}'] = {{ $v }}"
                                                        class="text-lg transition-transform duration-100 hover:scale-125"
                                                        :class="values['{{ $item->id }}'] == {{ $v }} ? 'scale-125' : 'opacity-30 grayscale'"
                                                        x-show="values['{{ $item->id }}'] != {{ $v }}">
                                                    {{ ['😢','😟','😐','🙂','😊','😄','😁','🥳','🤩','💖'][$v-1] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @break

                                @case('emoji')
                                    @php $emojiOldValue = old("value_" . $item->id, ''); @endphp
                                    <div x-data="{ selected: '{{ $emojiOldValue }}' }" class="grid grid-cols-4 gap-2">
                                        @foreach([['😢','😢'],['😟','😟'],['😐','😐'],['🙂','🙂'],['😊','😊'],['😄','😄'],['😁','😁'],['🥳','🥳']] as [$emoji])
                                        <button type="button"
                                                @click="selected = '{{ $emoji }}'; values['{{ $item->id }}'] = '{{ $emoji }}'"
                                                :class="selected === '{{ $emoji }}' ? 'ring-2 ring-primary-500 scale-110 bg-primary-50 border-primary-200' : 'hover:bg-gray-50 hover:scale-105 border-gray-200'"
                                                class="text-3xl p-3 rounded-xl border transition-all duration-150 flex items-center justify-center aspect-square">
                                            {{ $emoji }}
                                        </button>
                                        @endforeach
                                        <input type="hidden" name="value_{{ $item->id }}" x-model="selected">
                                    </div>
                                    @break

                                @case('text')
                                    <textarea name="value_{{ $item->id }}"
                                              rows="3"
                                              class="w-full rounded-xl border-gray-200 shadow-sm transition-all duration-150 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none text-sm"
                                              placeholder="{{ __('Écris ce que tu ressens...') }}"
                                              x-model="values['{{ $item->id }}']">{{ old('value_' . $item->id) }}</textarea>
                                    @break

                                @case('checkbox')
                                    <div x-data="{ checked: {{ json_encode(old('value_' . $item->id, [])) }} }" class="space-y-2">
                                        @foreach(['✅ Oui', '❌ Non', '🤷 Pas sûr'] as $opt)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer transition-all duration-150 hover:border-primary-200 hover:bg-primary-50/30 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500/20">
                                            <input type="checkbox"
                                                   name="value_{{ $item->id }}[]"
                                                   value="{{ $opt }}"
                                                   class="rounded-lg border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                                                   x-model="checked"
                                                   @change="values['{{ $item->id }}'] = checked">
                                            <span class="text-sm font-medium text-gray-700">{{ $opt }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @break
                            @endswitch

                            <x-input-error :messages="$errors->get('value_' . $item->id)" class="mt-2" />
                        </div>
                    </div>
                </x-card>
                @endforeach
            </div>

            {{-- Notes --}}
            <x-card title="📝 {{ __('Notes') }}" subtitle="{{ __('Optionnel — ajoute un petit mot sur ta journée') }}">
                <textarea name="notes" rows="3"
                          class="w-full rounded-xl border-gray-200 shadow-sm transition-all duration-150 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none text-sm"
                          placeholder="{{ __('Un petit mot sur ta journée...') }}">{{ old('notes') }}</textarea>
            </x-card>

            {{-- Tags --}}
            @if($tags->count() > 0)
            <x-card title="🏷️ {{ __('Tags émotionnels') }}" subtitle="{{ __('Associe des tags à ton humeur du jour') }}">
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <label class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border-2 cursor-pointer transition-all duration-150 hover:scale-105 hover:shadow-sm"
                           style="border-color: {{ $tag->color }}; {{ old('tags') && in_array($tag->id, old('tags')) ? 'background-color: ' . $tag->color . '20' : '' }}"
                           x-data="{ checked: {{ in_array($tag->id, old('tags', [])) ? 'true' : 'false' }} }"
                           :class="checked ? 'shadow-sm ring-2 ring-offset-1' : ''"
                           :style="checked ? 'border-color: {{ $tag->color }}; background-color: {{ $tag->color }}20; --tw-ring-color: {{ $tag->color }}' : ''"
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
                <div class="mt-3">
                    <a href="{{ route('tags.index') }}" class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Gérer mes tags') }}
                    </a>
                </div>
            </x-card>
            @else
            <x-card>
                <div class="text-center py-4">
                    <p class="text-sm text-gray-400">
                        🏷️ {{ __("Tu peux créer des tags personnalisés") }}
                        <a href="{{ route('tags.create') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            {{ __('pour catégoriser tes émotions') }}
                        </a>
                    </p>
                </div>
            </x-card>
            @endif

            {{-- Submit --}}
            <div class="flex items-center justify-between gap-4 pt-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Retour') }}
                </a>
                <x-primary-button size="lg" id="submit-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Valider mon check-in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
