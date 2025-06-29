<?php

namespace App\Http\Controllers;

use App\Models\ScheduleType;
use App\Models\Specialty;
use Illuminate\Http\Request;

class ScheduleTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $scheduleTypes = ScheduleType::with('specialty')->get();
        return view('modules.schedule_types.index', compact('scheduleTypes'));
    }

    public function create()
    {
        $specialties = Specialty::all();
        return view('modules.schedule_types.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_patients' => 'required|integer|min:1',
        ]);
        $data['days_of_week'] = json_encode($data['days_of_week']);
        ScheduleType::create($data);
        return redirect()->route('schedule-types.index')->with('success', 'Tipo de horario creado correctamente.');
    }

    public function edit(ScheduleType $scheduleType)
    {
        $specialties = Specialty::all();
        $scheduleType->days_of_week = json_decode($scheduleType->days_of_week, true);
        return view('modules.schedule_types.edit', compact('scheduleType', 'specialties'));
    }

    public function update(Request $request, ScheduleType $scheduleType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_patients' => 'required|integer|min:1',
        ]);
        $data['days_of_week'] = json_encode($data['days_of_week']);
        $scheduleType->update($data);
        return redirect()->route('schedule-types.index')->with('success', 'Tipo de horario actualizado correctamente.');
    }

    public function destroy(ScheduleType $scheduleType)
    {
        $scheduleType->delete();
        return redirect()->route('schedule-types.index')->with('success', 'Tipo de horario eliminado correctamente.');
    }
} 