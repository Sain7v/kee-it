<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between w-full gap-2">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <a href="{{ route('tasks.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base sm:text-xl font-bold text-gray-900 truncate">{{ $task->title }}</h1>
                <p class="text-xs sm:text-sm text-gray-500">
                    {{ ucfirst($task->category) }} ·
                    @php
                        $badgeBg = match($task->priority) {
                            'critica' => 'bg-red-100 text-red-700',
                            'alta'    => 'bg-orange-100 text-orange-700',
                            'media'   => 'bg-yellow-100 text-yellow-700',
                            default   => 'bg-green-100 text-green-700',
                        };
                    @endphp
                    <span class="inline-flex text-[11px] font-semibold px-1.5 py-0.5 rounded-full {{ $badgeBg }}">{{ ucfirst($task->priority) }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <a href="{{ route('tasks.edit', $task) }}"
               class="inline-flex items-center gap-1 px-2 sm:px-3 py-2 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span class="hidden sm:inline">Editar</span>
            </a>
            @if($task->status !== 'completada')
                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-2 sm:px-3 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="hidden sm:inline">Completar</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="grid lg:grid-cols-3 gap-4 lg:gap-6">

        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-5">

            {{-- Description --}}
            @if($task->description)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-2">Descripción</h2>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line break-words">{{ $task->description }}</p>
                </div>
            @endif

            {{-- AI Suggestion --}}
            <div class="bg-white rounded-xl border border-brand-100 shadow-sm p-4 sm:p-5"
                 id="ai-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-brand-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.2 3.2 0 01-2.19.953H9.68a3.2 3.2 0 01-2.19-.953l-.347-.347z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-900">Sugerencia de IA</h2>
                    </div>
                    <button id="btn-refresh-ai"
                            class="text-xs text-brand-600 hover:underline font-medium whitespace-nowrap">Actualizar</button>
                </div>

                <div id="ai-text" class="mt-3 text-sm text-gray-600 leading-relaxed break-words">
                    {{ $aiSuggestion ?? 'Sin sugerencia disponible. Haz clic en "Actualizar" para generar una.' }}
                </div>
            </div>

            {{-- Subtasks --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">
                    Subtareas
                    <span id="subtask-counter" class="text-gray-400 font-normal">
                        @if($task->subtasks->count())
                            ({{ $task->subtasks->where('is_completed', true)->count() }}/{{ $task->subtasks->count() }})
                        @endif
                    </span>
                </h2>

                <ul class="space-y-1 mb-4" id="subtask-list">
                    @foreach($task->subtasks->sortBy('order') as $sub)
                        <li class="flex items-center gap-2 sm:gap-3 group" data-subtask-id="{{ $sub->id }}">
                            <button type="button"
                                    onclick="toggleSubtask({{ $task->id }}, {{ $sub->id }}, this)"
                                    data-completed="{{ $sub->is_completed ? 'true' : 'false' }}"
                                    class="subtask-toggle w-5 h-5 rounded border-2 shrink-0 flex items-center justify-center transition-colors
                                           {{ $sub->is_completed ? 'bg-green-500 border-green-500 hover:bg-green-600' : 'border-gray-300 hover:border-green-400' }}">
                                @if($sub->is_completed)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>

                            <span class="flex-1 text-sm subtask-label {{ $sub->is_completed ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                {{ $sub->title }}
                            </span>

                            <button type="button"
                                    onclick="deleteSubtask({{ $task->id }}, {{ $sub->id }}, this)"
                                    class="p-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 text-gray-300 hover:text-red-500 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </li>
                    @endforeach
                </ul>

                @if($task->subtasks->isEmpty())
                    <p class="text-xs text-gray-400 mb-4" id="subtask-empty">Sin subtareas aún.</p>
                @endif

                {{-- Add subtask --}}
                @if($task->status !== 'completada')
                    <div class="flex gap-2">
                        <input type="text" id="new-subtask-title" data-task-id="{{ $task->id }}"
                               placeholder="Nueva subtarea…"
                               class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-300">
                        <button type="button" onclick="addSubtask({{ $task->id }})"
                                class="px-3 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors whitespace-nowrap">
                            Añadir
                        </button>
                    </div>
                @endif
            </div>

            {{-- Activity log --}}
            @if($task->logs->count())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Actividad</h2>
                    <ul class="space-y-3">
                        @foreach($task->logs as $log)
                            <li class="flex gap-3">
                                <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-brand-400 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-700">{{ $log->action }}</p>
                                    @if($log->note)
                                        <p class="text-xs text-gray-400 mt-0.5 break-words">{{ $log->note }}</p>
                                    @endif
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $log->logged_at->locale('es')->diffForHumans() }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Detalles</h2>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            @php
                                $statusBg = match($task->status) {
                                    'completada'  => 'bg-green-100 text-green-700',
                                    'en_progreso' => 'bg-blue-100 text-blue-700',
                                    'cancelada'   => 'bg-gray-100 text-gray-500',
                                    default       => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusBg }}">
                                {{ str_replace('_', ' ', ucfirst($task->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($task->due_date)
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 shrink-0">Fecha límite</dt>
                            <dd class="font-medium text-right {{ $task->due_date->isPast() && $task->status !== 'completada' ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $task->due_date->locale('es')->isoFormat('ddd D MMM, HH:mm') }}
                            </dd>
                        </div>
                    @endif
                    @if($task->estimated_minutes)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Estimado</dt>
                            <dd class="font-medium text-gray-800">{{ $task->estimated_minutes }} min</dd>
                        </div>
                    @endif
                    @if($task->actual_minutes)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tiempo real</dt>
                            <dd class="font-medium text-gray-800">{{ $task->actual_minutes }} min</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Procrastinación</dt>
                        <dd class="font-medium {{ $task->procrastination_score > 10 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $task->procrastination_score }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Priority breakdown --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Desglose de prioridad</h2>
                <div class="space-y-3">

                    @foreach([
                        ['Urgencia', $priorityBreakdown['urgency'], 100, 'bg-red-400'],
                        ['Peso',     $priorityBreakdown['weight'],  30,  'bg-orange-400'],
                        ['Procrastinación', $priorityBreakdown['procrastination'], 50, 'bg-yellow-400'],
                    ] as [$label, $value, $max, $color])
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500">{{ $label }}</span>
                                <span class="font-semibold text-gray-700">{{ $value }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full"
                                     style="width: {{ min(100, round(($value / $max) * 100)) }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Total</span>
                        <span class="text-xl font-black text-brand-600">{{ $priorityBreakdown['total'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Timer --}}
            @if($task->status !== 'completada')
                <div x-data="taskTimer({{ $task->id }}, '{{ route('tasks.start-timer', $task) }}', '{{ route('tasks.stop-timer', $task) }}', '{{ csrf_token() }}')"
                     class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Cronómetro</h2>

                    <div class="text-3xl font-mono font-bold text-center text-gray-800 mb-4" x-text="display()"></div>

                    <div class="flex gap-2">
                        <button @click="start()" x-show="!running"
                                class="flex-1 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                            Iniciar
                        </button>
                        <button @click="stop()" x-show="running"
                                class="flex-1 py-2.5 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 transition-colors">
                            Detener
                        </button>
                    </div>

                    @if($task->actual_minutes)
                        <p class="text-xs text-gray-400 text-center mt-2">
                            Total acumulado: {{ $task->actual_minutes }} min
                        </p>
                    @endif
                </div>
            @endif

            {{-- Danger zone --}}
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-red-600 mb-2">Zona peligrosa</h2>
                @if($task->status !== 'completada' && $task->due_date)
                    <form method="POST" action="{{ route('tasks.postpone', $task) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full text-sm font-medium text-yellow-700 border border-yellow-200 bg-yellow-50 hover:bg-yellow-100 py-2.5 rounded-lg transition-colors">
                            Posponer 1 día (+{{ config('services.kee_it.procrastination_penalty', 5) }} proc.)
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                      onsubmit="return confirm('¿Eliminar esta tarea? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full text-sm font-medium text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 py-2.5 rounded-lg transition-colors">
                        Eliminar tarea
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Subtask AJAX ──────────────────────────────────────────────────────────────
const CSRF_TOKEN = '{{ csrf_token() }}';

function subtaskCounterUpdate() {
    const list = document.getElementById('subtask-list');
    const total = list.querySelectorAll('li[data-subtask-id]').length;
    const done  = list.querySelectorAll('[data-completed="true"]').length;
    document.getElementById('subtask-counter').textContent = total ? `(${done}/${total})` : '';
}

async function toggleSubtask(taskId, subtaskId, btn) {
    const li    = btn.closest('li');
    const label = li.querySelector('.subtask-label');
    const nowDone = btn.dataset.completed !== 'true';

    const res = await fetch(`/tasks/${taskId}/subtasks/${subtaskId}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
    });
    if (!res.ok) return;

    btn.dataset.completed = nowDone ? 'true' : 'false';

    if (nowDone) {
        btn.classList.remove('border-gray-300', 'hover:border-green-400');
        btn.classList.add('bg-green-500', 'border-green-500', 'hover:bg-green-600');
        btn.innerHTML = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
        label.classList.add('line-through', 'text-gray-400');
        label.classList.remove('text-gray-700');
    } else {
        btn.classList.add('border-gray-300', 'hover:border-green-400');
        btn.classList.remove('bg-green-500', 'border-green-500', 'hover:bg-green-600');
        btn.innerHTML = '';
        label.classList.remove('line-through', 'text-gray-400');
        label.classList.add('text-gray-700');
    }

    subtaskCounterUpdate();
}

async function deleteSubtask(taskId, subtaskId, btn) {
    if (!confirm('¿Eliminar esta subtarea?')) return;

    const res = await fetch(`/tasks/${taskId}/subtasks/${subtaskId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
    });
    if (!res.ok) return;

    btn.closest('li').remove();
    subtaskCounterUpdate();

    const list = document.getElementById('subtask-list');
    if (!list.querySelector('li[data-subtask-id]')) {
        const empty = document.getElementById('subtask-empty');
        if (empty) empty.style.display = '';
    }
}

async function addSubtask(taskId) {
    const input = document.getElementById('new-subtask-title');
    const title = input.value.trim();
    if (!title) return;

    const res = await fetch(`/tasks/${taskId}/subtasks`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title }),
    });
    if (!res.ok) return;

    const data = await res.json();
    input.value = '';

    const empty = document.getElementById('subtask-empty');
    if (empty) empty.style.display = 'none';

    const li = document.createElement('li');
    li.className = 'flex items-center gap-2 sm:gap-3 group';
    li.dataset.subtaskId = data.id;
    li.innerHTML = `
        <button type="button"
                onclick="toggleSubtask(${taskId}, ${data.id}, this)"
                data-completed="false"
                class="subtask-toggle w-5 h-5 rounded border-2 shrink-0 flex items-center justify-center transition-colors border-gray-300 hover:border-green-400">
        </button>
        <span class="flex-1 text-sm subtask-label text-gray-700">${data.title.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</span>
        <button type="button"
                onclick="deleteSubtask(${taskId}, ${data.id}, this)"
                class="p-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 text-gray-300 hover:text-red-500 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    document.getElementById('subtask-list').appendChild(li);
    subtaskCounterUpdate();
}

// Allow Enter key in the new-subtask input
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('new-subtask-title');
    if (input) {
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSubtask(parseInt(input.dataset.taskId));
            }
        });
    }
});

// ── Timer ─────────────────────────────────────────────────────────────────────
function taskTimer(taskId, startUrl, stopUrl, token) {
    return {
        running: false,
        seconds: 0,
        startedAt: null,
        interval: null,

        start() {
            this.running = true;
            this.startedAt = Date.now();
            fetch(startUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            });
            this.interval = setInterval(() => this.seconds++, 1000);
        },

        stop() {
            clearInterval(this.interval);
            this.running = false;
            const minutes = Math.round(this.seconds / 60);
            this.seconds = 0;
            fetch(stopUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ minutes }),
            }).then(r => r.json()).then(d => {
                if (d.actual_minutes !== undefined) {
                    document.querySelector('[x-text="display()"]')?.closest('[x-data]')
                        .__x?.$data && null;
                }
            });
        },

        display() {
            const h = Math.floor(this.seconds / 3600);
            const m = Math.floor((this.seconds % 3600) / 60);
            const s = this.seconds % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        }
    };
}

document.getElementById('btn-refresh-ai')?.addEventListener('click', async function () {
    this.textContent = 'Cargando…';
    this.disabled = true;
    const res = await fetch('{{ route('tasks.ai-suggestion', $task) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    document.getElementById('ai-text').textContent = data.suggestion ?? 'Sin respuesta.';
    this.textContent = 'Actualizar';
    this.disabled = false;
});
</script>
@endpush

</x-app-layout>
