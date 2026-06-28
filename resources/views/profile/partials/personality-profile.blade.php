<section>
    <header>
        <div class="flex items-center gap-3">
            <span class="text-3xl">🧠</span>
            <div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Profil de personnalité') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Ton profil Big Five, basé sur le questionnaire d\'onboarding.') }}
                </p>
            </div>
        </div>
    </header>

    <div class="mt-6 space-y-4">
        @php
            $traits = [
                'openness' => ['label' => 'Ouverture', 'icon' => '🎨'],
                'conscientiousness' => ['label' => 'Conscienciosité', 'icon' => '📋'],
                'extraversion' => ['label' => 'Extraversion', 'icon' => '🎉'],
                'agreeableness' => ['label' => 'Agréabilité', 'icon' => '🤝'],
                'neuroticism' => ['label' => 'Névrosisme', 'icon' => '🌊'],
            ];
        @endphp

        @foreach($traits as $key => $info)
        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-gray-700">{{ $info['icon'] }} {{ $info['label'] }}</span>
                <span class="text-sm {{ $personality->$key >= 60 ? 'text-green-600 font-medium' : ($personality->$key <= 35 ? 'text-orange-500' : 'text-gray-600') }}">
                    {{ $personality->$key }}/100
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all {{ $personality->$key >= 60 ? 'bg-green-400' : ($personality->$key <= 35 ? 'bg-orange-400' : 'bg-indigo-400') }}"
                     style="width: {{ $personality->$key }}%">
                </div>
            </div>
        </div>
        @endforeach

        <div class="pt-4">
            <p class="text-sm text-gray-500 italic">
                {{ $personality->getProfileDescription() }}
            </p>
        </div>

        <div class="pt-2">
            <a href="{{ route('personality.results') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 font-medium rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-all text-sm">
                Voir le détail avec le graphique →
            </a>
        </div>
    </div>
</section>
