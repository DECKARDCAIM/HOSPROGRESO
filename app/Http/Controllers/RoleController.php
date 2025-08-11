<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');
        
        $page = (int) ($request->query('page', 1));
        $key = "roles:index:v1:status={$status}:q=".urlencode((string)$search).":p={$page}";
        $roles = Cache::tags(['roles','listados'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return Role::where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                })
                ->withCount('users')
                ->orderBy('name')
                ->paginate(25);
        });
        $roles->appends($request->all());

        return view('modules.roles.index', compact('roles', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3|unique:roles,name',
            'description' => 'nullable|string|max:500'
        ];
        
        $messages = [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min' => 'El nombre del rol debe tener más de 3 caracteres.',
            'name.unique' => 'Ya existe un rol con este nombre.'
        ];
        
        $this->validate($request, $rules, $messages);

        $role = new Role();
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        NotificationService::notifyCreate('Rol', $role->name);

        return redirect()->route('roles.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Exitosa',
            'message' => 'El rol ' . $role->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('modules.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $rules = [
            'name' => 'required|min:3|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500'
        ];
        
        $messages = [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min' => 'El nombre del rol debe tener más de 3 caracteres.',
            'name.unique' => 'Ya existe un rol con este nombre.'
        ];
        
        $this->validate($request, $rules, $messages);

        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        NotificationService::notifyUpdate('Rol', $role->name);

        return redirect()->route('roles.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Exitosa',
            'message' => 'El rol ' . $role->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Role $role)
    {
        $roleName = $role->name;
        
        // Desactivar el rol
        $role->is_active = false;
        $role->save();
        
        // Desactivar todos los usuarios con este rol
        User::where('role_id', $role->id)->update(['is_active' => false]);
        
        NotificationService::notifyDelete('Rol', $roleName);
        
        return redirect()->route('roles.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Exitosa',
            'message' => 'El rol ' . $roleName . ' se ha eliminado correctamente. Los usuarios con este rol han sido desactivados.'
        ]);
    }

    /**
     * Reactivar rol inactivo.
     */
    public function reactivate($id)
    {
        $role = Role::findOrFail($id);
        $role->is_active = true;
        $role->save();
        
        NotificationService::notifyUpdate('Rol', $role->name);
        
        return redirect()->route('roles.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Exitosa',
                'message' => 'El rol ' . $role->name . ' ha sido reactivado correctamente.'
            ]);
    }
}
