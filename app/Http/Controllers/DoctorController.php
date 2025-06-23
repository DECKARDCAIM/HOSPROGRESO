<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('specialty')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->paginate(10);
        return view('modules.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        return view('modules.doctors.create', compact('specialties'));
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
        ]);

        Doctor::create($request->all());

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor creado exitosamente.');
    }

    public function edit(Doctor $doctor)
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        return view('modules.doctors.edit', compact('doctor', 'specialties'));
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
        ]);

        $doctor->update($request->all());

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor actualizado exitosamente.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->update(['is_active' => false]);

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor eliminado exitosamente.');
    }
} 