<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar si hay múltiples permisos separados por | (OR lógico)
        if (str_contains($permission, '|')) {
            $permissions = explode('|', $permission);
            $hasAnyPermission = false;
            
            foreach ($permissions as $perm) {
                if ($user->hasPermission(trim($perm))) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                return redirect()->route('permission.denied')
                               ->with('error', 'No tienes permiso para acceder a esta página.');
            }
        } else {
            // Verificar si el usuario tiene el permiso requerido (modo original)
            if (!$user->hasPermission($permission)) {
                return redirect()->route('permission.denied')
                               ->with('error', 'No tienes permiso para acceder a esta página.');
            }
        }

        return $next($request);
    }
}