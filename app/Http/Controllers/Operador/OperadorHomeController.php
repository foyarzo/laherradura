<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Advertencia;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;

class OperadorHomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now  = now();

        $mensajeBienvenida = Setting::getValue(
            'mensaje_bienvenida',
            'Bienvenido al sistema.'
        );

        $advertencias = Advertencia::query()
            ->where('activa', 1)
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($a) {
                $nivel = Str::of($a->nivel ?? 'info')->lower()->trim()->toString();
                $a->nivel = in_array($nivel, ['info', 'success', 'warning', 'danger'], true) ? $nivel : 'info';

                $a->activa  = (bool) $a->activa;
                $a->user_id = is_null($a->user_id) ? null : (int) $a->user_id;

                return $a;
            });

        $admins = User::query()
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })
            ->whereNotNull('email')
            ->select('id', 'email')
            ->orderBy('email')
            ->get();

        return view('operador.home', compact(
            'advertencias',
            'admins',
            'mensajeBienvenida'
        ));
    }
}