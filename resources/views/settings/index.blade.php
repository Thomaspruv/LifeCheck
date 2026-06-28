<x-app-layout>
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
                            <option value="light" {{ old('theme', $settings->theme) === 'light' ? 'selected' : '' }}>
                                {{ __('Clair') }}
                            </option>
                            <option value="dark" {{ old('theme', $settings->theme) === 'dark' ? 'selected' : '' }}>
                                {{ __('Sombre') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('theme')" class="mt-2" />
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
    </div>
</x-app-layout>
