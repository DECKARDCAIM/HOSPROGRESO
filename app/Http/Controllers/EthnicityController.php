<?php

namespace App\Http\Controllers;

use App\Models\Ethnicity;
use Illuminate\Http\Request;

class EthnicityController extends Controller
{
    public function index()
    {
        $ethnicities = Ethnicity::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.ethnicities.index', compact('ethnicities'));
    }

    public function create()
    {
        return view('modules.ethnicities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:ethnicities,code',
        ]);

        Ethnicity::create($request->all());

        return redirect()->route('ethnicities.index')
            ->with('success', 'Etnia creada exitosamente.');
    }

    public function edit(Ethnicity $ethnicity)
    {
        return view('modules.ethnicities.edit', compact('ethnicity'));
    }

    public function update(Request $request, Ethnicity $ethnicity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:ethnicities,code,' . $ethnicity->id,
        ]);

        $ethnicity->update($request->all());

        return redirect()->route('ethnicities.index')
            ->with('success', 'Etnia actualizada exitosamente.');
    }

    public function destroy(Ethnicity $ethnicity)
    {
        $ethnicity->update(['is_active' => false]);

        return redirect()->route('ethnicities.index')
            ->with('success', 'Etnia eliminada exitosamente.');
    }
} 