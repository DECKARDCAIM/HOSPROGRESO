<?php

namespace App\Http\Controllers;

use App\Models\PatientStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientStatusController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $search = $request->get('search');

        $query = PatientStatus::query();

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $page = (int) ($request->query('page', 1));
        $cacheKey = "estados_paciente:index:v1:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";
        $patientStatuses = Cache::tags(['estados_paciente'])->remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            return $query->orderBy('name')->paginate(10);
        });

        return view('modules.patient_statuses.index', compact('patientStatuses', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.patient_statuses.create');
    }

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

        Cache::tags(['estados_paciente'])->flush();

        return redirect()
            ->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Creado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha creado exitosamente.'
            ]);
    }

    public function edit(PatientStatus $patientStatus)
    {
        return view('modules.patient_statuses.edit', compact('patientStatus'));
    }

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

        Cache::tags(['estados_paciente'])->flush();

        return redirect()
            ->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Actualizado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha actualizado exitosamente.'
            ]);
    }

    public function destroy(PatientStatus $patientStatus)
    {
        $patientStatus->update(['is_active' => false]);

        Cache::tags(['estados_paciente'])->flush();

        return redirect()
            ->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Desactivado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha desactivado exitosamente.'
            ]);
    }

    public function reactivate($id)
    {
        $patientStatus = PatientStatus::findOrFail($id);
        $patientStatus->update(['is_active' => true]);

        Cache::tags(['estados_paciente'])->flush();

        return redirect()
            ->route('patient-statuses.index')
            ->with('success', [
                'title' => 'Estado del Paciente Reactivado',
                'message' => 'El estado "' . $patientStatus->name . '" se ha reactivado exitosamente.'
            ]);
    }
}