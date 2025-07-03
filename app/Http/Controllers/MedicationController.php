<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $medications = Medication::where('is_active', $status === 'active' ? 1 : 0)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends(['status' => $status, 'search' => $search]);
        return view('modules.medications.index', compact('medications', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.medications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $medication = Medication::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        NotificationService::notifyCreate('Medicamento', $medication->name);
        return redirect()->route('medications.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El medicamento ' . $medication->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Medication $medication)
    {
        return view('modules.medications.edit', compact('medication'));
    }

    public function update(Request $request, Medication $medication)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $medication->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        NotificationService::notifyUpdate('Medicamento', $medication->name);
        return redirect()->route('medications.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'El medicamento ' . $medication->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Medication $medication)
    {
        $medicationName = $medication->name;
        $medication->update(['is_active' => false]);

        NotificationService::notifyDelete('Medicamento', $medicationName);

        return redirect()->route('medications.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El medicamento ' . $medicationName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $medication = Medication::findOrFail($id);
        $medication->is_active = true;
        $medication->save();
        NotificationService::notifyUpdate('Medicamento', $medication->name);
        return redirect()->route('medications.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El medicamento ' . $medication->name . ' ha sido reactivado correctamente.'
            ]);
    }
} 