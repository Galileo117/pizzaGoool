<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Actualiza datos personales de la cuenta sin guardar una dirección predeterminada.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = $request->user();

            $user->email = $request->validated('email');
            $user->save();

            $user->cliente()->updateOrCreate(
                ['usuario_id' => $user->id],
                [
                    'nombres' => $request->validated('nombres'),
                    'apellidos' => $request->validated('apellidos'),
                    'telefono' => $request->validated('telefono'),
                ]
            );
        });

        return Redirect::route('cliente.perfil')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->pedidos()->exists() || $user->pedidosAsignados()->exists()) {
            return Redirect::route('profile.edit')
                ->with('error', 'No se puede eliminar una cuenta asociada a pedidos; el historial debe conservarse.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
