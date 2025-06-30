<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cui' => ['nullable', 'string', 'size:13', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe una cuenta con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'cui.size' => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique' => 'Ya existe una cuenta con este CUI.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cui' => $data['cui'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role_id' => null, // Sin rol asignado al registrarse
            'is_active' => true, // Activo pero sin acceso hasta que tenga rol
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered($request, $user)
    {
        // Cerrar la sesión inmediatamente después del registro
        Auth::logout();
        
        // Notificar a los administradores sobre el nuevo registro
        $admins = User::whereHas('role', function($query) {
            $query->where('name', 'Administrador');
        })->where('is_active', true)->get();
        
        foreach ($admins as $admin) {
            NotificationService::create(
                'Nuevo Usuario Registrado',
                'El usuario ' . $user->name . ' (' . $user->email . ') se ha registrado y necesita asignación de rol.',
                'info',
                $admin->id
            );
        }

        // Notificar al usuario registrado
        NotificationService::create(
            'Registro Exitoso - Pendiente de Aprobación',
            'Tu cuenta ha sido creada correctamente. Un administrador debe asignarte un rol antes de que puedas acceder al sistema.',
            'warning',
            $user->id
        );

        return redirect('/login')->with('toast', [
            'type' => 'warning',
            'title' => 'Registro Exitoso',
            'message' => 'Tu cuenta ha sido creada. Un administrador debe asignarte un rol antes de que puedas acceder al sistema. Te notificaremos cuando esté listo.'
        ]);
    }
}
