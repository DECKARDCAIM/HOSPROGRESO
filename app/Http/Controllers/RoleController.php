<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page   = (int) ($request->query('page', 1));

        $ttl      = now()->addMinutes(10);
        $version  = 'v2';
        $cacheKey = "roles:index:{$version}:status={$status}:q=" . urlencode((string)$search) . ":p={$page}";

        $roles = Cache::tags(['roles'])->remember($cacheKey, $ttl, function () use ($status, $search) {
            return Role::select('id','name','description','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->withCount('users')
                ->orderBy('name')
                ->paginate(25);
        });

        $roles->appends($request->all());

        return view('modules.roles.index', compact('roles','status','search'));
    }

    public function create()
    {
        return view('modules.roles.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:3|unique:roles,name',
            'description' => 'nullable|string|max:500',
        ];
        $messages = [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min'      => 'El nombre del rol debe tener más de 3 caracteres.',
            'name.unique'   => 'Ya existe un rol con este nombre.',
        ];
        $this->validate($request, $rules, $messages);

        $role              = new Role();
        $role->name        = $request->input('name');
        $role->description = $request->input('description');
        $role->is_active   = true;
        $role->save();

        Cache::tags(['roles'])->flush();

        return redirect()->route('roles.index')->with('toast', [
            'type'    => 'success',
            'title'   => 'Creación Exitosa',
            'message' => 'El rol ' . $role->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(Role $role)
    {
        $ttl      = now()->addHours(6);
        $version  = 'v2';
        $cacheKey = "roles:show:{$version}:{$role->id}";

        $cached = Cache::tags(['roles'])->remember($cacheKey, $ttl, function () use ($role) {
            return $role->only(['id','name','description','is_active']);
        });
        $role->fill($cached);

        return view('modules.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $rules = [
            'name'        => 'required|min:3|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
        ];
        $messages = [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min'      => 'El nombre del rol debe tener más de 3 caracteres.',
            'name.unique'   => 'Ya existe un rol con este nombre.',
        ];
        $this->validate($request, $rules, $messages);

        $role->name        = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        Cache::tags(['roles'])->flush();

        return redirect()->route('roles.index')->with('toast', [
            'type'    => 'info',
            'title'   => 'Actualización Exitosa',
            'message' => 'El rol ' . $role->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Role $role)
    {
        $roleName = $role->name;

        $role->is_active = false;
        $role->save();

        User::where('role_id', $role->id)->update(['is_active' => false]);

        Cache::tags(['roles'])->flush();

        return redirect()->route('roles.index')->with('toast', [
            'type'    => 'warning',
            'title'   => 'Eliminación Exitosa',
            'message' => 'El rol ' . $roleName . ' se ha eliminado correctamente. Los usuarios con este rol han sido desactivados.'
        ]);
    }

    public function reactivate($id)
    {
        $role = Role::findOrFail($id);
        $role->is_active = true;
        $role->save();

        Cache::tags(['roles'])->flush();

        return redirect()->route('roles.index', ['status' => 'inactive'])->with('toast', [
            'type'    => 'success',
            'title'   => 'Reactivación Exitosa',
            'message' => 'El rol ' . $role->name . ' ha sido reactivado correctamente.'
        ]);
    }
}