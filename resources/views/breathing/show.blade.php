<x-app-layout :title="$exercise->name . ' — LifeCheck'"
    :seoDescription="$exercise->description">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('breathing.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    ← Retour
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $exercise->icon }} {{ $exercise->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Timer / Animation Area -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8"
                         x-data="breathingApp(@json($exercise->pattern_data), @json($exercise->type))">
                        @if ($exercise->type === 'breathing' && $exercise->pattern_data)
                            <!-- Breathing Exercise Timer -->
                            <div class="text-center mb-8">
                                <!-- Breathing Circle Animation -->
                                <div class="relative mx-auto mb-6"
                                     style="width: 220px; height: 220px;">
                                    <!-- Outer ring -->
                                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                                    <!-- Inner circle -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="rounded-full transition-all duration-700 ease-in-out flex items-center justify-center"
                                             :style="{
                                                 width: circleSize + 'px',
                                                 height: circleSize + 'px',
                                                 backgroundColor: circleColor,
                                                 opacity: circleOpacity,
                                                 transform: 'scale(' + circleScale + ')',
                                                 transition: 'all ' + stepDuration + 'ms ease-in-out',
                                             }">
                                            <span class="text-white font-bold text-lg drop-shadow-md"
                                                  x-text="phaseLabel"></span>
                                        </div>
                                    </div>
                                    <!-- Timer display -->
                                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-100">
                                        <span class="text-sm font-mono font-bold text-gray-700" x-text="timerDisplay"></span>
                                    </div>
                                </div>

                                <!-- Phase indicators -->
                                <div class="flex justify-center gap-3 mb-6">
                                    <template x-for="(p, i) in phases" :key="i">
                                        <span class="text-xs px-3 py-1 rounded-full font-medium transition-all"
                                              :class="currentPhase === i ? 'text-white shadow-sm' : 'text-gray-400 bg-gray-50'"
                                              :style="currentPhase === i ? 'background-color: ' + phaseColors[i] + '; color: white;' : ''"
                                              x-text="p.label">
                                        </span>
                                    </template>
                                </div>

                                <!-- Cycle counter -->
                                <p class="text-sm text-gray-400 mb-4" x-show="isRunning">
                                    Cycle <span x-text="currentCycle"></span>/<span x-text="totalCycles"></span>
                                </p>

                                <!-- Controls -->
                                <div class="flex items-center justify-center gap-4">
                                    <button @click="toggleTimer"
                                            class="px-8 py-3 rounded-xl font-bold text-white transition-all shadow-lg hover:shadow-xl hover:scale-105"
                                            :style="{ backgroundColor: isRunning ? '#ef4444' : '{{ $exercise->color }}' }"
                                            x-text="isRunning ? '⏹ Arrêter' : '▶ Commencer'">
                                    </button>
                                    <button @click="resetTimer"
                                            class="px-4 py-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition text-sm"
                                            x-show="isRunning || currentCycle > 0">
                                        ↺ Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Duration selector (before starting) -->
                            <div class="flex justify-center gap-2" x-show="!isRunning && currentCycle === 0">
                                <template x-for="min in durations" :key="min">
                                    <button @click="setDuration(min)"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition border"
                                            :class="selectedMinutes === min
                                                ? 'bg-indigo-600 text-white border-indigo-600'
                                                : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            x-text="min + ' min'">
                                    </button>
                                </template>
                            </div>
                        @else
                            <!-- Meditation Timer -->
                            <div class="text-center mb-8">
                                <div class="relative mx-auto mb-6"
                                     style="width: 220px; height: 220px;">
                                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                                    <div class="absolute inset-0 rounded-full border-4 border-transparent"
                                         :style="{
                                             borderTopColor: '{{ $exercise->color }}',
                                             transform: 'rotate(' + meditationRotation + 'deg)',
                                             transition: 'transform 1s linear',
                                         }">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <span class="text-5xl block mb-2">🧘</span>
                                            <p class="text-3xl font-bold font-mono text-gray-700" x-text="timerDisplay"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meditation guidance text -->
                                <div class="max-w-md mx-auto min-h-[4rem] flex items-center justify-center">
                                    <p class="text-gray-600 text-sm leading-relaxed italic transition-opacity duration-500"
                                       x-show="isRunning"
                                       x-text="guidanceText"
                                       :style="{ opacity: guidanceOpacity }">
                                    </p>
                                </div>

                                <!-- Controls -->
                                <div class="flex items-center justify-center gap-4 mt-4">
                                    <button @click="toggleTimer"
                                            class="px-8 py-3 rounded-xl font-bold text-white transition-all shadow-lg hover:shadow-xl hover:scale-105"
                                            :style="{ backgroundColor: isRunning ? '#ef4444' : '{{ $exercise->color }}' }"
                                            x-text="isRunning ? '⏹ Arrêter' : '▶ Commencer'">
                                    </button>
                                    <button @click="resetTimer"
                                            class="px-4 py-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition text-sm"
                                            x-show="isRunning || totalSeconds > 0">
                                        ↺ Reset
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-3" x-show="!isRunning && totalSeconds === 0">
                                    Choisis une durée ci-dessous puis appuie sur Commencer.
                                </p>
                            </div>

                            <!-- Duration selector (before starting) -->
                            <div class="flex justify-center gap-2" x-show="!isRunning && totalSeconds === 0">
                                <template x-for="min in durations" :key="min">
                                    <button @click="setDuration(min)"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition border"
                                            :class="selectedMinutes === min
                                                ? 'bg-indigo-600 text-white border-indigo-600'
                                                : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            x-text="min + ' min'">
                                    </button>
                                </template>
                            </div>
                        @endif

                        <!-- Completion form (submitted when timer ends OR user stops) -->
                        <form id="complete-form" method="POST" action="{{ route('breathing.complete') }}" class="hidden">
                            @csrf
                            <input type="hidden" name="exercise_id" value="{{ $exercise->id }}">
                            <input type="hidden" name="duration_seconds" id="duration_seconds" value="">
                            <input type="hidden" name="completed" id="completed" value="1">
                            <input type="hidden" name="notes" id="session_notes" value="">
                        </form>
                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Description -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <span>📖</span> Description
                        </h3>
                        <p class="text-sm text-gray-600">{{ $exercise->description }}</p>
                    </div>

                    <!-- Benefits -->
                    @if ($exercise->benefits)
                        <div class="bg-green-50 border border-green-100 rounded-2xl p-5">
                            <h3 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
                                <span>✨</span> Bienfaits
                            </h3>
                            <p class="text-sm text-green-700">{{ $exercise->benefits }}</p>
                        </div>
                    @endif

                    <!-- Instructions -->
                    @if ($exercise->instructions)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                <span>📋</span> Instructions
                            </h3>
                            <div class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">
                                {{ $exercise->instructions }}
                            </div>
                        </div>
                    @endif

                    <!-- Tips -->
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                        <h3 class="font-semibold text-amber-800 mb-2 flex items-center gap-2">
                            <span>💡</span> Conseils
                        </h3>
                        <ul class="text-sm text-amber-700 space-y-1.5">
                            <li>• Trouve un endroit calme</li>
                            <li>• Porte des vêtements confortables</li>
                            <li>• Commence par 3-5 minutes si tu débutes</li>
                            <li>• Ne force pas ta respiration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Summary Modal -->
    <div x-show="showSummaryModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity"
         x-cloak>
        <div @click.away="cancelSummary"
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 md:p-8 transform transition-all"
             x-show="showSummaryModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="text-center mb-6">
                <span class="text-5xl block mb-3" x-text="summaryEmoji"></span>
                <h3 class="text-xl font-bold text-gray-800" x-text="summaryTitle"></h3>
                <p class="text-sm text-gray-500 mt-1" x-text="'{{ $exercise->icon }} ' + '{{ $exercise->name }}'"></p>
                <p class="text-2xl font-bold text-indigo-600 mt-2" x-text="summaryDuration"></p>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">📝 Note personnelle (optionnelle)</label>
                <textarea x-model="sessionNote"
                          rows="3"
                          class="w-full rounded-xl border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                          placeholder="Comment te sens-tu après cette séance ?"></textarea>
            </div>

            <div class="flex gap-3">
                <button @click="submitSession"
                        class="flex-1 px-5 py-3 rounded-xl font-bold text-white transition-all shadow-lg hover:shadow-xl"
                        :style="{ backgroundColor: '{{ $exercise->color }}' }">
                    ✅ Valider & enregistrer
                </button>
                <button @click="cancelSummary"
                        class="px-5 py-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition font-medium">
                    Passer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('breathingApp', (patternData, type) => ({
                // Timer state
                isRunning: false,
                totalSeconds: 0,
                selectedMinutes: 5,
                durations: @json($exercise->duration_options ?? [3, 5, 10]),
                timerInterval: null,

                // Breathing state
                currentPhase: 0,
                phases: [],
                currentCycle: 0,
                totalCycles: 0,
                phaseTimer: null,

                // Meditation state
                meditationRotation: 0,
                meditationInterval: null,

                // Completion / Summary
                completed: false,
                showSummaryModal: false,
                sessionNote: '',
                summaryDuration: '',
                summaryEmoji: '',
                summaryTitle: '',
                isSubmitting: false,

                // Audio
                audioCtx: null,

                // Guidance
                guidanceOpacity: 1,
                guidanceInterval: null,

                init() {
                    if (type === 'breathing' && patternData) {
                        this.buildPhases(patternData);
                    }
                },

                // ── AUDIO ──
                initAudio() {
                    try {
                        this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    } catch(e) {
                        // Audio not supported, silently ignore
                    }
                },

                playBeep(frequency = 520, duration = 0.15, startTime = 0) {
                    if (!this.audioCtx) return;
                    try {
                        const osc = this.audioCtx.createOscillator();
                        const gain = this.audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = frequency;
                        gain.gain.value = 0.3;
                        gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + duration + startTime);
                        osc.connect(gain);
                        gain.connect(this.audioCtx.destination);
                        osc.start(this.audioCtx.currentTime + startTime);
                        osc.stop(this.audioCtx.currentTime + duration + startTime + 0.05);
                    } catch(e) { /* ignore */ }
                },

                playBell() {
                    if (!this.audioCtx) return;
                    try {
                        // Two-tone bell
                        this.playBeep(880, 0.4, 0);
                        this.playBeep(1108, 0.4, 0.15);
                        this.playBeep(1318, 0.6, 0.3);
                    } catch(e) { /* ignore */ }
                },

                // ── BREATHING ──
                buildPhases(pattern) {
                    this.phases = [];
                    if (pattern.inhale > 0) this.phases.push({ label: 'Inspire', seconds: pattern.inhale, type: 'inhale' });
                    if (pattern.hold1 > 0) this.phases.push({ label: 'Retiens', seconds: pattern.hold1, type: 'hold1' });
                    if (pattern.exhale > 0) this.phases.push({ label: 'Expire', seconds: pattern.exhale, type: 'exhale' });
                    if (pattern.hold2 > 0) this.phases.push({ label: 'Retiens', seconds: pattern.hold2, type: 'hold2' });
                },

                setDuration(minutes) {
                    this.selectedMinutes = minutes;
                },

                toggleTimer() {
                    if (this.isRunning) {
                        this.stopTimer();
                        this.showSessionSummary('⏹️', 'Séance interrompue');
                    } else {
                        this.startTimer();
                    }
                },

                startTimer() {
                    this.isRunning = true;
                    this.totalSeconds = this.selectedMinutes * 60;
                    this.completed = false;
                    this.initAudio();

                    if (type === 'meditation') {
                        this.startMeditation();
                    } else {
                        this.startBreathing();
                    }
                },

                startBreathing() {
                    if (this.phases.length === 0) return;
                    this.currentCycle = 0;
                    this.totalCycles = Math.ceil(this.totalSeconds / this.phases.reduce((sum, p) => sum + p.seconds, 0));
                    this.runBreathingCycle();

                    // Main countdown
                    this.timerInterval = setInterval(() => {
                        if (this.totalSeconds > 0) {
                            this.totalSeconds--;
                        }
                        if (this.totalSeconds <= 0) {
                            this.stopTimer();
                            this.playBell();
                            this.onComplete();
                        }
                    }, 1000);
                },

                runBreathingCycle() {
                    if (this.currentPhase >= this.phases.length) {
                        this.currentPhase = 0;
                        this.currentCycle++;
                    }
                    if (this.currentCycle >= this.totalCycles && this.currentPhase === 0) {
                        return;
                    }

                    const phase = this.phases[this.currentPhase];
                    let phaseTime = phase.seconds;

                    // Beep on phase transition
                    if (phase.type === 'inhale') {
                        this.playBeep(440, 0.1);
                    } else if (phase.type === 'exhale') {
                        this.playBeep(330, 0.1);
                    } else {
                        this.playBeep(520, 0.08);
                    }

                    this.phaseTimer = setInterval(() => {
                        phaseTime--;
                        if (phaseTime <= 0) {
                            clearInterval(this.phaseTimer);
                            this.currentPhase++;
                            this.runBreathingCycle();
                        }
                    }, 1000);
                },

                startMeditation() {
                    this.meditationInterval = setInterval(() => {
                        this.meditationRotation = (this.meditationRotation + 0.5) % 360;
                    }, 100);

                    this.timerInterval = setInterval(() => {
                        if (this.totalSeconds > 0) {
                            this.totalSeconds--;
                            // Bell at halfway
                            if (this.totalSeconds === Math.floor(this.selectedMinutes * 30)) {
                                this.playBeep(660, 0.2);
                            }
                        }
                        if (this.totalSeconds <= 0) {
                            this.stopTimer();
                            this.playBell();
                            this.onComplete();
                        }
                    }, 1000);
                },

                stopTimer() {
                    this.isRunning = false;
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    if (this.phaseTimer) clearInterval(this.phaseTimer);
                    if (this.meditationInterval) clearInterval(this.meditationInterval);
                    if (this.guidanceInterval) clearInterval(this.guidanceInterval);
                },

                resetTimer() {
                    this.stopTimer();
                    this.totalSeconds = 0;
                    this.currentPhase = 0;
                    this.currentCycle = 0;
                    this.meditationRotation = 0;
                    this.completed = false;
                    this.showSummaryModal = false;
                },

                onComplete() {
                    this.completed = true;
                    this.showSessionSummary('🧘', 'Séance terminée ! 🎉');
                },

                showSessionSummary(emoji, title) {
                    const mins = Math.floor((this.selectedMinutes * 60) / 60);
                    const secs = (this.selectedMinutes * 60) % 60;
                    this.summaryEmoji = emoji;
                    this.summaryTitle = title;
                    this.summaryDuration = mins > 0 ? mins + ' min' + (secs > 0 ? ' ' + secs + 's' : '') : this.selectedMinutes * 60 + 's';
                    this.showSummaryModal = true;
                },

                submitSession() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;
                    document.getElementById('duration_seconds').value = this.selectedMinutes * 60;
                    document.getElementById('completed').value = this.completed ? '1' : '0';
                    document.getElementById('session_notes').value = this.sessionNote;
                    document.getElementById('complete-form').submit();
                },

                cancelSummary() {
                    // Submit without notes
                    this.sessionNote = '';
                    this.submitSession();
                },

                // ── COMPUTED ──
                get timerDisplay() {
                    if (this.totalSeconds === 0 && !this.isRunning) return '--:--';
                    const m = Math.floor(this.totalSeconds / 60);
                    const s = this.totalSeconds % 60;
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },

                get circleSize() {
                    if (!this.isRunning) return 100;
                    const phase = this.phases[this.currentPhase];
                    if (!phase) return 100;
                    if (phase.type === 'inhale') return 160;
                    if (phase.type === 'exhale') return 60;
                    return phase.type === 'hold1' ? 160 : 60;
                },

                get circleColor() {
                    if (!this.isRunning) return '{{ $exercise->color }}';
                    const phase = this.phases[this.currentPhase];
                    if (!phase) return '{{ $exercise->color }}';
                    const colors = {
                        inhale: '#6366f1',
                        hold1: '#8b5cf6',
                        exhale: '#10b981',
                        hold2: '#f59e0b',
                    };
                    return colors[phase.type] || '{{ $exercise->color }}';
                },

                get circleOpacity() {
                    if (!this.isRunning) return 0.6;
                    return 0.9;
                },

                get circleScale() {
                    if (!this.isRunning) return 1;
                    const phase = this.phases[this.currentPhase];
                    if (!phase) return 1;
                    return phase.type === 'inhale' ? 1.4 : (phase.type === 'exhale' ? 0.7 : 1);
                },

                get phaseLabel() {
                    if (!this.isRunning) return 'Prêt';
                    const phase = this.phases[this.currentPhase];
                    if (!phase) return '';
                    const labels = { inhale: '⬆️', hold1: '⏸️', exhale: '⬇️', hold2: '⏸️' };
                    return labels[phase.type] || '';
                },

                get phaseColors() {
                    return ['#6366f1', '#8b5cf6', '#10b981', '#f59e0b'];
                },

                get stepDuration() {
                    const phase = this.phases[this.currentPhase];
                    return phase ? phase.seconds * 1000 : 1000;
                },

                // ── STRUCTURED MEDITATION GUIDANCE ──
                get guidanceText() {
                    if (!this.isRunning) return '';
                    const total = this.selectedMinutes * 60;
                    const elapsed = total - this.totalSeconds;
                    const pct = elapsed / total;

                    // Phased guidance: opening → body → breath → awareness → closing
                    if (pct < 0.10) {
                        return "Prends une grande inspiration... expire doucement. Laisse le stress de la journée s'évacuer au fil de l'expiration. Installe-toi confortablement et ferme les yeux.";
                    } else if (pct < 0.20) {
                        const hints = [
                            "Porte ton attention sur les points de contact entre ton corps et le sol.",
                            "Relâche consciemment les épaules et la mâchoire.",
                            "Sens la verticalité de ta colonne vertébrale.",
                            "Observe les sensations de ton corps sans chercher à les changer.",
                        ];
                        return hints[Math.floor(elapsed / 8) % hints.length];
                    } else if (pct < 0.40) {
                        const hints = [
                            "Laisse ta respiration couler naturellement. Observe-la sans forcer.",
                            "Sens l'air frais qui entre par tes narines... l'air plus chaud qui ressort.",
                            "Imagine ta respiration comme une vague qui va et vient.",
                            "À chaque inspiration, tu accueilles l'énergie. À chaque expiration, tu relâches les tensions.",
                        ];
                        return hints[Math.floor(elapsed / 10) % hints.length];
                    } else if (pct < 0.60) {
                        const hints = [
                            "Les pensées sont comme des nuages. Observe-les passer sans t'y accrocher.",
                            "Reviens à ta respiration — ton ancre dans l'instant présent.",
                            "Laisse chaque expiration emporter une pensée, comme une feuille dans le vent.",
                            "Dans ce moment présent, tout est parfait tel que c'est.",
                        ];
                        return hints[Math.floor(elapsed / 10) % hints.length];
                    } else if (pct < 0.80) {
                        const hints = [
                            "Étends ta conscience à tout ton corps. Sens l'unité de ton être.",
                            "Répète silencieusement : paix, calme, sérénité.",
                            "Imagine une lumière douce qui traverse tout ton corps, guérissant chaque cellule.",
                            "Dans ce silence intérieur, découvre l'espace calme entre tes pensées.",
                        ];
                        return hints[Math.floor(elapsed / 10) % hints.length];
                    } else if (pct < 0.95) {
                        const hints = [
                            "Commence doucement à préparer ta sortie de méditation.",
                            "Laisse les sons de l'environnement revenir naturellement.",
                            "Ancre-toi dans cette sensation de calme que tu as cultivée.",
                            "Remue doucement les doigts et les orteils.",
                        ];
                        return hints[Math.floor(elapsed / 8) % hints.length];
                    } else {
                        const hints = [
                            "Prends une dernière grande inspiration... lentement...",
                            "Quand tu es prêt, ouvre doucement les yeux.",
                            "Emporte ce calme avec toi dans le reste de ta journée ✨",
                        ];
                        return hints[Math.floor(elapsed / 5) % hints.length];
                    }
                },
            }));
        });
    </script>
    @endpush
</x-app-layout>
