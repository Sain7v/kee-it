<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between w-full gap-3">
        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Recordatorios</h1>
            @if($unreadCount > 0)
                <p class="text-xs sm:text-sm text-brand-600 font-medium mt-0.5">{{ $unreadCount }} notificación(es) sin leer</p>
            @endif
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('reminders.mark-read') }}" class="shrink-0">
                @csrf
                <button type="submit"
                        class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors whitespace-nowrap">
                    Marcar leídas
                </button>
            </form>
        @endif
    </div>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="grid lg:grid-cols-3 gap-4 lg:gap-6">

        {{-- Create reminder --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Nuevo recordatorio</h2>

            <form method="POST" action="{{ route('reminders.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="task_id" class="block text-sm font-medium text-gray-700 mb-1">Tarea</label>
                    <select id="task_id" name="task_id"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                   @error('task_id') border-red-400 @enderror">
                        <option value="">Selecciona una tarea…</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                {{ Str::limit($task->title, 40) }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="remind_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora</label>
                    <input type="datetime-local" id="remind_at" name="remind_at"
                           value="{{ old('remind_at') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                  @error('remind_at') border-red-400 @enderror">
                    @error('remind_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Crear recordatorio
                </button>
            </form>
        </div>

        {{-- Pending + Sent --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-5">

            {{-- Pending --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">
                        Pendientes
                        @if($pending->count())
                            <span class="ml-1.5 bg-brand-100 text-brand-700 text-[11px] font-bold px-1.5 py-0.5 rounded-full">{{ $pending->count() }}</span>
                        @endif
                    </h2>
                </div>

                @if($pending->isEmpty())
                    <div class="px-5 py-10 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm">Sin recordatorios pendientes</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($pending as $reminder)
                            <li class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5">
                                <div class="shrink-0 w-8 h-8 sm:w-9 sm:h-9 bg-brand-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $reminder->task->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $reminder->remind_at->locale('es')->isoFormat('ddd D MMM, HH:mm') }}
                                        · {{ $reminder->remind_at->locale('es')->diffForHumans() }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" class="shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Sent (last 20) --}}
            @if($sent->count())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-700">Historial (últimos 20)</h2>
                    </div>
                    <ul class="divide-y divide-gray-50">
                        @foreach($sent as $reminder)
                            <li class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3 opacity-70">
                                <div class="shrink-0 w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-600 truncate">{{ $reminder->task->title }}</p>
                                    <p class="text-xs text-gray-400">
                                        Enviado {{ $reminder->sent_at->locale('es')->diffForHumans() }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</div>

</x-app-layout>
