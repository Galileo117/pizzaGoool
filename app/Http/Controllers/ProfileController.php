<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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
        // Usamos transacción para guardar en ambas tablas de forma segura
        DB::transaction(function () use ($request) {
            $user = $request->user();

            // 1. Actualizamos la tabla usuarios (solo email)
            $user->email = $request->validated('email');

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();

            // 2. Actualizamos la tabla clientes (perfil)
            $user->cliente()->updateOrCreate(
                ['usuario_id' => $user->id],
                [
                    'nombres' => $request->validated('nombres'),
                    'apellidos' => $request->validated('apellidos'),
                    'telefono' => $request->validated('telefono'),
                    'direccion_entrega' => $request->validated('direccion_entrega'),
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
