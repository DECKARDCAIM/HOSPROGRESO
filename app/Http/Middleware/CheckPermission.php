<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
                // Cache per-user permission checks for 60s to reduce DB
                $has = Cache::tags(['permisos'])
                    ->remember("perm:{$user->id}:".trim($perm), now()->addSeconds(60), fn() => $user->hasPermission(trim($perm)));
                if ($has) {
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
            $has = Cache::tags(['permisos'])
                ->remember("perm:{$user->id}:{$permission}", now()->addSeconds(60), fn() => $user->hasPermission($permission));
            if (!$has) {
                return redirect()->route('permission.denied')
                               ->with('error', 'No tienes permiso para acceder a esta página.');
            }
        }

        return $next($request);
    }
}