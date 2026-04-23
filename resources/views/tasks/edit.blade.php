<x-app-layout>

<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ route('tasks.show', $task) }}" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Editar tarea</h1>
            <p class="text-sm text-gray-500 truncate">{{ $task->title }}</p>
        </div>
    </div>
</x-slot>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">

        <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-4 sm:space-y-5">
            @csrf @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $task->title) }}"
                       class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                              @error('title') border-red-400 @enderror"
                       required autofocus>
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500 resize-none">{{ old('description', $task->description) }}</textarea>
            </div>

            {{-- Category + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                    <select id="category" name="category"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                        @foreach(['tarea','examen','proyecto','lectura','otro'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $task->category) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status" name="status"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                        <option value="pendiente"   {{ old('status', $task->status) === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ old('status', $task->status) === 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                        <option value="completada"  {{ old('status', $task->status) === 'completada'  ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada"   {{ old('status', $task->status) === 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
            </div>

            {{-- Due date + Estimated --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha límite <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="due_date" name="due_date"
                           value="{{ old('due_date', $task->due_date?->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500 @error('due_date') border-red-400 @enderror">
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_minutes" class="block text-sm font-medium text-gray-700 mb-1">Tiempo estimado (min)</label>
                    <input type="number" id="estimated_minutes" name="estimated_minutes" min="1"
                           value="{{ old('estimated_minutes', $task->estimated_minutes) }}"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            {{-- Recurrence --}}
            <div x-data="{ recurring: {{ $task->is_recurring ? 'true' : 'false' }} }">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_recurring" value="1"
                           x-model="recurring"
                           {{ $task->is_recurring ? 'checked' : '' }}
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                    <span class="text-sm font-medium text-gray-700">Tarea recurrente</span>
                </label>

                <div x-show="recurring" class="mt-3">
                    <label for="recurrence_rule" class="block text-sm font-medium text-gray-700 mb-1">Regla de recurrencia</label>
                    <input type="text" id="recurrence_rule" name="recurrence_rule"
                           value="{{ old('recurrence_rule', $task->recurrence_rule) }}"
                           placeholder="Ej. FREQ=WEEKLY;BYDAY=MO,WE,FR"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            {{-- Subtasks (read-only display) --}}
            @if($task->subtasks->count())
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Subtareas</p>
                    <ul class="space-y-2">
                        @foreach($task->subtasks as $sub)
                            <li class="flex items-center gap-2 text-sm text-gray-600">
                                <div class="w-4 h-4 rounded border-2 border-gray-300 shrink-0
                                            {{ $sub->is_completed ? 'bg-green-500 border-green-500' : '' }}">
                                </div>
                                <span class="{{ $sub->is_completed ? 'line-through text-gray-400' : '' }}">{{ $sub->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 sm:gap-3 pt-3 border-t border-gray-100">
                <a href="{{ route('tasks.show', $task) }}"
                   class="text-center px-4 py-2.5 sm:py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 sm:py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Guardar cambios
                </button>
            </div>

        </form>

        {{-- Delete form (outside the edit form to avoid nesting) --}}
        <div class="mt-4 pt-4 border-t border-red-100">
            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                  onsubmit="return confirm('¿Eliminar esta tarea permanentemente?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                    Eliminar tarea
                </button>
            </form>
        </div>
    </div>
</div>

</x-app-layout>
