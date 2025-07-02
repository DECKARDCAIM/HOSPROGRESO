<?php

namespace App\Http\Controllers;

use App\Models\ControlType;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ControlTypeController extends Controller
{
    /**
     * Mostrar lista de tipos de control
     */
    public function index()
    {
        $controlTypes = ControlType::orderBy('name')->paginate(25);
        return view('modules.control_types.index', compact('controlTypes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('modules.control_types.create');
    }

    /**
     * Guardar nuevo tipo de control
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:control_types,name',
            'code' => 'required|string|max:50|unique:control_types,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $controlType = ControlType::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        NotificationService::notifyCreate('Tipo de Control', $controlType->name);

        return redirect()->route('control-types.index')
            ->with('success', [
                'title' => 'Tipo de Control Creado',
                'message' => 'El tipo de control se ha creado correctamente.'
            ]);
    }

    /**
     * Mostrar detalles del tipo de control
     */
    public function show(ControlType $controlType)
    {
        return view('modules.control_types.show', compact('controlType'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(ControlType $controlType)
    {
        return view('modules.control_types.edit', compact('controlType'));
    }

    /**
     * Actualizar tipo de control
     */
    public function update(Request $request, ControlType $controlType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:control_types,name,' . $controlType->id,
            'code' => 'required|string|max:50|unique:control_types,code,' . $controlType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $controlType->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        NotificationService::notifyUpdate('Tipo de Control', $controlType->name);

        return redirect()->route('control-types.index')
            ->with('success', [
                'title' => 'Tipo de Control Actualizado',
                'message' => 'El tipo de control se ha actualizado correctamente.'
            ]);
    }

    /**
     * Eliminar tipo de control
     */
    public function destroy(ControlType $controlType)
    {
        try {
            // Verificar si tiene consultas asociadas
            if ($controlType->medicalConsultations()->count() > 0) {
                return back()->withErrors(['general' => 'No se puede eliminar un tipo de control que tiene consultas médicas asociadas.']);
            }

            $name = $controlType->name;
            $controlType->delete();

            NotificationService::notifyDelete('Tipo de Control', $name);

            return redirect()->route('control-types.index')
                ->with('toast', [
                    'type' => 'warning',
                    'title' => 'Eliminación Exitosa',
                    'message' => 'El tipo de control se ha eliminado correctamente.'
                ]);
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Error al eliminar el tipo de control: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus(ControlType $controlType)
    {
        $controlType->is_active = !$controlType->is_active;
        $controlType->save();

        $status = $controlType->is_active ? 'activado' : 'desactivado';
        
        NotificationService::notifyUpdate('Tipo de Control', $controlType->name . ' ' . $status);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'is_active' => $controlType->is_active
        ]);
    }

    /**
     * Reactivar tipo de control
     */
    public function reactivate($id)
    {
        $controlType = ControlType::findOrFail($id);
        $controlType->is_active = true;
        $controlType->save();

        NotificationService::notifyUpdate('Tipo de Control', $controlType->name . ' reactivado');

        return redirect()->route('control-types.index')
            ->with('success', [
                'title' => 'Tipo de Control Reactivado',
                'message' => 'El tipo de control se ha reactivado correctamente.'
            ]);
    }
}
