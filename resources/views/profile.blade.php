<x-app-layout>

<x-slot name="header">
    <h1 class="text-xl font-bold text-gray-900">Perfil</h1>
</x-slot>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Personal info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Información personal</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                  @error('name') border-red-400 @enderror" required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                  @error('email') border-red-400 @enderror" required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <p class="text-xs text-yellow-600 bg-yellow-50 rounded-lg px-3 py-2">
                    Tu correo no está verificado.
                    <button form="" formaction="{{ route('verification.send') }}" formmethod="POST" class="underline font-medium">
                        Reenviar verificación
                    </button>
                </p>
            @endif

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>

    {{-- Password --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Cambiar contraseña</h2>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                <input type="password" id="current_password" name="current_password"
                       class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                              @error('current_password') border-red-400 @enderror" autocomplete="current-password">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                    <input type="password" id="password" name="password"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500
                                  @error('password') border-red-400 @enderror" autocomplete="new-password">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500"
                           autocomplete="new-password">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

    {{-- Preferences --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Preferencias</h2>

        <form method="POST" action="{{ route('profile.preferences') }}" class="space-y-5">
            @csrf @method('PATCH')

            {{-- Work hours --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Horario de trabajo</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="work_start" class="block text-xs text-gray-500 mb-1">Inicio</label>
                        <input type="time" id="work_start" name="work_start"
                               value="{{ old('work_start', $user->preference('work_start', '08:00')) }}"
                               class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                    </div>
                    <div>
                        <label for="work_end" class="block text-xs text-gray-500 mb-1">Fin</label>
                        <input type="time" id="work_end" name="work_end"
                               value="{{ old('work_end', $user->preference('work_end', '22:00')) }}"
                               class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>
            </div>

            {{-- Work days --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Días de trabajo</p>
                @php $workDays = old('work_days', $user->preference('work_days', ['lunes','martes','miércoles','jueves','viernes'])); @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach(['lunes','martes','miércoles','jueves','viernes','sábado','domingo'] as $day)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="work_days[]" value="{{ $day }}"
                                   {{ in_array($day, (array)$workDays) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-600 capitalize">{{ ucfirst($day) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Priority method + Reminder hours --}}
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="priority_method" class="block text-sm font-medium text-gray-700 mb-1">Método de prioridad</label>
                    <select id="priority_method" name="priority_method"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                        @foreach(['auto' => 'Automático', 'manual' => 'Manual', 'ia' => 'IA'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority_method', $user->preference('priority_method', 'auto')) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="reminder_hours" class="block text-sm font-medium text-gray-700 mb-1">Recordatorio anticipado</label>
                    <select id="reminder_hours" name="reminder_hours"
                            class="w-full rounded-lg border-gray-200 text-sm focus:ring-brand-500 focus:border-brand-500">
                        @foreach([12 => '12 horas antes', 24 => '24 horas antes', 48 => '48 horas antes'] as $val => $label)
                            <option value="{{ $val }}" {{ (int) old('reminder_hours', $user->preference('reminder_hours', 24)) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Guardar preferencias
                </button>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-red-600 mb-1">Eliminar cuenta</h2>
        <p class="text-xs text-gray-500 mb-4">Esta acción es permanente e irreversible. Se eliminarán todas tus tareas y datos.</p>

        <div x-data="{ open: false }">
            <button type="button" @click="open = true"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                Eliminar mi cuenta
            </button>

            {{-- Confirm modal --}}
            <div x-show="open" x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div @click.outside="open = false"
                     class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">¿Eliminar cuenta?</h3>
                    <p class="text-sm text-gray-500 mb-4">Ingresa tu contraseña para confirmar la eliminación permanente de tu cuenta.</p>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf @method('DELETE')

                        <div class="mb-4">
                            <label for="del_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                            <input type="password" id="del_password" name="password"
                                   class="w-full rounded-lg border-gray-200 text-sm focus:ring-red-500 focus:border-red-500"
                                   required>
                            @error('password', 'userDeletion')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="open = false"
                                    class="flex-1 py-2 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="flex-1 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

</x-app-layout>
