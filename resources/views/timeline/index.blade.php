<x-app-layout title="Timeline — LifeCheck"
    seoDescription="Frise chronologique interactive de votre parcours de bien-être : visualisez l'évolution de votre humeur jour après jour.">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📜 {{ __('Frise chronologique') }}
            </h2>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-500">🔥 {{ $streak }} jours</span>
                <span class="text-gray-500">📊 {{ $totalCheckins }} check-ins</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="timelineApp()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <!-- Month Navigation (horizontal scrollable tabs) -->
            <div class="mb-8 overflow-x-auto" x-ref="monthNav">
                <div class="flex gap-2 min-w-max px-2 pb-2">
                    <template x-for="(month, idx) in months" :key="month.key">
                        <button @click="scrollToMonth(idx)"
                            :class="activeMonth === idx
                                ? 'bg-indigo-600 text-white shadow-md'
                                : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                            class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                            <span x-text="month.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            @if(count($timelineData) === 0)
                <!-- Empty state -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-16 text-center">
                    <div class="text-6xl mb-4">📜</div>
                    <p class="text-gray-400 text-lg mb-2">Aucun check-in pour le moment.</p>
                    <p class="text-gray-400 text-sm mb-6">Commence à faire tes check-ins pour voir ta frise chronologique.</p>
                    <a href="{{ route('checkin.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-500 transition-all transform hover:scale-105">
                        ✍️ Faire mon premier check-in
                    </a>
                </div>
            @else
                <!-- Timeline -->
                <div class="relative">
                    <!-- Central timeline line -->
                    <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-0.5 bg-gradient-to-b from-indigo-300 via-indigo-400 to-purple-300 transform -translate-x-1/2 hidden md:block"></div>
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-indigo-300 via-indigo-400 to-purple-300 block md:hidden"></div>

                    @foreach($timelineData as $monthIdx => $month)
                    <div class="mb-12 month-section"
                         x-ref="month-{{ $monthIdx }}"
                         :data-month-idx="{{ $monthIdx }}"
                         data-month="{{ $month['month_label'] }}">

                        <!-- Month header -->
                        <div class="flex items-center gap-3 mb-6 relative z-10">
                            <div class="flex items-center justify-center w-16 h-8 rounded-full bg-indigo-100 border-2 border-indigo-300 text-indigo-700 text-sm font-bold md:ml-0 ml-0">
                                📅
                            </div>
                            <h3 class="text-lg font-bold text-indigo-700">{{ $month['month_label'] }}</h3>
                            <div class="flex-1 h-px bg-gradient-to-r from-indigo-200 to-transparent"></div>
                        </div>

                        @foreach($month['entries'] as $entry)
                        @php
                            $mood = $entry['avg_mood'];
                            $moodColor = match(true) {
                                $mood === null => 'gray',
                                $mood >= 8 => 'green',
                                $mood >= 6 => 'indigo',
                                $mood >= 4 => 'yellow',
                                $mood >= 2 => 'orange',
                                default => 'red',
                            };
                            $moodDotColor = match($moodColor) {
                                'green' => 'bg-green-400 border-green-500',
                                'indigo' => 'bg-indigo-400 border-indigo-500',
                                'yellow' => 'bg-yellow-400 border-yellow-500',
                                'orange' => 'bg-orange-400 border-orange-500',
                                'red' => 'bg-red-400 border-red-500',
                                default => 'bg-gray-300 border-gray-400',
                            };
                            $moodRingColor = match($moodColor) {
                                'green' => 'ring-green-200',
                                'indigo' => 'ring-indigo-200',
                                'yellow' => 'ring-yellow-200',
                                'orange' => 'ring-orange-200',
                                'red' => 'ring-red-200',
                                default => 'ring-gray-200',
                            };
                            $moodEmoji = match(true) {
                                $mood === null => ($entry['mood_emoji'] ?? '❓'),
                                $mood >= 8 => '🌟',
                                $mood >= 6 => '😊',
                                $mood >= 4 => '😐',
                                $mood >= 2 => '😟',
                                default => '😢',
                            };
                            $isEven = $loop->even;
                        @endphp

                        <!-- Timeline entry -->
                        <div class="relative mb-8 group">
                            <!-- Desktop: alternate left/right -->
                            <div class="md:flex md:items-start {{ $isEven ? 'md:flex-row-reverse' : '' }}">
                                <!-- Content card -->
                                <div class="md:w-[calc(50%-2rem)] w-full pl-16 md:pl-0">
                                    <a href="{{ route('history.show', $entry['id']) }}"
                                       class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-{{ $moodColor }}-200 transition-all duration-200 group/card">

                                        <!-- Card header: date + mood -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ $entry['date']->format('l d') }}
                                                </span>
                                                <span class="text-xs text-gray-400 ml-2">
                                                    {{ $entry['date']->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($entry['emotion_tags']->count() > 0)
                                                    <span class="text-xs text-gray-400">{{ $entry['item_count'] }} réponses</span>
                                                @endif
                                                <span class="text-2xl" title="Humeur">{{ $moodEmoji }}</span>
                                            </div>
                                        </div>

                                        <!-- Mood bar -->
                                        @if($mood !== null)
                                        <div class="mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-500 ease-out"
                                                         style="width: {{ $mood * 10 }}%; background-color: {{ match($moodColor) { 'green' => '#22c55e', 'indigo' => '#6366f1', 'yellow' => '#eab308', 'orange' => '#f97316', 'red' => '#ef4444', default => '#9ca3af' } }};">
                                                    </div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-500 w-8 text-right">{{ $mood }}/10</span>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Notes -->
                                        @if($entry['notes'])
                                        <p class="text-sm text-gray-600 italic mb-3 line-clamp-2">
                                            "{{ $entry['notes'] }}"
                                        </p>
                                        @endif

                                        <!-- Emotion tags -->
                                        @if($entry['emotion_tags']->count() > 0)
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($entry['emotion_tags'] as $tag)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color: {{ $tag->color }}15; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30;">
                                                {{ $tag->icon }} {{ $tag->name }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif

                                        <!-- View detail arrow -->
                                        <div class="mt-3 flex justify-end">
                                            <span class="text-xs text-gray-400 group-hover/card:text-indigo-500 transition-colors flex items-center gap-1">
                                                Voir le détail →
                                            </span>
                                        </div>
                                    </a>
                                </div>

                                <!-- Timeline dot -->
                                <div class="absolute left-8 md:left-1/2 top-6 transform -translate-x-1/2 z-20">
                                    <div class="w-5 h-5 rounded-full {{ $moodDotColor }} border-2 ring-4 {{ $moodRingColor }} shadow-sm transition-all duration-200 group-hover:scale-125 group-hover:shadow-md">
                                    </div>
                                </div>

                                <!-- Desktop spacer (alternate layout) -->
                                <div class="hidden md:block md:w-[calc(50%-2rem)]"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                    <!-- Bottom anchor -->
                    <div class="text-center py-6">
                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-50 rounded-full text-sm text-indigo-600 font-medium">
                            <span>🏁</span>
                            <span>Début du parcours</span>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-400"></span> Excellent (8-10)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-400"></span> Bien (6-7)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400"></span> Mitigé (4-5)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400"></span> Difficile (2-3)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span> Très difficile (1)</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('timelineApp', () => ({
                months: [],
                activeMonth: 0,
                observer: null,

                init() {
                    // Build months array from DOM
                    const sections = this.$el.querySelectorAll('.month-section');
                    this.months = Array.from(sections).map((el, i) => ({
                        key: el.dataset.monthIdx,
                        label: el.dataset.month,
                        idx: i,
                    }));

                    // Intersection Observer for active month tracking
                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const idx = parseInt(entry.target.dataset.monthIdx);
                                if (!isNaN(idx)) {
                                    this.activeMonth = idx;
                                    this.centerActiveTab();
                                }
                            }
                        });
                    }, { threshold: 0.3, rootMargin: '-80px 0px -30% 0px' });

                    sections.forEach(s => this.observer.observe(s));
                },

                scrollToMonth(idx) {
                    const el = this.$refs['month-' + idx];
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        this.activeMonth = idx;
                    }
                },

                centerActiveTab() {
                    const nav = this.$refs.monthNav;
                    if (!nav) return;
                    const activeBtn = nav.querySelector('button.bg-indigo-600');
                    if (activeBtn) {
                        activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                },

                destroy() {
                    if (this.observer) this.observer.disconnect();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
