<?php

namespace App\Http\Controllers;

use App\Models\CivilStatus;
use Illuminate\Http\Request;

class CivilStatusController extends Controller
{
    public function index()
    {
        $civilStatuses = CivilStatus::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.civil_statuses.index', compact('civilStatuses'));
    }

    public function create()
    {
        return view('modules.civil_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:civil_statuses,code',
        ]);

        CivilStatus::create($request->all());

        return redirect()->route('civil-statuses.index')
            ->with('success', 'Estado civil creado exitosamente.');
    }

    public function edit(CivilStatus $civilStatus)
    {
        return view('modules.civil_statuses.edit', compact('civilStatus'));
    }

    public function update(Request $request, CivilStatus $civilStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:civil_statuses,code,' . $civilStatus->id,
        ]);

        $civilStatus->update($request->all());

        return redirect()->route('civil-statuses.index')
            ->with('success', 'Estado civil actualizado exitosamente.');
    }

    public function destroy(CivilStatus $civilStatus)
    {
        $civilStatus->update(['is_active' => false]);

        return redirect()->route('civil-statuses.index')
            ->with('success', 'Estado civil eliminado exitosamente.');
    }
} 