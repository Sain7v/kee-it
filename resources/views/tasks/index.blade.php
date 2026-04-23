<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between w-full gap-3">
        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Mis tareas</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $tasksPending }} pendientes · {{ $tasksToday }} completadas hoy</p>
        </div>
        <a href="{{ route('tasks.create') }}"
           class="shrink-0 inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-medium px-3 sm:px-4 py-2 rounded-lg hover:bg-brand-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden xs:inline">Nueva tarea</span>
            <span class="xs:hidden">Nueva</span>
        </a>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Filters --}}
    <form method="GET" action="{{ route('tasks.index') }}"
          class="bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3 mb-4 sm:mb-5
                 flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2 sm:gap-3">

        <select name="status" onchange="this.form.submit()"
                class="w-full sm:w-auto text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 py-2">
            <option value="">Todos los estados</option>
            <option value="pendiente"   {{ request('status') === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
            <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En progreso</option>
        </select>

        <select name="category" onchange="this.form.submit()"
                class="w-full sm:w-auto text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 py-2">
            <option value="">Todas las categorías</option>
            @foreach(['tarea','examen','proyecto','lectura','otro'] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>

        <select name="sort" onchange="this.form.submit()"
                class="w-full sm:w-auto text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 py-2">
            <option value="priority"   {{ request('sort', 'priority') === 'priority'   ? 'selected' : '' }}>Prioridad</option>
            <option value="due_date"   {{ request('sort') === 'due_date'   ? 'selected' : '' }}>Fecha límite</option>
            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Más recientes</option>
        </select>

        @if(request()->hasAny(['status','category','sort']))
            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-400 hover:text-red-500 transition-colors py-1">
                Limpiar filtros
            </a>
        @endif

    </form>

    {{-- Task list --}}
    @if($tasks->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-14 sm:py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-medium text-gray-500">Sin tareas activas</p>
            <p class="text-sm mt-1">¡Estás al día o ajusta los filtros!</p>
            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block text-sm text-brand-600 font-medium hover:underline">+ Crear una tarea</a>
        </div>
    @else
        <div class="space-y-2">
            @foreach($tasks as $task)
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
                    $statusBg = match($task->status) {
                        'en_progreso' => 'bg-blue-100 text-blue-700',
                        'completada'  => 'bg-green-100 text-green-700',
                        default       => 'bg-gray-100 text-gray-600',
                    };
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 border-l-4 {{ $borderColor }} shadow-sm
                            flex items-center gap-3 px-3 sm:px-4 py-3 sm:py-3.5 hover:shadow-md transition-shadow group">

                    {{-- Toggle --}}
                    <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="shrink-0">
                        @csrf @method('PATCH')
                        <button type="submit"
                                title="Cambiar estado"
                                class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                       {{ $task->status === 'completada'
                                           ? 'bg-green-500 border-green-500'
                                           : 'border-gray-300 hover:border-brand-400 bg-white' }}">
                            @if($task->status === 'completada')
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>
                    </form>

                    {{-- Score (hidden on mobile) --}}
                    <div class="hidden sm:block shrink-0 w-11 text-center">
                        <div class="text-xl font-extrabold leading-none {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $task->priority_total }}
                        </div>
                        <div class="text-[9px] text-gray-400 uppercase tracking-wide">score</div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('tasks.show', $task) }}"
                           class="font-semibold text-sm text-gray-800 hover:text-brand-600 truncate block transition-colors">
                            {{ $task->title }}
                        </a>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                            <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-full {{ $badgeBg }}">{{ ucfirst($task->priority) }}</span>
                            <span class="text-[11px] font-medium px-1.5 py-0.5 rounded-full {{ $statusBg }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                            <span class="hidden sm:inline text-xs text-gray-400">{{ ucfirst($task->category) }}</span>
                            @if($task->estimated_minutes)
                                <span class="hidden sm:inline text-xs text-gray-400">· {{ $task->estimated_minutes }} min</span>
                            @endif
                            {{-- Due date on mobile --}}
                            @if($task->due_date)
                                <span class="sm:hidden text-xs font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $task->due_date->locale('es')->isoFormat('D MMM') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Due date (desktop) --}}
                    <div class="shrink-0 text-right hidden sm:block">
                        @if($task->due_date)
                            <p class="text-xs font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                {{ $task->due_date->locale('es')->isoFormat('ddd D MMM') }}
                            </p>
                            <p class="text-[11px] text-gray-400">
                                {{ $isOverdue ? 'Vencida' : $task->due_date->locale('es')->diffForHumans() }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400">Sin fecha</p>
                        @endif
                    </div>

                    {{-- Actions: always visible on mobile, hover on desktop --}}
                    <div class="shrink-0 flex items-center gap-0.5 sm:gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                        @if($task->status !== 'completada')
                            <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Completar"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('tasks.postpone', $task) }}" class="hidden sm:block">
                                @csrf @method('PATCH')
                                <button type="submit" title="Posponer 1 día"
                                        class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('tasks.edit', $task) }}" title="Editar"
                           class="p-2 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>

                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                              onsubmit="return confirm('¿Eliminar esta tarea?')" class="hidden sm:block">
                            @csrf @method('DELETE')
                            <button type="submit" title="Eliminar"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($tasks->hasPages())
            <div class="mt-4 sm:mt-5">
                {{ $tasks->links() }}
            </div>
        @endif
    @endif

</div>

</x-app-layout>
