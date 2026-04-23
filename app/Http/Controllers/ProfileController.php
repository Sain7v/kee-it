<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'work_start'      => ['nullable', 'date_format:H:i'],
            'work_end'        => ['nullable', 'date_format:H:i'],
            'work_days'       => ['nullable', 'array'],
            'work_days.*'     => ['in:lunes,martes,miércoles,jueves,viernes,sábado,domingo'],
            'priority_method' => ['nullable', 'in:auto,manual,ia'],
            'reminder_hours'  => ['nullable', 'integer', 'in:12,24,48'],
        ]);

        $user = $request->user();
        $preferences = $user->preferences ?? [];

        $user->preferences = array_merge($preferences, array_filter($validated, fn ($v) => $v !== null));
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Preferencias guardadas correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
