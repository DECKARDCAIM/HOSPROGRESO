<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/panel';

    public function __construct()
    {
        $this->middleware('guest');
        // Bloquear acceso al registro
        abort(404, 'Registro deshabilitado');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'cui'      => ['nullable','string','size:13','unique:users'],
            'phone'    => ['nullable','string','max:20'],
        ], [
            'name.required'     => 'El nombre completo es obligatorio.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico debe tener un formato válido.',
            'email.unique'      => 'Ya existe una cuenta con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'La confirmación de contraseña no coincide.',
            'cui.size'          => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique'        => 'Ya existe una cuenta con este CUI.',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'cui'       => $data['cui']   ?? null,
            'phone'     => $data['phone'] ?? null,
            'role_id'   => null,
            'is_active' => true,
        ]);
    }

    protected function registered($request, $user)
    {
        Auth::logout();

        return redirect('/login')->with('toast', [
            'type'    => 'warning',
            'title'   => 'Registro Exitoso',
            'message' => 'Tu cuenta ha sido creada. Un administrador debe asignarte un rol antes de que puedas acceder al sistema.'
        ]);
    }
}