<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAdvertenciaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => ['nullable', 'exists:users,id'],
            'titulo'     => ['required', 'string', 'max:150'],
            'mensaje'    => ['required', 'string'],
            'nivel'      => ['required', Rule::in(['info','success','warning','danger'])],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date'],
            // ojo: boolean de Laravel es ok, pero normalizamos abajo igual
            'activa'     => ['nullable'],
        ]);

        // ✅ default TRUE si el campo no viene
        // ✅ y si viene, conviértelo de forma confiable (on/1/true/"1" etc.)
        $activa = $request->has('activa')
            ? (bool) $request->boolean('activa')
            : true;

        // (Opcional) si ends_at viene antes que starts_at, lo evitamos
        if (!empty($data['starts_at']) && !empty($data['ends_at'])) {
            if (\Carbon\Carbon::parse($data['ends_at'])->lt(\Carbon\Carbon::parse($data['starts_at']))) {
                return back()->withErrors(['ends_at' => 'La fecha de término no puede ser menor a la fecha de inicio.'])->withInput();
            }
        }

        Advertencia::create([
            'user_id'     => $data['user_id'] ?? null,
            'created_by'  => auth()->id(),
            'titulo'      => $data['titulo'],
            'mensaje'     => $data['mensaje'],
            'nivel'       => $data['nivel'],
            'starts_at'   => $data['starts_at'] ?? null,
            'ends_at'     => $data['ends_at'] ?? null,
            'activa'      => $activa,
        ]);

        return back()->with('ok', 'Advertencia creada.');
    }

    public function destroy(Advertencia $advertencia)
    {
        $advertencia->delete();
        return back()->with('ok', 'Advertencia eliminada.');
    }
}
