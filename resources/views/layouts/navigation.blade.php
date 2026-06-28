<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)"
     class="sticky top-0 z-40 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 transition-shadow duration-300"
     :class="scrolled && 'shadow-sm'">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left: Logo + Navigation --}}
            <div class="flex items-center gap-1">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-primary-600 dark:text-primary-400 font-bold text-lg">
                        <x-application-logo class="block h-8 w-auto" />
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex lg:items-center lg:ml-8 lg:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon='
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    '>{{ __('Dashboard') }}</x-nav-link>

                    <x-nav-link :href="route('checkin.create')" :active="request()->routeIs('checkin.*')" icon='
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    '>{{ __('Check-in') }}</x-nav-link>

                    <x-nav-link :href="route('progression.index')" :active="request()->routeIs('progression.*')" icon='
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    '>{{ __('Progression') }}</x-nav-link>

                    <x-nav-link :href="route('history.index')" :active="request()->routeIs('history.*')" icon='
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    '>{{ __('Historique') }}</x-nav-link>

                    <x-nav-link :href="route('trends')" :active="request()->routeIs('trends')" icon='
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    '>{{ __('Tendances') }}</x-nav-link>

                    <!-- More dropdown -->
                    <x-dropdown align="left" width="64" dropdownClasses="!mt-1">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                {{ __('Plus') }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="grid grid-cols-2 gap-1 p-2">
                                <a href="{{ route('goals.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('goals.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Objectifs') }}</span>
                                </a>
                                <a href="{{ route('challenges.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('challenges.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Défis') }}</span>
                                </a>
                                <a href="{{ route('breathing.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('breathing.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Respiration') }}</span>
                                </a>
                                <a href="{{ route('tags.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('tags.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Tags') }}</span>
                                </a>
                                <a href="{{ route('templates.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('templates.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span class="text-xs font-medium">{{ __('Templates') }}</span>
                                </a>
                                <a href="{{ route('journal.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('journal.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Journal') }}</span>
                                </a>
                                <a href="{{ route('timeline.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('timeline.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="text-xs font-medium">{{ __('Timeline') }}</span>
                                </a>
                                <a href="{{ route('widget') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('widget') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Widget') }}</span>
                                </a>
                                <a href="{{ route('personality.results') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ request()->routeIs('personality.*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    <span class="text-xs font-medium">{{ __('Personnalité') }}</span>
                                </a>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Right: Theme Toggle + Language + User Menu --}}
            <div class="flex items-center gap-2">
                {{-- Theme Toggle --}}
                <button onclick="window.themeManager.toggle()"
                        class="flex items-center justify-center w-9 h-9 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                        title="{{ __('Changer le thème') }}">
                    {{-- Sun icon (shown in dark mode) --}}
                    <svg class="hidden dark:block w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- Moon icon (shown in light mode) --}}
                    <svg class="block dark:hidden w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- Language Switcher --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1.5 px-2.5 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                        @switch(App::getLocale())
                            @case('fr') 🇫🇷 @break
                            @case('en') 🇬🇧 @break
                            @case('es') 🇪🇸 @break
                            @default 🌐
                        @endswitch
                    </button>
                    <div x-show="open" @click.away="open = false"
                         class="absolute right-0 mt-1 w-36 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-black/5 dark:ring-white/10 shadow-dropdown border border-gray-100 dark:border-gray-700 z-50"
                         style="display: none;">
                        <div class="py-1">
                            @foreach (['fr' => '🇫🇷 Français', 'en' => '🇬🇧 English', 'es' => '🇪🇸 Español'] as $locale => $label)
                                <form method="POST" action="{{ route('language.switch') }}">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $locale }}">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm {{ App::getLocale() === $locale ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                        {{ $label }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-2.5 py-1.5 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                            <span class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-300 flex items-center justify-center text-xs font-bold">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </span>
                            <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                            <svg class="hidden sm:block w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('Profile') }}
                            </div>
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('export.index')">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Export des données') }}
                            </div>
                        </x-dropdown-link>
                        <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Log Out') }}
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>

                {{-- Hamburger (mobile) --}}
                <button @click="open = !open" class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden border-t border-gray-200 dark:border-gray-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            '>{{ __('Dashboard') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('checkin.create')" :active="request()->routeIs('checkin.*')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            '>{{ __('Check-in') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('history.index')" :active="request()->routeIs('history.*')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            '>{{ __('Historique') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('trends')" :active="request()->routeIs('trends')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            '>{{ __('Tendances') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('progression.index')" :active="request()->routeIs('progression.*')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            '>{{ __('Progression') }}</x-responsive-nav-link>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 pb-3 px-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">{{ __('Plus') }}</p>
            <div class="grid grid-cols-2 gap-1">
                <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')" icon='🎯'>{{ __('Objectifs') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('challenges.index')" :active="request()->routeIs('challenges.*')" icon='💡'>{{ __('Défis') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('breathing.index')" :active="request()->routeIs('breathing.*')" icon='🧘'>{{ __('Respiration') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')" icon='🏷️'>{{ __('Tags') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('templates.index')" :active="request()->routeIs('templates.*')" icon='📋'>{{ __('Templates') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')" icon='📔'>{{ __('Journal') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('timeline.index')" :active="request()->routeIs('timeline.*')" icon='📜'>{{ __('Timeline') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('widget')" :active="request()->routeIs('widget')" icon='📱'>{{ __('Widget') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('personality.results')" :active="request()->routeIs('personality.*')" icon='🧠'>{{ __('Personnalité') }}</x-responsive-nav-link>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 pb-3 px-2">
            <div class="px-3 pb-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">{{ __('Langue') }}</div>
                <div class="flex gap-1.5">
                    @foreach (['fr' => '🇫🇷', 'en' => '🇬🇧', 'es' => '🇪🇸'] as $locale => $flag)
                        <form method="POST" action="{{ route('language.switch') }}">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $locale }}">
                            <button type="submit" class="px-3 py-1 text-sm rounded-lg {{ App::getLocale() === $locale ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                {{ $flag }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            <x-responsive-nav-link :href="route('profile.edit')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            '>{{ __('Profile') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('export.index')" icon='
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            '>{{ __('Export des données') }}</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="!text-red-600 dark:!text-red-400 !border-red-200 dark:!border-red-800">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        {{ __('Log Out') }}
                    </span>
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
