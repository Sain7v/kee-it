<x-app-layout>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-6">

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pendientes</p>
            <p class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $tasksPending }}</p>
            <p class="mt-1 text-xs text-gray-400">tareas activas</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Completadas hoy</p>
            <p class="mt-1 text-2xl sm:text-3xl font-bold text-green-600">{{ $tasksCompletedToday }}</p>
            <p class="mt-1 text-xs text-gray-400 truncate">{{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Críticas</p>
            <p class="mt-1 text-2xl sm:text-3xl font-bold {{ $tasksCritical > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $tasksCritical }}</p>
            <p class="mt-1 text-xs text-gray-400">requieren atención</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Racha actual</p>
            <p class="mt-1 text-2xl sm:text-3xl font-bold {{ $streak['at_risk'] ? 'text-orange-500' : 'text-brand-600' }}">
                {{ $streak['current'] }} 🔥
            </p>
            <p class="mt-1 text-xs text-gray-400">mejor: {{ $streak['best'] }} días</p>
        </div>

    </div>

    <div class="grid lg:grid-cols-3 gap-4 lg:gap-6">

        {{-- Urgent tasks --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Tareas urgentes</h2>
                <a href="{{ route('tasks.index') }}" class="text-xs text-brand-600 font-medium hover:underline">Ver todas</a>
            </div>

            @if($tasksUrgent->isEmpty())
                <div class="px-5 py-10 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">¡Todo bajo control!</p>
                    <p class="text-xs mt-1">No tienes tareas urgentes pendientes.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-50">
                    @foreach($tasksUrgent as $task)
                        @php
                            $isOverdue = $task->due_date?->isPast();
                            $borderColor = match($task->priority) {
                                'critica' => 'border-red-500',
                                'alta'    => 'border-orange-400',
                                'media'   => 'border-yellow-400',
                                default   => 'border-green-400',
                            };
                            $badgeBg = match($task->priority) {
                                'critica' => 'bg-red-100 text-red-700',
                                'alta'    => 'bg-orange-100 text-orange-700',
                                'media'   => 'bg-yellow-100 text-yellow-700',
                                default   => 'bg-green-100 text-green-700',
                            };
                        @endphp
                        <li class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3 sm:py-3.5 border-l-4 {{ $borderColor }} hover:bg-gray-50 transition-colors">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-5 h-5 rounded border-2 border-gray-300 hover:border-brand-400 bg-white transition-colors flex items-center justify-center">
                                </button>
                            </form>

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('tasks.show', $task) }}"
                                   class="text-sm font-medium text-gray-800 hover:text-brand-600 truncate block">
                                    {{ $task->title }}
                                </a>
                                <div class="flex items-center gap-1.5 sm:gap-2 mt-0.5 flex-wrap">
                                    <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-full {{ $badgeBg }}">{{ ucfirst($task->priority) }}</span>
                                    <span class="text-xs text-gray-400">{{ ucfirst($task->category) }}</span>
                                    @if($task->subtasks->count())
                                        <span class="hidden sm:inline text-xs text-gray-400">· {{ $task->subtasks->count() }} subtareas</span>
                                    @endif
                                </div>
                            </div>

                            <div class="shrink-0 text-right hidden sm:block">
                                <p class="text-xs font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                    @if($task->due_date)
                                        {{ $task->due_date->locale('es')->isoFormat('D MMM') }}
                                    @else
                                        Sin fecha
                                    @endif
                                </p>
                                <p class="text-[11px] text-gray-400">{{ $isOverdue ? 'vencida' : ($task->due_date?->locale('es')->diffForHumans() ?? '') }}</p>
                            </div>

                            <div class="shrink-0 w-8 sm:w-10 text-center">
                                <span class="text-base sm:text-lg font-extrabold {{ $isOverdue ? 'text-red-600' : 'text-gray-700' }}">{{ $task->priority_total }}</span>
                                <p class="text-[9px] text-gray-400 uppercase tracking-wide">score</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="px-4 sm:px-5 py-3 border-t border-gray-100">
                    <a href="{{ route('tasks.create') }}"
                       class="inline-flex items-center gap-1.5 text-sm text-brand-600 font-medium hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar tarea
                    </a>
                </div>
            @endif
        </div>

        {{-- Right column: streak + week progress --}}
        <div class="space-y-4">

            {{-- Streak card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">Racha</h3>
                    @if($streak['at_risk'])
                        <span class="text-[11px] font-semibold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">⚠ En riesgo</span>
                    @endif
                </div>

                <div class="flex items-end gap-3 mb-4">
                    <span class="text-3xl sm:text-4xl font-black {{ $streak['at_risk'] ? 'text-orange-500' : 'text-brand-600' }}">{{ $streak['current'] }}</span>
                    <span class="text-sm text-gray-500 mb-1.5">días seguidos 🔥</span>
                </div>

                @if($streak['at_risk'])
                    <p class="text-xs text-orange-600 bg-orange-50 rounded-lg px-3 py-2 mb-3">
                        Completa al menos una tarea hoy para mantener tu racha.
                    </p>
                @endif

                {{-- 7-day calendar dots --}}
                <div class="grid grid-cols-7 gap-1">
                    @foreach($streakCalendar as $day)
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($day['date'])->locale('es')->isoFormat('dd')[0] }}</span>
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center
                                        {{ $day['is_productive'] ? 'bg-brand-500' : 'bg-gray-100' }}"
                                 title="{{ $day['tasks_completed'] }} tareas">
                                @if($day['is_productive'])
                                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Week progress --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-semibold text-gray-900">Progreso semanal</h3>
                    <span class="text-sm font-bold text-brand-600">{{ $weekProgress['percent'] }}%</span>
                </div>
                <p class="text-xs text-gray-400 mb-3">{{ $weekProgress['label'] }}</p>

                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-brand-500 h-2.5 rounded-full transition-all duration-500"
                         style="width: {{ $weekProgress['percent'] }}%"></div>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    {{ $weekProgress['completed'] }} de {{ $weekProgress['total'] }} tareas esta semana
                </p>
            </div>

            {{-- Quick links --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Acceso rápido</h3>
                <div class="space-y-1">
                    <a href="{{ route('tasks.create') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-brand-600 py-2 rounded-lg hover:bg-brand-50 px-2 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva tarea
                    </a>
                    <a href="{{ route('calendar') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-brand-600 py-2 rounded-lg hover:bg-brand-50 px-2 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Ver calendario
                    </a>
                    <a href="{{ route('stats') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-brand-600 py-2 rounded-lg hover:bg-brand-50 px-2 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Mis estadísticas
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
</x-app-layout>
