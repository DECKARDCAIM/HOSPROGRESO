<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AllergyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $allergies = Allergy::where('is_active', $status === 'active' ? 1 : 0)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends(['status' => $status, 'search' => $search]);
        return view('modules.allergies.index', compact('allergies', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.allergies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $allergy = Allergy::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        NotificationService::notifyCreate('Alergia', $allergy->name);
        return redirect()->route('allergies.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La alergia ' . $allergy->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Allergy $allergy)
    {
        return view('modules.allergies.edit', compact('allergy'));
    }

    public function update(Request $request, Allergy $allergy)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $allergy->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        NotificationService::notifyUpdate('Alergia', $allergy->name);
        return redirect()->route('allergies.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'La alergia ' . $allergy->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Allergy $allergy)
    {
        $allergyName = $allergy->name;
        $allergy->update(['is_active' => false]);

        NotificationService::notifyDelete('Alergia', $allergyName);

        return redirect()->route('allergies.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La alergia ' . $allergyName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $allergy = Allergy::findOrFail($id);
        $allergy->is_active = true;
        $allergy->save();
        NotificationService::notifyUpdate('Alergia', $allergy->name);
        return redirect()->route('allergies.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La alergia ' . $allergy->name . ' ha sido reactivada correctamente.'
            ]);
    }
} 