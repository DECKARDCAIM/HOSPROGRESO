<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use Illuminate\Http\Request;

class AllergyController extends Controller
{
    public function index()
    {
        $allergies = Allergy::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.allergies.index', compact('allergies'));
    }

    public function create()
    {
        return view('modules.allergies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:allergies,code',
        ]);

        Allergy::create($request->all());

        return redirect()->route('allergies.index')
            ->with('success', 'Alergia creada exitosamente.');
    }

    public function edit(Allergy $allergy)
    {
        return view('modules.allergies.edit', compact('allergy'));
    }

    public function update(Request $request, Allergy $allergy)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:allergies,code,' . $allergy->id,
        ]);

        $allergy->update($request->all());

        return redirect()->route('allergies.index')
            ->with('success', 'Alergia actualizada exitosamente.');
    }

    public function destroy(Allergy $allergy)
    {
        $allergy->update(['is_active' => false]);

        return redirect()->route('allergies.index')
            ->with('success', 'Alergia eliminada exitosamente.');
    }
} 