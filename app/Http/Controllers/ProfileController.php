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
        return view('modules.profile.index', [
            'user' => Auth::user(),
            'sessions' => $this->getSessionsProperty()
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
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

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => '/storage/' . $path]);

        NotificationService::create('Foto de Perfil Actualizada', 'Tu foto de perfil ha sido actualizada.', 'info');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'path' => $user->profile_photo_url]);
        }
        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Foto de Perfil Actualizada',
            'message' => 'Foto de perfil actualizada correctamente.'
        ]);
    }

    /**
     * Delete a specific user session.
     */
    public function logoutSession($session_id)
    {
        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Esta función requiere el driver de sesión de base deatos.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('id', $session_id)
            ->where('user_id', Auth::user()->getKey())
            ->delete();

        NotificationService::create('Sesión Cerrada', 'Una sesión ha sido cerrada exitosamente.', 'warning');

        return back()->with('success', 'La sesión ha sido cerrada exitosamente.');
    }

    /**
     * Update the user's banner photo.
     */
    public function updateBanner(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'banner_photo' => ['required', 'image', 'max:4096'],
        ]);

        if ($user->banner_photo_path) {
            Storage::disk('public')->delete($user->banner_photo_path);
        }
        $path = $request->file('banner_photo')->store('banner-photos', 'public');
        $user->update(['banner_photo_path' => '/storage/' . $path]);

        NotificationService::create('Banner Actualizado', 'Tu banner ha sido actualizado correctamente.', 'info');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'path' => $user->banner_photo_url]);
        }
        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Banner Actualizado',
            'message' => 'Banner actualizado correctamente.'
        ]);
    }

    public function logoutOtherBrowserSessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Esta función requiere el driver de sesión de base deatos.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getKey())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        Auth::logoutOtherDevices($request->password);

        NotificationService::create('Sesiones Cerradas', 'Se han cerrado las demás sesiones de navegador.', 'warning');

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Sesiones Cerradas',
            'message' => 'Se han cerrado las demás sesiones de navegador.'
        ]);
    }
} 