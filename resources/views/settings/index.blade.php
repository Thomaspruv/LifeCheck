<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Paramètres')]]">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paramètres') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                    @csrf

                    <!-- Reminder enabled -->
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="reminder_enabled" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                {{ old('reminder_enabled', $settings->reminder_enabled) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">{{ __('Activer le rappel quotidien') }}</span>
                        </label>
                        <input type="hidden" name="reminder_enabled" value="0">
                        <x-input-error :messages="$errors->get('reminder_enabled')" class="mt-2" />
                    </div>

                    <!-- Reminder time -->
                    <div>
                        <x-input-label for="checkin_reminder_time" :value="__('Heure du rappel')" />
                        <x-text-input id="checkin_reminder_time"
                            name="checkin_reminder_time"
                            type="time"
                            class="mt-1 block w-full"
                            :value="old('checkin_reminder_time', $settings->checkin_reminder_time)"
                            autocomplete="off" />
                        <x-input-error :messages="$errors->get('checkin_reminder_time')" class="mt-2" />
                    </div>

                    <!-- Week start -->
                    <div>
                        <x-input-label for="week_start" :value="__('Début de semaine')" />
                        <select id="week_start" name="week_start"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="monday" {{ old('week_start', $settings->week_start) === 'monday' ? 'selected' : '' }}>
                                {{ __('Lundi') }}
                            </option>
                            <option value="sunday" {{ old('week_start', $settings->week_start) === 'sunday' ? 'selected' : '' }}>
                                {{ __('Dimanche') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('week_start')" class="mt-2" />
                    </div>

                    <!-- Theme -->
                    <div>
                        <x-input-label for="theme" :value="__('Thème')" />
                        <select id="theme" name="theme"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="auto" {{ old('theme', $settings->theme) === 'auto' ? 'selected' : '' }}>
                                {{ __('Auto (suit le système)') }}
                            </option>
                            <option value="light" {{ old('theme', $settings->theme) === 'light' ? 'selected' : '' }}>
                                {{ __('Clair') }}
                            </option>
                            <option value="dark" {{ old('theme', $settings->theme) === 'dark' ? 'selected' : '' }}>
                                {{ __('Sombre') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('theme')" class="mt-2" />
                    </div>

                    <!-- Theme Color Palette -->
                    <div>
                        <x-input-label :value="__('Couleur du thème')" />
                        <div class="mt-2 grid grid-cols-6 gap-3">
                            @php
                                $palettes = [
                                    'indigo' => ['bg' => '#6366f1', 'ring' => 'ring-indigo-500', 'name' => __('Indigo')],
                                    'rose' => ['bg' => '#f43f5e', 'ring' => 'ring-rose-500', 'name' => __('Rose')],
                                    'emerald' => ['bg' => '#10b981', 'ring' => 'ring-emerald-500', 'name' => __('Émeraude')],
                                    'amber' => ['bg' => '#f59e0b', 'ring' => 'ring-amber-500', 'name' => __('Ambre')],
                                    'sky' => ['bg' => '#0ea5e9', 'ring' => 'ring-sky-500', 'name' => __('Ciel')],
                                    'violet' => ['bg' => '#8b5cf6', 'ring' => 'ring-violet-500', 'name' => __('Violet')],
                                ];
                            @endphp
                            @foreach ($palettes as $key => $palette)
                                <label class="flex flex-col items-center gap-1.5 cursor-pointer group">
                                    <input type="radio" name="theme_color" value="{{ $key }}"
                                        class="sr-only peer"
                                        {{ old('theme_color', $settings->theme_color ?? 'indigo') === $key ? 'checked' : '' }}>
                                    <span class="block w-8 h-8 rounded-full ring-2 ring-offset-2 transition-all duration-150
                                        peer-checked:ring-{{ $key === 'indigo' ? 'indigo' : $key }}-500
                                        peer-checked:scale-110 group-hover:scale-105"
                                        style="background-color: {{ $palette['bg'] }};">
                                    </span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 peer-checked:text-gray-900 dark:peer-checked:text-white font-medium">
                                        {{ $palette['name'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('theme_color')" class="mt-2" />
                    </div>

                    <!-- Timezone -->
                    <div>
                        <x-input-label for="timezone" :value="__('Fuseau horaire')" />
                        <select id="timezone" name="timezone"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (DateTimeZone::listIdentifiers() as $tz)
                                <option value="{{ $tz }}" {{ old('timezone', $settings->timezone) === $tz ? 'selected' : '' }}>
                                    {{ $tz }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                    </div>

                    <!-- Language -->
                    <div>
                        <x-input-label for="locale" :value="__('Langue')" />
                        <select id="locale" name="locale"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="fr" {{ old('locale', $settings->locale) === 'fr' ? 'selected' : '' }}>
                                🇫🇷 {{ __('Français') }}
                            </option>
                            <option value="en" {{ old('locale', $settings->locale) === 'en' ? 'selected' : '' }}>
                                🇬🇧 {{ __('English') }}
                            </option>
                            <option value="es" {{ old('locale', $settings->locale) === 'es' ? 'selected' : '' }}>
                                🇪🇸 {{ __('Español') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('locale')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Telegram Integration Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">🤖 {{ __('Liaison Telegram') }}</h3>
                <p class="text-sm text-gray-500 mb-4">
                    {{ __('Lie ton compte LifeCheck à Telegram pour faire ton check-in sans quitter la discussion.') }}
                    {{ __('Tape') }} <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">@lifecheck_bot</code>
                    {{ __('dans n\'importe quel chat Telegram.') }}
                </p>

                @if(Auth::user()->telegram_chat_id)
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-green-600 text-lg">✅</span>
                            <span class="font-medium text-green-800">{{ __('Compte Telegram lié') }}</span>
                        </div>
                        <p class="text-sm text-green-600 mb-3">
                            👉 {{ __('Tape') }} <code class="bg-green-100 px-1 py-0.5 rounded text-xs font-mono">@lifecheck_bot</code>
                            {{ __('dans n\'importe quel chat pour faire ton check-in rapidement.') }}
                        </p>
                        <form method="POST" action="{{ route('telegram.revoke') }}" onsubmit="return confirm('{{ __('Veux-tu vraiment révoquer la liaison Telegram ?') }}')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                🔌 {{ __('Révoquer la liaison') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-4">
                        @if(session('telegram_token'))
                            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <p class="text-sm font-medium text-indigo-800 mb-2">{{ __('Ton jeton de liaison :') }}</p>
                                <div class="flex items-center gap-2 mb-3" x-data="{ copied: false }">
                                    <code class="flex-1 p-2 bg-white border border-indigo-200 rounded-lg text-sm font-mono text-indigo-700 select-all">
                                        {{ session('telegram_token') }}
                                    </code>
                                    <button @click="
                                        navigator.clipboard.writeText('{{ session('telegram_token') }}');
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                    " class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition" x-text="copied ? '✅' : '📋'">
                                        📋
                                    </button>
                                </div>
                                <ol class="text-sm text-indigo-700 space-y-1 list-decimal list-inside">
                                    <li>{{ __('Ouvre Telegram et cherche') }} <strong>@lifecheck_bot</strong></li>
                                    <li>{{ __('Envoie') }} <code class="bg-indigo-100 px-1 py-0.5 rounded text-xs">/start {{ session('telegram_token') }}</code></li>
                                    <li>{{ __('Ton compte sera lié automatiquement !') }}</li>
                                </ol>
                            </div>
                        @else
                            <form method="POST" action="{{ route('telegram.generateToken') }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition">
                                    🔗 {{ __('Générer un jeton de liaison') }}
                                </button>
                            </form>
                            <p class="text-xs text-gray-400">
                                {{ __('Après avoir cliqué, un jeton unique sera généré. Envoie-le au bot Telegram pour lier ton compte.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Google Calendar Integration Section -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">📅 {{ __('Google Calendar') }}</h3>
            <p class="text-sm text-gray-500 mb-4">
                {{ __('Connecte ton compte Google pour voir ton humeur quotidienne directement dans ton agenda Google Calendar.') }}
                {{ __('Un événement sera créé chaque jour où tu fais ton check-in.') }}
            </p>

            @if($settings->isGoogleCalendarConnected())
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-green-600 text-lg">✅</span>
                        <span class="font-medium text-green-800">{{ __('Google Calendar connecté') }}</span>
                    </div>
                    <p class="text-sm text-green-600 mb-3">
                        {{ __('Tes humeurs sont synchronisées dans ton agenda Google Calendar.') }}
                    </p>
                    <form method="POST" action="{{ route('google-calendar.disconnect') }}"
                          onsubmit="return confirm('{{ __('Veux-tu vraiment déconnecter Google Calendar ? Les événements déjà créés resteront dans ton agenda.') }}')">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition">
                            🔌 {{ __('Déconnecter Google Calendar') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-4">
                    <a href="{{ route('google-calendar.redirect') }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition text-sm font-medium">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12.5V14.26H18.08C17.82 15.63 16.98 16.78 15.77 17.52V20.26H19.33C21.32 18.41 22.56 15.67 22.56 12.25Z" fill="#4285F4"/>
                            <path d="M12.5 23C15.38 23 17.79 22.04 19.33 20.26L15.77 17.52C14.92 18.09 13.86 18.43 12.5 18.43C9.72 18.43 7.37 16.56 6.53 14.07H2.87V16.89C4.4 19.92 8.09 23 12.5 23Z" fill="#34A853"/>
                            <path d="M6.53 14.07C6.33 13.48 6.21 12.85 6.21 12.2C6.21 11.55 6.33 10.92 6.53 10.33V7.51H2.87C2.16 8.92 1.76 10.52 1.76 12.2C1.76 13.88 2.16 15.48 2.87 16.89L6.53 14.07Z" fill="#FBBC05"/>
                            <path d="M12.5 5.57C13.97 5.57 15.27 6.09 16.28 7.03L19.42 3.89C17.77 2.35 15.38 1 12.5 1C8.09 1 4.4 4.08 2.87 7.51L6.53 10.33C7.37 7.84 9.72 5.57 12.5 5.57Z" fill="#EA4335"/>
                        </svg>
                        {{ __('Se connecter avec Google') }}
                    </a>
                    <p class="text-xs text-gray-400">
                        {{ __('Tu seras redirigé vers Google pour autoriser l\'accès à ton agenda.') }}
                        {{ __('LifeCheck pourra uniquement créer et modifier des événements dans un agenda dédié "LifeCheck — Humeur".') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
