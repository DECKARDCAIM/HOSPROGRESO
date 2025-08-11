<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\ScheduleType;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));

        $ttl = now()->addMinutes(10);
        $cacheKey = "doctores:index:v1:status={$status}:q=".urlencode((string) $search).":p={$page}";
        $doctors = Cache::tags(['doctores','listados'])->remember($cacheKey, $ttl, function () use ($status, $search) {
            return Doctor::with(['specialty:id,name'])
                ->select('id','first_name','second_name','third_name','first_lastname','second_lastname','married_lastname','cui','license_number','specialty_id','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                          ->orWhere('second_name', 'like', "%$search%")
                          ->orWhere('third_name', 'like', "%$search%")
                          ->orWhere('first_lastname', 'like', "%$search%")
                          ->orWhere('second_lastname', 'like', "%$search%")
                          ->orWhere('married_lastname', 'like', "%$search%")
                          ->orWhere('cui', 'like', "%$search%")
                          ->orWhere('license_number', 'like', "%$search%")
                          ->orWhereHas('specialty', function($q2) use ($search) {
                              $q2->where('name', 'like', "%$search%") ;
                          });
                    });
                })
                ->orderBy('first_name')
                ->paginate(25);
        });

        $doctors->appends(['status' => $status, 'search' => $search]);

        return view('modules.doctors.index', compact('doctors', 'status', 'search'));
    }

    public function create()
    {
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), function () {
            return Specialty::where('is_active', true)->orderBy('name')->get(['id','name']);
        });
        $scheduleTypes = Cache::tags(['tipos_horario','catalogos'])->remember('schedule-types:select:v1', now()->addHours(12), function () {
            return ScheduleType::with('specialty:id,name')->orderBy('name')->get(['id','name','specialty_id']);
        });
        return view('modules.doctors.create', compact('specialties', 'scheduleTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'first_lastname' => 'required|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'married_lastname' => 'nullable|string|max:255',
            'cui' => 'required|string|max:13|unique:doctors,cui',
            'license_number' => 'required|string|max:255|unique:doctors,license_number',
            'specialty_id' => 'required|exists:specialties,id',
            'schedule_type_id' => 'required|exists:schedule_types,id',
        ]);

        $doctor = Doctor::create($request->all());
        NotificationService::notifyCreate('Doctor', $doctor->first_name . ' ' . $doctor->first_lastname);

        return redirect()->route('doctors.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El doctor ' . $doctor->first_name . ' ' . $doctor->first_lastname . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Doctor $doctor)
    {
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), function () {
            return Specialty::where('is_active', true)->orderBy('name')->get(['id','name']);
        });
        $scheduleTypes = Cache::tags(['tipos_horario','catalogos'])->remember('schedule-types:select:v1', now()->addHours(12), function () {
            return ScheduleType::with('specialty:id,name')->orderBy('name')->get(['id','name','specialty_id']);
        });
        return view('modules.doctors.edit', compact('doctor', 'specialties', 'scheduleTypes'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'first_lastname' => 'required|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'married_lastname' => 'nullable|string|max:255',
            'cui' => 'required|string|max:13|unique:doctors,cui,' . $doctor->id,
            'license_number' => 'required|string|max:255|unique:doctors,license_number,' . $doctor->id,
            'specialty_id' => 'required|exists:specialties,id',
            'schedule_type_id' => 'required|exists:schedule_types,id',
        ]);

        $doctor->update($request->all());
        NotificationService::notifyUpdate('Doctor', $doctor->first_name . ' ' . $doctor->first_lastname);

        return redirect()->route('doctors.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'El doctor ' . $doctor->first_name . ' ' . $doctor->first_lastname . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->update(['is_active' => false]);
        NotificationService::notifyDelete('Doctor', $doctor->first_name . ' ' . $doctor->first_lastname);

        return redirect()->route('doctors.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El doctor ' . $doctor->first_name . ' ' . $doctor->first_lastname . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->is_active = true;
        $doctor->save();
        NotificationService::notifyUpdate('Doctor', $doctor->first_name . ' ' . $doctor->first_lastname);
        return redirect()->route('doctors.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El doctor ' . $doctor->first_name . ' ' . $doctor->first_lastname . ' ha sido reactivado correctamente.'
            ]);
    }
} 