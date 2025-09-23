<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function index(Request $request)
    {
        $status      = $request->query('status', 'active');
        $roleFilter  = $request->query('role');
        $search      = $request->query('search', '');
        $page        = (int) ($request->query('page', 1));

        $ttl      = now()->addMinutes(10);
        $version  = 'v2';
        $cacheKey = "usuarios:index:{$version}:status={$status}:role=" . ($roleFilter ?: 'null')
                    . ":q=" . urlencode((string)$search) . ":p={$page}";

        $users = Cache::tags(['usuarios'])->remember($cacheKey, $ttl, function () use ($status, $roleFilter, $search) {
            return User::with('role:id,name')
                ->select('id','name','email','cui','role_id','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($roleFilter, fn($q) => $q->where('role_id', $roleFilter))
                ->when($search, function ($q) use ($search) {
                    $like = "%{$search}%";
                    $q->where(function ($w) use ($like) {
                        $w->where('name', 'like', $like)
                          ->orWhere('email', 'like', $like)
                          ->orWhere('cui', 'like', $like);
                    });
                })
                ->orderBy('name')
                ->paginate(25);
        });

        $users->appends($request->all());

        $roles = Cache::tags(['roles'])->remember(
            'roles:select:v1',
            now()->addHours(12),
            fn() => Role::where('is_active', true)->orderBy('name')->get(['id','name'])
        );

        return view('modules.users.index', compact('users','roles','status','roleFilter','search'));
    }

    public function create()
    {
        $roles = Cache::tags(['roles'])->remember(
            'roles:select:v1',
            now()->addHours(12),
            fn() => Role::where('is_active', true)->orderBy('name')->get(['id','name'])
        );

        return view('modules.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'cui'           => 'nullable|string|size:13|unique:users,cui',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'birth_date'    => 'nullable|date',
            'gender'        => 'nullable|in:M,F',
            'role_id'       => 'required|exists:roles,id',
            'password'      => 'required|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        $messages = [
            'name.required'       => 'El nombre completo es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'El correo electrónico debe tener un formato válido.',
            'email.unique'        => 'Ya existe un usuario con este correo electrónico.',
            'cui.size'            => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique'          => 'Ya existe un usuario con este CUI.',
            'role_id.required'    => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists'      => 'El rol seleccionado no es válido.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'La confirmación de contraseña no coincide.',
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg.',
            'profile_photo.max'   => 'La imagen no debe ser mayor a 2MB.',
        ];
        $this->validate($request, $rules, $messages);

        $user              = new User();
        $user->name        = $request->input('name');
        $user->email       = $request->input('email');
        $user->cui         = $request->input('cui');
        $user->phone       = $request->input('phone');
        $user->address     = $request->input('address');
        $user->birth_date  = $request->input('birth_date');
        $user->gender      = $request->input('gender');
        $user->role_id     = $request->input('role_id');
        
        // Guardar la contraseña sin hashear temporalmente para impresión
        $plainPassword = $request->input('password');
        $user->password    = Hash::make($plainPassword);
        $user->is_active   = true;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();
        
        // Guardar contraseña temporal en sesión para impresión (se elimina después de 1 hora)
        session(['temp_password_' . $user->id => $plainPassword], 60);
        session(['temp_password_created_' . $user->id => time()], 60);

        Cache::tags(['usuarios'])->flush();

        return redirect()->route('usuarios.index')->with('toast', [
            'type'    => 'success',
            'title'   => 'Creación Exitosa',
            'message' => 'El usuario ' . $user->name . ' se ha creado correctamente.'
        ]);
    }

    public function show(User $user)
    {
        return view('modules.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $ttl      = now()->addHours(6);
        $version  = 'v2';
        $cacheKey = "usuarios:show:{$version}:{$user->id}";

        $cached = Cache::tags(['usuarios'])->remember($cacheKey, $ttl, function () use ($user) {
            return $user->only([
                'id','name','email','cui','phone','address','birth_date','gender','role_id','is_active','profile_photo_path'
            ]);
        });
        $user->fill($cached);

        $roles = Cache::tags(['roles'])->remember(
            'roles:select:v1',
            now()->addHours(12),
            fn() => Role::where('is_active', true)->orderBy('name')->get(['id','name'])
        );

        return view('modules.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => ['required','email', Rule::unique('users')->ignore($user->id)],
            'cui'           => ['nullable','string','size:13', Rule::unique('users')->ignore($user->id)],
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'birth_date'    => 'nullable|date',
            'gender'        => 'nullable|in:M,F',
            'role_id'       => 'required|exists:roles,id',
            'password'      => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        $messages = [
            'name.required'       => 'El nombre completo es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'El correo electrónico debe tener un formato válido.',
            'email.unique'        => 'Ya existe un usuario con este correo electrónico.',
            'cui.size'            => 'El CUI debe tener exactamente 13 dígitos.',
            'cui.unique'          => 'Ya existe un usuario con este CUI.',
            'role_id.required'    => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists'      => 'El rol seleccionado no es válido.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'La confirmación de contraseña no coincide.',
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg.',
            'profile_photo.max'   => 'La imagen no debe ser mayor a 2MB.',
        ];
        $this->validate($request, $rules, $messages);

        $user->name       = $request->input('name');
        $user->email      = $request->input('email');
        $user->cui        = $request->input('cui');
        $user->phone      = $request->input('phone');
        $user->address    = $request->input('address');
        $user->birth_date = $request->input('birth_date');
        $user->gender     = $request->input('gender');
        $user->role_id    = $request->input('role_id');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        Cache::tags(['usuarios'])->flush();

        return redirect()->route('usuarios.index')->with('toast', [
            'type'    => 'success',
            'title'   => 'Actualización Exitosa',
            'message' => 'El usuario ' . $user->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(User $user)
    {
        $userName        = $user->name;
        $user->is_active = false;
        $user->save();

        Cache::tags(['usuarios'])->flush();

        return redirect()->route('usuarios.index')->with('toast', [
            'type'    => 'warning',
            'title'   => 'Desactivación Exitosa',
            'message' => 'El usuario ' . $userName . ' se ha desactivado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $user = User::findOrFail($id);

        if (!$user->role || !$user->role->is_active) {
            return redirect()->route('usuarios.index', ['status' => 'inactive'])->with('toast', [
                'type'    => 'error',
                'title'   => 'Error de Reactivación',
                'message' => 'No se puede reactivar el usuario porque su rol está inactivo o no tiene rol asignado.'
            ]);
        }

        $user->is_active = true;
        $user->save();

        Cache::tags(['usuarios'])->flush();

        return redirect()->route('usuarios.index', ['status' => 'inactive'])->with('toast', [
            'type'    => 'success',
            'title'   => 'Reactivación Exitosa',
            'message' => 'El usuario ' . $user->name . ' ha sido reactivado correctamente.'
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $rules = ['password' => 'required|string|min:8|confirmed'];
        $messages = [
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
        $this->validate($request, $rules, $messages);

        // Guardar la contraseña sin hashear temporalmente para impresión
        $plainPassword = $request->input('password');
        $user->password = Hash::make($plainPassword);
        $user->save();
        
        // Guardar contraseña temporal en sesión para impresión (se elimina después de 1 hora)
        session(['temp_password_' . $user->id => $plainPassword], 60);
        session(['temp_password_created_' . $user->id => time()], 60);

        Cache::tags(['usuarios'])->flush();

        return redirect()->route('usuarios.edit', $user->id)->with('toast', [
            'type'    => 'success',
            'title'   => 'Contraseña Restablecida',
            'message' => 'La contraseña del usuario ' . $user->name . ' se ha restablecido correctamente.'
        ]);
    }

    public function printCredentials(User $user)
    {
        // Verificar si el usuario es administrador
        $isAdmin = $user->role && strtolower($user->role->name) === 'administrador';
        
        // Obtener la contraseña sin hashear (solo para impresión)
        $plainPassword = session('temp_password_' . $user->id);
        $passwordCreatedAt = session('temp_password_created_' . $user->id);
        
        // Si es administrador, mostrar "Contraseña no disponible"
        if ($isAdmin) {
            $plainPassword = 'Contraseña no disponible';
        } else {
            // Verificar si la contraseña temporal ha expirado (60 minutos)
            $isExpired = false;
            if ($passwordCreatedAt) {
                $expirationTime = $passwordCreatedAt + (60 * 60); // 60 minutos en segundos
                $isExpired = time() > $expirationTime;
            }
            
            // Si no hay contraseña temporal o ha expirado, mostrar "no disponible"
            if (!$plainPassword || $isExpired) {
                $plainPassword = 'Contraseña no disponible';
            }
        }
        
        // Generar PDF usando DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('modules.users.print-credentials', compact('user', 'plainPassword'));
        
        // Configurar el PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);
        
        // Generar nombre del archivo
        $filename = 'Credenciales_' . $user->name . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // Retornar el PDF para descarga
        return $pdf->download($filename);
    }


    /**
     * Método para generar nueva contraseña temporal manualmente
     */
    public function generateNewPassword(User $user)
    {
        // Verificar si el usuario es administrador
        $isAdmin = $user->role && strtolower($user->role->name) === 'administrador';
        
        if ($isAdmin) {
            return redirect()->route('usuarios.print-credentials', $user->id)
                ->with('toast', [
                    'type' => 'warning',
                    'title' => 'Acción No Permitida',
                    'message' => 'No se puede generar contraseña para administradores.'
                ]);
        }
        
        // Generar nueva contraseña temporal
        $plainPassword = 'Temp' . rand(1000, 9999) . '!';
        $user->password = Hash::make($plainPassword);
        $user->save();
        
        // Guardar nueva contraseña temporal en sesión por 60 minutos
        session(['temp_password_' . $user->id => $plainPassword], 60);
        session(['temp_password_created_' . $user->id => time()], 60);
        
        
        return redirect()->route('usuarios.print-credentials', $user->id)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Nueva Contraseña Generada',
                'message' => 'Se ha generado una nueva contraseña temporal válida por 60 minutos.'
            ]);
    }
}