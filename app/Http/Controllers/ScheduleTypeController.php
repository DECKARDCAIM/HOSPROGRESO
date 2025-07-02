<?php

namespace App\Http\Controllers;

use App\Models\ScheduleType;
use App\Models\Specialty;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ScheduleTypeController extends Controller
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
        
        $scheduleTypes = ScheduleType::with('specialty')
            ->where('is_active', $status === 'active' ? 1 : 0)
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends($request->all());

        return view('modules.schedule_types.index', compact('scheduleTypes', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        return view('modules.schedule_types.create', compact('specialties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:schedule_types,name',
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_patients' => 'required|integer|min:1|max:100',
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de horario es obligatorio.',
            'name.unique' => 'Ya existe un tipo de horario con este nombre.',
            'specialty_id.required' => 'La especialidad es obligatoria.',
            'specialty_id.exists' => 'La especialidad seleccionada no es válida.',
            'days_of_week.required' => 'Debe seleccionar al menos un día.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'max_patients.required' => 'El número máximo de pacientes es obligatorio.',
            'max_patients.max' => 'El número máximo de pacientes no puede ser mayor a 100.',
        ];

        $this->validate($request, $rules, $messages);

        $data = $request->only(['name', 'specialty_id', 'start_time', 'end_time', 'max_patients']);
        $data['days_of_week'] = json_encode($request->input('days_of_week'));
        $data['is_active'] = true;
        
        $scheduleType = ScheduleType::create($data);

        NotificationService::notifyCreate('Tipo de Horario', $scheduleType->name);

        return redirect()->route('schedule-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El tipo de horario ' . $scheduleType->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScheduleType $scheduleType)
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $scheduleType->days_of_week = json_decode($scheduleType->days_of_week, true);
        return view('modules.schedule_types.edit', compact('scheduleType', 'specialties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduleType $scheduleType)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:schedule_types,name,' . $scheduleType->id,
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_patients' => 'required|integer|min:1|max:100',
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de horario es obligatorio.',
            'name.unique' => 'Ya existe un tipo de horario con este nombre.',
            'specialty_id.required' => 'La especialidad es obligatoria.',
            'specialty_id.exists' => 'La especialidad seleccionada no es válida.',
            'days_of_week.required' => 'Debe seleccionar al menos un día.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'max_patients.required' => 'El número máximo de pacientes es obligatorio.',
            'max_patients.max' => 'El número máximo de pacientes no puede ser mayor a 100.',
        ];

        $this->validate($request, $rules, $messages);

        $data = $request->only(['name', 'specialty_id', 'start_time', 'end_time', 'max_patients']);
        $data['days_of_week'] = json_encode($request->input('days_of_week'));
        
        $scheduleType->update($data);

        NotificationService::notifyUpdate('Tipo de Horario', $scheduleType->name);

        return redirect()->route('schedule-types.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El tipo de horario ' . $scheduleType->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(ScheduleType $scheduleType)
    {
        try {
            // Verificar si tiene doctores asociados
            if ($scheduleType->doctors()->count() > 0) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Error de Eliminación',
                    'message' => 'No se puede eliminar un tipo de horario que tiene doctores asociados.'
                ]);
            }

            $scheduleTypeName = $scheduleType->name;
            $scheduleType->is_active = false;
            $scheduleType->save();

            NotificationService::notifyDelete('Tipo de Horario', $scheduleTypeName);

            return redirect()->route('schedule-types.index')->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El tipo de horario ' . $scheduleTypeName . ' se ha eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al eliminar el tipo de horario: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reactivar tipo de horario inactivo.
     */
    public function reactivate($id)
    {
        $scheduleType = ScheduleType::findOrFail($id);
        $scheduleType->is_active = true;
        $scheduleType->save();

        NotificationService::notifyUpdate('Tipo de Horario', $scheduleType->name);

        return redirect()->route('schedule-types.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El tipo de horario ' . $scheduleType->name . ' ha sido reactivado correctamente.'
            ]);
    }
} 