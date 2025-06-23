<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryTest;
use Illuminate\Http\Request;

class LaboratoryTestController extends Controller
{
    public function index()
    {
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.laboratory_tests.index', compact('laboratoryTests'));
    }

    public function create()
    {
        return view('modules.laboratory_tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:laboratory_tests,code',
        ]);

        LaboratoryTest::create($request->all());

        return redirect()->route('laboratory-tests.index')
            ->with('success', 'Prueba de laboratorio creada exitosamente.');
    }

    public function edit(LaboratoryTest $laboratoryTest)
    {
        return view('modules.laboratory_tests.edit', compact('laboratoryTest'));
    }

    public function update(Request $request, LaboratoryTest $laboratoryTest)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:laboratory_tests,code,' . $laboratoryTest->id,
        ]);

        $laboratoryTest->update($request->all());

        return redirect()->route('laboratory-tests.index')
            ->with('success', 'Prueba de laboratorio actualizada exitosamente.');
    }

    public function destroy(LaboratoryTest $laboratoryTest)
    {
        $laboratoryTest->update(['is_active' => false]);

        return redirect()->route('laboratory-tests.index')
            ->with('success', 'Prueba de laboratorio eliminada exitosamente.');
    }
} 