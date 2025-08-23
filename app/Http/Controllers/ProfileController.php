<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        $version = 'no-db';
        if (config('session.driver') === 'database') {
            $base = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $userId);

            $maxLastActivity = (int) ($base->max('last_activity') ?? 0);
            $count = (int) ($base->count() ?? 0);

            $version = $maxLastActivity . ':' . $count;
        }

        $cacheKey = "profile:sessions:user:{$userId}:v:{$version}";

        $sessions = Cache::tags(['perfil', 'perfil:' . $userId])
            ->remember($cacheKey, now()->addMinutes(5), fn() => $this->getSessionsProperty());

        return view('modules.profile.index', [
            'user' => Auth::user(),
            'sessions' => $sessions,
        ]);
    }

    protected function getSessionsProperty()
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getKey())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $agent = $this->createAgent($session);
                return (object) [
                    'id' => $session->id,
                    'agent' => [
                        'is_desktop' => $agent->isDesktop(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
    }

    protected function createAgent($session)
    {
        $agent = new Agent();
        $agent->setUserAgent($session->user_agent);
        return $agent;
    }

    public function edit()
    {
        return view('modules.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cui' => ['nullable', 'string', 'max:13'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['Masculino', 'Femenino'])],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->route('profile.edit')->with('toast', [
            'type' => 'success',
            'title' => 'Información Actualizada',
            'message' => 'Tu información ha sido actualizada correctamente.'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Contraseña Actualizada',
            'message' => 'Contraseña actualizada correctamente.'
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ]);

            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto de perfil actualizada correctamente.',
                    'path' => $user->profile_photo_url
                ]);
            }

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Foto de Perfil Actualizada',
                'message' => 'Foto de perfil actualizada correctamente.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . collect($e->errors())->flatten()->first()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir la foto: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function logoutSession($session_id)
    {
        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Esta función requiere el driver de sesión de base de datos.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('id', $session_id)
            ->where('user_id', Auth::user()->getKey())
            ->delete();

        Cache::tags(['perfil'])->forget('profile:sessions:user:' . Auth::id());

        return back()->with('success', 'La sesión ha sido cerrada exitosamente.');
    }

    public function updateBanner(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'banner_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            ]);

            if ($user->banner_photo_path && Storage::disk('public')->exists($user->banner_photo_path)) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }

            $path = $request->file('banner_photo')->store('banner-photos', 'public');
            $user->update(['banner_photo_path' => $path]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner actualizado correctamente.',
                    'path' => $user->banner_photo_url
                ]);
            }

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Banner Actualizado',
                'message' => 'Banner actualizado correctamente.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . collect($e->errors())->flatten()->first()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el banner: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        try {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->update(['profile_photo_path' => null]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto de perfil eliminada correctamente.',
                    'path' => $user->profile_photo_url
                ]);
            }

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Foto Eliminada',
                'message' => 'Foto de perfil eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la foto: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function deleteBanner(Request $request)
    {
        $user = Auth::user();

        try {
            if ($user->banner_photo_path && Storage::disk('public')->exists($user->banner_photo_path)) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }

            $user->update(['banner_photo_path' => null]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner eliminado correctamente.',
                    'path' => $user->banner_photo_url
                ]);
            }

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Banner Eliminado',
                'message' => 'Banner eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el banner: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function logoutOtherBrowserSessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Esta función requiere el driver de sesión de base de datos.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getKey())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        Auth::logoutOtherDevices($request->password);

        Cache::tags(['perfil'])->forget('profile:sessions:user:' . Auth::id());

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Sesiones Cerradas',
            'message' => 'Se han cerrado las demás sesiones de navegador.'
        ]);
    }
}