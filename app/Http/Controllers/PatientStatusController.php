<?php

namespace App\Http\Controllers;

use App\Models\PatientStatus;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PatientStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $search = $request->get('search');

        $query = PatientStatus::query();

        // Filtrar por estado
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Filtrar por búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $patientStatuses = $query->orderBy('name')->paginate(10);

        return view('modules.patient_statuses.index', compact('patientStatuses', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.patient_statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:patient_statuses,name',
            'description' => 'nullable|string|max:200',
            'color' => 'nullable|string|max:7',
        ], [
            'name.required' => 'El nombre del estado del paciente es obligatorio.',
            'name.unique' => 'Este estado del paciente ya existe.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'description.max' => 'La descripción no puede tener más de 200 caracteres.',
            'color.max' => 'El color debe ser un código hex válido.',
        ]);

        $data = $request->all();
        $data['color'] = $data['color'] ?? '#007bff';
        
        $patientStatus = PatientStatus::create($data);

        NotificationService::notifyCreate('Estado del Paciente', $patientStatus->name);

        return redirect()->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Creado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha creado exitosamente.'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientStatus $patientStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientStatus $patientStatus)
    {
        return view('modules.patient_statuses.edit', compact('patientStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientStatus $patientStatus)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:patient_statuses,name,' . $patientStatus->id,
            'description' => 'nullable|string|max:200',
            'color' => 'nullable|string|max:7',
        ], [
            'name.required' => 'El nombre del estado del paciente es obligatorio.',
            'name.unique' => 'Este estado del paciente ya existe.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'description.max' => 'La descripción no puede tener más de 200 caracteres.',
            'color.max' => 'El color debe ser un código hex válido.',
        ]);

        $data = $request->all();
        $data['color'] = $data['color'] ?? '#007bff';
        
        $patientStatus->update($data);

        NotificationService::notifyUpdate('Estado del Paciente', $patientStatus->name);

        return redirect()->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Actualizado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha actualizado exitosamente.'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientStatus $patientStatus)
    {
        // Soft delete - cambiar is_active a false
        $patientStatus->update(['is_active' => false]);

        NotificationService::notifyDelete('Estado del Paciente', $patientStatus->name);

        return redirect()->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Desactivado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha desactivado exitosamente.'
            ]);
    }

    /**
     * Reactivate the specified resource.
     */
    public function reactivate($id)
    {
        $patientStatus = PatientStatus::findOrFail($id);
        $patientStatus->update(['is_active' => true]);

        NotificationService::notifyUpdate('Estado del Paciente', $patientStatus->name . ' - Reactivado');

        return redirect()->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Reactivado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha reactivado exitosamente.'
            ]);
    }
}