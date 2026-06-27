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

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
