<?php

namespace App\Http\Controllers;

use App\Models\Ethnicity;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class EthnicityController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $ethnicities = Ethnicity::where('is_active', $status === 'active' ? 1 : 0)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends(['status' => $status, 'search' => $search]);
        return view('modules.ethnicities.index', compact('ethnicities', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.ethnicities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $ethnicity = Ethnicity::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        NotificationService::notifyCreate('Etnia', $ethnicity->name);
        return redirect()->route('ethnicities.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La etnia ' . $ethnicity->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Ethnicity $ethnicity)
    {
        return view('modules.ethnicities.edit', compact('ethnicity'));
    }

    public function update(Request $request, Ethnicity $ethnicity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $ethnicity->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        NotificationService::notifyUpdate('Etnia', $ethnicity->name);
        return redirect()->route('ethnicities.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'La etnia ' . $ethnicity->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Ethnicity $ethnicity)
    {
        $ethnicityName = $ethnicity->name;
        $ethnicity->update(['is_active' => false]);

        NotificationService::notifyDelete('Etnia', $ethnicityName);

        return redirect()->route('ethnicities.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La etnia ' . $ethnicityName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $ethnicity = Ethnicity::findOrFail($id);
        $ethnicity->is_active = true;
        $ethnicity->save();
        NotificationService::notifyUpdate('Etnia', $ethnicity->name);
        return redirect()->route('ethnicities.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La etnia ' . $ethnicity->name . ' ha sido reactivada correctamente.'
            ]);
    }
} 