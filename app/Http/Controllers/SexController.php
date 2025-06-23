<?php

namespace App\Http\Controllers;

use App\Models\Sex;
use Illuminate\Http\Request;

class SexController extends Controller
{
    public function index()
    {
        $sexes = Sex::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.sexes.index', compact('sexes'));
    }

    public function create()
    {
        return view('modules.sexes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:sexes,code',
        ]);

        Sex::create($request->all());

        return redirect()->route('sexes.index')
            ->with('success', 'Sexo creado exitosamente.');
    }

    public function edit(Sex $sex)
    {
        return view('modules.sexes.edit', compact('sex'));
    }

    public function update(Request $request, Sex $sex)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:sexes,code,' . $sex->id,
        ]);

        $sex->update($request->all());

        return redirect()->route('sexes.index')
            ->with('success', 'Sexo actualizado exitosamente.');
    }

    public function destroy(Sex $sex)
    {
        $sex->update(['is_active' => false]);

        return redirect()->route('sexes.index')
            ->with('success', 'Sexo eliminado exitosamente.');
    }
} 