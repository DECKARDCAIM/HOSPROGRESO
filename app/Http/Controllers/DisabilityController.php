<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use Illuminate\Http\Request;

class DisabilityController extends Controller
{
    public function index()
    {
        $disabilities = Disability::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.disabilities.index', compact('disabilities'));
    }

    public function create()
    {
        return view('modules.disabilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:disabilities,code',
        ]);

        Disability::create($request->all());

        return redirect()->route('disabilities.index')
            ->with('success', 'Discapacidad creada exitosamente.');
    }

    public function edit(Disability $disability)
    {
        return view('modules.disabilities.edit', compact('disability'));
    }

    public function update(Request $request, Disability $disability)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:disabilities,code,' . $disability->id,
        ]);

        $disability->update($request->all());

        return redirect()->route('disabilities.index')
            ->with('success', 'Discapacidad actualizada exitosamente.');
    }

    public function destroy(Disability $disability)
    {
        $disability->update(['is_active' => false]);

        return redirect()->route('disabilities.index')
            ->with('success', 'Discapacidad eliminada exitosamente.');
    }
} 