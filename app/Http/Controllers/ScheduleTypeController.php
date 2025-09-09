<?php

namespace App\Http\Controllers;

use App\Models\ScheduleType;
use App\Models\Specialty;
use App\Rules\NightShiftTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScheduleTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');

        $page = (int) ($request->query('page', 1));
        $key = "tipos_horario:index:v1:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";
        $scheduleTypes = Cache::tags(['tipos_horario'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return ScheduleType::with('specialty:id,name')
                ->select('id', 'name', 'specialty_id', 'days_of_week', 'start_time', 'end_time', 'max_patients', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $scheduleTypes->appends($request->all());

        return view('modules.schedule_types.index', compact('scheduleTypes', 'status', 'search'));
    }

    public function create()
    {
        $specialties = Cache::tags(['especialidades'])->remember('especialidades:select:v2', now()->addHours(12), fn() => Specialty::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        return view('modules.schedule_types.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => ['required', new NightShiftTime($request->input('start_time'))],
            'max_patients' => 'required|integer|min:1|max:100',
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de horario es obligatorio.',
            'specialty_id.required' => 'La especialidad es obligatoria.',
            'specialty_id.exists' => 'La especialidad seleccionada no es válida.',
            'days_of_week.required' => 'Debe seleccionar al menos un día.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio, o para turnos nocturnos que crucen la medianoche, debe ser válida.',
            'max_patients.required' => 'El número máximo de pacientes es obligatorio.',
            'max_patients.max' => 'El número máximo de pacientes no puede ser mayor a 100.',
        ];

        $this->validate($request, $rules, $messages);

        $data = $request->only(['name', 'specialty_id', 'start_time', 'end_time', 'max_patients']);
        $data['days_of_week'] = json_encode($request->input('days_of_week'));
        $data['is_active'] = true;

        $scheduleType = ScheduleType::create($data);

        Cache::tags(['tipos_horario'])->flush();

        return redirect()->route('schedule-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El tipo de horario ' . $scheduleType->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(ScheduleType $scheduleType)
    {
        $specialties = Cache::tags(['especialidades'])->remember('especialidades:select:v2', now()->addHours(12), fn() => Specialty::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $scheduleType->days_of_week = json_decode($scheduleType->days_of_week, true);
        return view('modules.schedule_types.edit', compact('scheduleType', 'specialties'));
    }

    public function update(Request $request, ScheduleType $scheduleType)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => ['required', new NightShiftTime($request->input('start_time'))],
            'max_patients' => 'required|integer|min:1|max:100',
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de horario es obligatorio.',
            'specialty_id.required' => 'La especialidad es obligatoria.',
            'specialty_id.exists' => 'La especialidad seleccionada no es válida.',
            'days_of_week.required' => 'Debe seleccionar al menos un día.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio, o para turnos nocturnos que crucen la medianoche, debe ser válida.',
            'max_patients.required' => 'El número máximo de pacientes es obligatorio.',
            'max_patients.max' => 'El número máximo de pacientes no puede ser mayor a 100.',
        ];

        $this->validate($request, $rules, $messages);

        $data = $request->only(['name', 'specialty_id', 'start_time', 'end_time', 'max_patients']);
        $data['days_of_week'] = json_encode($request->input('days_of_week'));

        $scheduleType->update($data);

        Cache::tags(['tipos_horario'])->flush();

        return redirect()->route('schedule-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Actualización Éxitosa',
            'message' => 'El tipo de horario ' . $scheduleType->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function getBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
        
        if (!$specialtyId) {
            return response()->json([]);
        }

        $scheduleTypes = ScheduleType::where('specialty_id', $specialtyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'start_time', 'end_time', 'max_patients']);

        return response()->json($scheduleTypes);
    }

    public function destroy(ScheduleType $scheduleType)
    {
        try {
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

            Cache::tags(['tipos_horario'])->flush();

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

    public function reactivate($id)
    {
        $scheduleType = ScheduleType::findOrFail($id);
        $scheduleType->is_active = true;
        $scheduleType->save();

        Cache::tags(['tipos_horario'])->flush();

        return redirect()
            ->route('schedule-types.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El tipo de horario ' . $scheduleType->name . ' ha sido reactivado correctamente.'
            ]);
    }
}