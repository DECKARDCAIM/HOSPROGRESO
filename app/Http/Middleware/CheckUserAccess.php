<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado, verificar si puede acceder
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si el usuario no puede acceder al sistema, cerrar sesión
            $canAccess = Cache::tags(['usuarios'])
                ->remember("user:{$user->id}:can_access", now()->addSeconds(60), fn() => $user->canAccess());
            if (!$canAccess) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Determinar el mensaje de error específico
                if (!$user->is_active) {
                    $message = 'Su cuenta ha sido desactivada. Contacte al administrador.';
                } elseif (!$user->role_id) {
                    $message = 'Su cuenta no tiene un rol asignado. Un administrador debe asignarle un rol para continuar.';
                } elseif (!$user->role || !$user->role->is_active) {
                    $message = 'Su rol ha sido desactivado. Contacte al administrador.';
                } else {
                    $message = 'Ya no tiene permisos para acceder al sistema. Contacte al administrador.';
                }
                
                return redirect('/login')->with('toast', [
                    'type' => 'error',
                    'title' => 'Acceso Denegado',
                    'message' => $message
                ]);
            }
        }
        
        return $next($request);
    }
}
