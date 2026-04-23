<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 sm:h-16">

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="flex items-center shrink-0">
                <svg viewBox="0 0 680 180" xmlns="http://www.w3.org/2000/svg" class="h-9 sm:h-12 w-auto">
                    <style>.nav-wordmark { font-family: sans-serif; font-size: 72px; font-weight: 500; letter-spacing: -2px; fill: #1a1a1a; }</style>
                    <g transform="translate(80, 50)">
                        <rect x="0" y="0" width="80" height="80" rx="20" fill="#2D6A4F"/>
                        <line x1="18" y1="40" x2="30" y2="56" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="30" y1="56" x2="62" y2="20" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="18" y1="24" x2="30" y2="40" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity="0.45"/>
                        <line x1="30" y1="40" x2="62" y2="4" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity="0.45"/>
                        <text x="100" y="68" class="nav-wordmark">keepit</text>
                    </g>
                </svg>
            </a>

            {{-- Desktop nav links --}}
            <div class="hidden sm:flex items-center gap-1">

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Inicio
                </a>

                <a href="{{ route('tasks.index') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tasks.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Tareas
                </a>

                <a href="{{ route('calendar') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('calendar') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Calendario
                </a>

                <a href="{{ route('stats') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('stats') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Estadísticas
                </a>

                <a href="{{ route('reminders.index') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('reminders.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Recordatorios
                </a>

            </div>

            {{-- Desktop right: bell + new task + user --}}
            <div class="hidden sm:flex items-center gap-3">

                {{-- Notification bell --}}
                @php $unread = Auth::user()->unreadNotifications()->count(); @endphp
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unread > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                    {{ $unread > 9 ? '9+' : $unread }}
                                </span>
                            @endif
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-xs font-semibold text-gray-700">Notificaciones</p>
                            @if($unread > 0)
                                <form method="POST" action="{{ route('reminders.mark-read') }}">
                                    @csrf
                                    <button class="text-[11px] text-brand-600 hover:underline">Marcar leídas</button>
                                </form>
                            @endif
                        </div>
                        @forelse(Auth::user()->notifications()->latest()->limit(8)->get() as $notif)
                            @php
                                $taskId = $notif->data['task_id'] ?? null;
                                $taskExists = $taskId && App\Models\Task::where('id', $taskId)->exists();
                                $notifUrl = $taskExists ? route('tasks.show', $taskId) : '#';
                            @endphp
                            <a href="{{ $notifUrl }}"
                               class="block px-4 py-2.5 border-b border-gray-50 hover:bg-gray-50 transition-colors {{ $notif->read_at ? 'opacity-60' : 'bg-brand-50' }}">
                                <p class="text-xs font-medium text-gray-800">{{ $notif->data['title'] ?? 'Recordatorio' }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $notif->created_at->locale('es')->diffForHumans() }}</p>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-xs text-gray-400">Sin notificaciones</div>
                        @endforelse
                        <div class="px-4 py-2">
                            <a href="{{ route('reminders.index') }}" class="text-xs text-brand-600 hover:underline">Ver recordatorios →</a>
                        </div>
                    </x-slot>
                </x-dropdown>

                <a href="{{ route('tasks.create') }}"
                   class="inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-medium px-3 py-2 rounded-lg hover:bg-brand-700 transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva tarea
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-2 sm:px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                            <div class="w-7 h-7 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-semibold text-xs shrink-0">
                                {{ Auth::user()->initials() }}
                            </div>
                            <span class="hidden lg:block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-500">Conectado como</p>
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Perfil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile right: bell + hamburger --}}
            <div class="flex items-center gap-1 sm:hidden">
                @php $unread = $unread ?? Auth::user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('reminders.index') }}" class="relative p-2.5 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unread > 0)
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                <button @click="open = !open" class="p-2.5 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Inicio
            </a>
            <a href="{{ route('tasks.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('tasks.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Tareas
            </a>
            <a href="{{ route('calendar') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('calendar') ? 'bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendario
            </a>
            <a href="{{ route('stats') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('stats') ? 'bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Estadísticas
            </a>
            <a href="{{ route('reminders.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('reminders.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Recordatorios
            </a>

            <div class="pt-1">
                <a href="{{ route('tasks.create') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium bg-brand-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva tarea
                </a>
            </div>
        </div>
        <div class="border-t border-gray-100 px-4 py-3 space-y-1">
            <p class="text-xs text-gray-500 px-3 py-1 font-medium">{{ Auth::user()->name }}</p>
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</nav>
