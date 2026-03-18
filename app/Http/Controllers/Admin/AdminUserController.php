<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertencia;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        $roles = Role::orderBy('name')->get();

        $sessions = DB::table('sessions')
            ->leftJoin('users', 'users.id', '=', 'sessions.user_id')
            ->select([
                'sessions.id',
                'sessions.user_id',
                'users.name as user_name',
                'users.email as user_email',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
            ])
            ->orderByDesc('sessions.last_activity')
            ->limit(100)
            ->get();

        $lastLogins = User::query()
            ->select(['id', 'name', 'email', 'last_login_at', 'last_login_ip'])
            ->orderByDesc('last_login_at')
            ->limit(50)
            ->get();

        $advertencias = Advertencia::with('creator', 'user')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('admin.usuarios', compact(
            'users',
            'roles',
            'sessions',
            'lastLogins',
            'advertencias'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:190', 'unique:users,email'],
            'rut'       => ['required', 'string', 'max:30', 'unique:users,rut'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable'],
            'password'  => ['required', 'string', 'min:6'],
            'role_id'   => ['required', Rule::exists('roles', 'id')],
        ]);

        try {
            $user = User::create([
                'name'       => trim($data['name']),
                'email'      => trim($data['email']),
                'rut'        => trim($data['rut']),
                'phone'      => isset($data['phone']) ? trim((string) $data['phone']) : null,
                'is_active'  => $request->has('is_active') ? $request->boolean('is_active') : true,
                'created_by' => auth()->id(),
                'password'   => $data['password'],
            ]);

            $user->roles()->sync([(int) $data['role_id']]);

            return redirect()->route('admin.usuarios')->with('ok', 'Usuario creado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'rut'       => ['required', 'string', 'max:30', Rule::unique('users', 'rut')->ignore($user->id)],
            'phone'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable'],
            'password'  => ['nullable', 'string', 'min:6'],
            'role_id'   => ['required', Rule::exists('roles', 'id')],
        ]);

        if (auth()->id() === $user->id) {
            $adminRoleId = Role::where('slug', 'admin')->value('id');
            if ($adminRoleId && (int) $data['role_id'] !== (int) $adminRoleId) {
                return back()->withErrors(['role_id' => 'No puedes quitarte el rol admin a ti mismo.'])->withInput();
            }
        }

        try {
            $user->name  = trim($data['name']);
            $user->email = trim($data['email']);
            $user->rut   = trim($data['rut']);
            $user->phone = isset($data['phone']) ? trim((string) $data['phone']) : null;

            $user->is_active = $request->boolean('is_active');

            if (!empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            $user->roles()->sync([(int) $data['role_id']]);

            return redirect()->route('admin.usuarios')->with('ok', 'Usuario actualizado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['delete' => 'No puedes eliminar tu propio usuario.']);
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.usuarios')->with('ok', 'Usuario eliminado.');
    }
}