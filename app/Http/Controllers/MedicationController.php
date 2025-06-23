<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index()
    {
        $medications = Medication::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.medications.index', compact('medications'));
    }

    public function create()
    {
        return view('modules.medications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:medications,code',
        ]);

        Medication::create($request->all());

        return redirect()->route('medications.index')
            ->with('success', 'Medicamento creado exitosamente.');
    }

    public function edit(Medication $medication)
    {
        return view('modules.medications.edit', compact('medication'));
    }

    public function update(Request $request, Medication $medication)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:medications,code,' . $medication->id,
        ]);

        $medication->update($request->all());

        return redirect()->route('medications.index')
            ->with('success', 'Medicamento actualizado exitosamente.');
    }

    public function destroy(Medication $medication)
    {
        $medication->update(['is_active' => false]);

        return redirect()->route('medications.index')
            ->with('success', 'Medicamento eliminado exitosamente.');
    }
} 