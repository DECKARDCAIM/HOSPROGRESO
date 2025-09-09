<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    public function edit(Role $role)
    {
        $permissions = Cache::tags(['permisos'])->remember('permisos:all:v1', now()->addHours(12), fn() => Permission::all())->groupBy('module');

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('modules.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        // Limpiar cache de permisos
        \Illuminate\Support\Facades\Cache::tags(['permisos'])->flush();

        return redirect()
            ->route('roles.permissions.edit', $role)
            ->with('toast', [
                'type'    => 'success',
                'title'   => 'Permisos Actualizados',
                'message' => "Los permisos del rol <strong>{$role->name}</strong> se han actualizado correctamente."
            ]);
    }
}