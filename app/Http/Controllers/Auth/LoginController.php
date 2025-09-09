<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/panel';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            if (!$user->canAccess()) {
                Auth::logout();

                if (!$user->is_active) {
                    $message = 'Su cuenta está desactivada. Contacte al administrador.';
                } elseif (!$user->role_id) {
                    $message = 'Su cuenta no tiene un rol asignado. Un administrador debe asignarle un rol para continuar.';
                } elseif (!$user->role || !$user->role->is_active) {
                    $message = 'Su rol está inactivo. Contacte al administrador.';
                } else {
                    $message = 'No tiene permisos para acceder al sistema. Contacte al administrador.';
                }

                session()->flash('login_error', $message);
                return false;
            }
            return true;
        }

        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect('/panel')->with('toast', [
            'type'    => 'success',
            'title'   => '¡Bienvenido, ' . $user->name . '!',
            'message' => 'Has iniciado sesión correctamente.'
        ]);
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Usuario';

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('toast', [
            'type'    => 'success',
            'title'   => 'Sesión cerrada',
            'message' => 'Has cerrado sesión correctamente. ¡Hasta pronto, ' . $userName . '!'
        ]);
    }
}