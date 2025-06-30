<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Acceso no autorizado. Solo los administradores pueden gestionar usuarios.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $role_filter = $request->query('role');
        $search = $request->query('search');
        
        $users = User::with('role')
            ->where('is_active', $status === 'active' ? 1 : 0)
            ->when($role_filter, function ($query) use ($role_filter) {
                return $query->where('role_id', $role_filter);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('cui', 'like', "%$search%");
                });
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends($request->all());

        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('modules.users.index', compact('users', 'roles', 'status', 'role_filter', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('modules.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cui' => 'nullable|string|size:13|unique:users,cui',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
        
        $messages = [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'cui.size' => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique' => 'Ya existe un usuario con este CUI.',
            'role_id.required' => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg.',
            'profile_photo.max' => 'La imagen no debe ser mayor a 2MB.'
        ];
        
        $this->validate($request, $rules, $messages);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->cui = $request->input('cui');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->birth_date = $request->input('birth_date');
        $user->gender = $request->input('gender');
        $user->role_id = $request->input('role_id');
        $user->password = Hash::make($request->input('password'));
        $user->is_active = true;

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        NotificationService::notifyCreate('Usuario', $user->name);

        return redirect()->route('usuarios.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Exitosa',
            'message' => 'El usuario ' . $user->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('modules.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('modules.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'cui' => ['nullable', 'string', 'size:13', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
        
        $messages = [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'cui.size' => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique' => 'Ya existe un usuario con este CUI.',
            'role_id.required' => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg.',
            'profile_photo.max' => 'La imagen no debe ser mayor a 2MB.'
        ];
        
        $this->validate($request, $rules, $messages);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->cui = $request->input('cui');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->birth_date = $request->input('birth_date');
        $user->gender = $request->input('gender');
        $user->role_id = $request->input('role_id');

        // Solo actualizar contraseña si se proporcionó una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            // Eliminar la foto anterior si existe
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        NotificationService::notifyUpdate('Usuario', $user->name);

        return redirect()->route('usuarios.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Exitosa',
            'message' => 'El usuario ' . $user->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->is_active = false;
        $user->save();
        
        NotificationService::notifyDelete('Usuario', $userName);
        
        return redirect()->route('usuarios.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Desactivación Exitosa',
            'message' => 'El usuario ' . $userName . ' se ha desactivado correctamente.'
        ]);
    }

    /**
     * Reactivar usuario inactivo.
     */
    public function reactivate($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar que el rol del usuario esté activo
        if (!$user->role || !$user->role->is_active) {
            return redirect()->route('usuarios.index', ['status' => 'inactive'])
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error de Reactivación',
                    'message' => 'No se puede reactivar el usuario porque su rol está inactivo o no tiene rol asignado.'
                ]);
        }
        
        $user->is_active = true;
        $user->save();
        
        NotificationService::notifyUpdate('Usuario', $user->name);
        
        return redirect()->route('usuarios.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Exitosa',
                'message' => 'El usuario ' . $user->name . ' ha sido reactivado correctamente.'
            ]);
    }

    /**
     * Restablecer contraseña de usuario
     */
    public function resetPassword(Request $request, User $user)
    {
        $rules = [
            'password' => 'required|string|min:8|confirmed'
        ];
        
        $messages = [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.'
        ];
        
        $this->validate($request, $rules, $messages);
        
        $user->password = Hash::make($request->input('password'));
        $user->save();
        
        NotificationService::notifyUpdate('Contraseña de Usuario', $user->name);
        
        return redirect()->route('usuarios.edit', $user->id)->with('toast', [
            'type' => 'success',
            'title' => 'Contraseña Restablecida',
            'message' => 'La contraseña del usuario ' . $user->name . ' se ha restablecido correctamente.'
        ]);
    }
}
