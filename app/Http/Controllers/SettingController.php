<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Mostrar formulario de edición
     */
    public function editBienvenida()
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $mensaje = Setting::getValue(
            'mensaje_bienvenida',
            ''
        );

        return view('admin.mensaje', compact('mensaje'));
    }

    /**
     * Actualizar mensaje de bienvenida
     */
    public function updateBienvenida(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'mensaje' => 'required|string'
        ]);

        Setting::setValue(
            'mensaje_bienvenida',
            $validated['mensaje']
        );

        return redirect()
            ->route('admin.mensaje')
            ->with('success', 'Mensaje actualizado correctamente');
    }
}