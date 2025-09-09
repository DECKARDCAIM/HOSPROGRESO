<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        $perms = str_contains($permission, '|')
            ? array_map('trim', explode('|', $permission))
            : [trim($permission)];

        $stampSource = $user->permissions_updated_at ?? $user->updated_at ?? now();
        $stamp = is_object($stampSource) && method_exists($stampSource, 'timestamp')
            ? $stampSource->timestamp
            : strtotime((string) $stampSource);
        $ns = sprintf('v2:u=%d:r=%s:t=%s', $user->id, $user->role_id ?? 'null', $stamp);

        $ttl = now()->addSeconds(60);

        $authorized = false;
        foreach ($perms as $perm) {
            if ($perm === '') {
                continue;
            }
            $key = "perm:{$ns}:p=" . urlencode($perm);

            $has = Cache::tags(['permisos'])->remember($key, $ttl, function () use ($user, $perm) {
                return (bool) $user->hasPermission($perm);
            });

            if ($has) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            return redirect()
                ->route('permission.denied')
                ->with('error', 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}