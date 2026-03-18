<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@dispensariolaherradura.cl'],
        ], [
            'email.ends_with' => 'Debes usar tu correo corporativo @dispensariolaherradura.cl.',
        ]);

        // ✅ Validación extra: que el usuario exista (así confirmas "de alguna manera" antes de enviar)
        $email = strtolower(trim($request->input('email')));
        if (!User::where('email', $email)->exists()) {
            return back()->withErrors([
                'email' => 'Este correo no está registrado en el sistema.',
            ]);
        }

        $status = Password::sendResetLink(['email' => $email]);

        // ✅ Mensaje claro de éxito / error (valida el resultado real del broker)
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Listo. Si el correo existe, te enviamos el enlace de recuperación.');
        }

        return back()->withErrors([
            'email' => 'No se pudo enviar el correo. Intenta nuevamente o contacta al administrador.',
        ]);
    }
}