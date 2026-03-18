<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Credenciales inválidas.'])
                ->onlyInput('email');
        }

        // ✅ Importante (seguridad)
        $request->session()->regenerate();

        // ✅ Guardar último login real
        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ])->save();
        }

        return redirect()->intended($this->roleHomeUrl($request));
    }

    public function home(Request $request)
    {
        return redirect()->to($this->roleHomeUrl($request));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function roleHomeUrl(Request $request): string
    {
        $user = Auth::user();

        if ($user?->hasRole('admin')) {
            return route('admin.home');
        }

        if ($user?->hasRole('operador')) {
            return route('operador.home');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return route('login');
    }
}
