<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile view.
     */
    public function index()
    {
        $sessions = Cache::tags(['perfil'])->remember('profile:sessions:user:'.Auth::id(), now()->addMinutes(5), fn()=> $this->getSessionsProperty());
        return view('modules.profile.index', [
            'user' => Auth::user(),
            'sessions' => $sessions
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
                ->get()->map(function ($session) {
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

    /**
     * Display the user's profile edit form.
     */
    public function edit()
    {
        return view('modules.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
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

        NotificationService::notifyUpdate('Perfil', $user->name);

        return redirect()->route('profile.edit')->with('toast', [
            'type' => 'success',
            'title' => 'Información Actualizada',
            'message' => 'Tu información ha sido actualizada correctamente.'
        ]);
    }

    /**
     * Update the user's password.
     */
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

        NotificationService::create('Contraseña Actualizada', 'Tu contraseña ha sido actualizada correctamente.', 'info');

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Contraseña Actualizada',
            'message' => 'Contraseña actualizada correctamente.'
        ]);
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ]);

            // Eliminar la foto anterior si existe
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);

            NotificationService::create('Foto de Perfil Actualizada', 'Tu foto de perfil ha sido actualizada.', 'info');

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

    /**
     * Delete a specific user session.
     */
    public function logoutSession($session_id)
    {
        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Esta función requiere el driver de sesión de base de datos.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('id', $session_id)
            ->where('user_id', Auth::user()->getKey())
            ->delete();

        // Invalidar caché del listado de sesiones para el usuario actual
        Cache::tags(['perfil'])->forget('profile:sessions:user:' . Auth::id());

        NotificationService::create('Sesión Cerrada', 'Una sesión ha sido cerrada exitosamente.', 'warning');

        return back()->with('success', 'La sesión ha sido cerrada exitosamente.');
    }

    /**
     * Update the user's banner photo.
     */
    public function updateBanner(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'banner_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            ]);

            // Eliminar el banner anterior si existe
            if ($user->banner_photo_path && Storage::disk('public')->exists($user->banner_photo_path)) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }

            $path = $request->file('banner_photo')->store('banner-photos', 'public');
            $user->update(['banner_photo_path' => $path]);

            NotificationService::create('Banner Actualizado', 'Tu banner ha sido actualizado correctamente.', 'info');

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

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        try {
            // Eliminar la foto del storage si existe
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Resetear a null para usar la foto por defecto
            $user->update(['profile_photo_path' => null]);

            NotificationService::create('Foto Eliminada', 'Tu foto de perfil ha sido eliminada.', 'warning');

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

    /**
     * Delete the user's banner photo.
     */
    public function deleteBanner(Request $request)
    {
        $user = Auth::user();

        try {
            // Eliminar el banner del storage si existe
            if ($user->banner_photo_path && Storage::disk('public')->exists($user->banner_photo_path)) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }

            // Resetear a null para usar el banner por defecto
            $user->update(['banner_photo_path' => null]);

            NotificationService::create('Banner Eliminado', 'Tu banner ha sido eliminado.', 'warning');

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

        // Invalidar caché del listado de sesiones para el usuario actual
        Cache::tags(['perfil'])->forget('profile:sessions:user:' . Auth::id());

        NotificationService::create('Sesiones Cerradas', 'Se han cerrado las demás sesiones de navegador.', 'warning');

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Sesiones Cerradas',
            'message' => 'Se han cerrado las demás sesiones de navegador.'
        ]);
    }
} 