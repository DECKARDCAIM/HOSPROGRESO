<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $uSource = $user->permissions_updated_at ?? $user->updated_at ?? now();
            $uStamp  = $uSource instanceof \DateTimeInterface ? $uSource->getTimestamp() : strtotime((string) $uSource);

            $rStamp = 0;
            if ($user->role) {
                $rSource = $user->role->updated_at ?? null;
                $rStamp  = $rSource instanceof \DateTimeInterface ? $rSource->getTimestamp() : ($rSource ? strtotime((string) $rSource) : 0);
            }

            $ns = sprintf('v2:u=%d:r=%s:tu=%s:tr=%s', $user->id, $user->role_id ?? 'null', $uStamp, $rStamp);
            $ttl = now()->addSeconds(60);

            $canAccess = Cache::tags(['usuarios'])->remember("user:can_access:{$ns}", $ttl, function () use ($user) {
                return (bool) $user->canAccess();
            });

            if (!$canAccess) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if (!$user->is_active) {
                    $message = 'Su cuenta ha sido desactivada. Contacte al administrador.';
                } elseif (!$user->role_id) {
                    $message = 'Su cuenta no tiene un rol asignado. Un administrador debe asignarle un rol para continuar.';
                } elseif (!$user->role || !$user->role->is_active) {
                    $message = 'Su rol ha sido desactivado. Contacte al administrador.';
                } else {
                    $message = 'Ya no tiene permisos para acceder al sistema. Contacte al administrador.';
                }

                $loginUrl = app('router')->has('login') ? route('login') : '/login';

                return redirect($loginUrl)->with('toast', [
                    'type'    => 'error',
                    'title'   => 'Acceso Denegado',
                    'message' => $message,
                ]);
            }
        }

        return $next($request);
    }
}