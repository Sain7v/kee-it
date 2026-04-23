<x-app-layout>

<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ route('tasks.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg sm:text-xl font-bold text-gray-900">Nueva tarea</h1>
    </div>
</x-slot>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">

        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4 sm:space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                       placeholder="Ej. Estudiar para examen de cálculo"
                       class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                              @error('title') border-red-400 @enderror"
                       autofocus required>
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Agrega detalles opcionales…"
                          class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500 resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Category + Due date --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                    <select id="category" name="category"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                   @error('category') border-red-400 @enderror">
                        <option value="">Selecciona…</option>
                        @foreach(['tarea','examen','proyecto','lectura','otro'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha límite <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="due_date" name="due_date"
                           value="{{ old('due_date') }}"
                           required
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500 @error('due_date') border-red-400 @enderror">
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Estimated minutes --}}
            <div>
                <label for="estimated_minutes" class="block text-sm font-medium text-gray-700 mb-1">Tiempo estimado (minutos)</label>
                <input type="number" id="estimated_minutes" name="estimated_minutes"
                       value="{{ old('estimated_minutes') }}" min="1" max="1440"
                       placeholder="Ej. 90"
                       class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                <p class="mt-1 text-xs text-gray-400">Influye en el cálculo de prioridad automática.</p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 sm:gap-3 pt-3 border-t border-gray-100">
                <a href="{{ route('tasks.index') }}"
                   class="text-center px-4 py-2.5 sm:py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 sm:py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Crear tarea
                </button>
            </div>

        </form>
    </div>
</div>

</x-app-layout>
