<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Mostrar formulario para asignar permisos a un rol
     */
    public function edit(Role $role)
    {
        // Obtener todos los permisos agrupados por módulo
        $permissions = Permission::all()->groupBy('module');
        
        // Obtener los IDs de permisos que ya tiene el rol
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('modules.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Actualizar permisos de un rol
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Sincronizar permisos (esto removerá los no seleccionados y agregará los nuevos)
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', "Permisos actualizados correctamente para el rol: {$role->name}");
    }
}