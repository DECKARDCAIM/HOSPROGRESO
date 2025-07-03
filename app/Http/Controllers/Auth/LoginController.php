<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/panel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            
            // Verificar si el usuario puede acceder al sistema
            if (!$user->canAccess()) {
                Auth::logout();
                
                // Determinar el mensaje de error específico
                if (!$user->is_active) {
                    $message = 'Su cuenta está desactivada. Contacte al administrador.';
                } elseif (!$user->role_id) {
                    $message = 'Su cuenta no tiene un rol asignado. Un administrador debe asignarle un rol para continuar.';
                } elseif (!$user->role || !$user->role->is_active) {
                    $message = 'Su rol está inactivo. Contacte al administrador.';
                } else {
                    $message = 'No tiene permisos para acceder al sistema. Contacte al administrador.';
                }
                
                // No podemos hacer redirect aquí, solo retornar false para que el trait maneje el error
                session()->flash('login_error', $message);
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        NotificationService::notifyLogin($user->name, $user->id);

        return redirect('/panel')->with('toast', [
            'type' => 'success',
            'title' => '¡Bienvenido, ' . $user->name . '!',
            'message' => 'Has iniciado sesión correctamente.'
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Usuario';
        
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('toast', [
            'type' => 'info',
            'title' => 'Sesión cerrada',
            'message' => 'Has cerrado sesión correctamente. ¡Hasta pronto, ' . $userName . '!'
        ]);
    }
}
