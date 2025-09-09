<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AllergyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $key = "alergias:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}";
        $allergies = Cache::tags(['alergias'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return Allergy::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $allergies->appends(['status' => $status, 'search' => $search]);
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

        Cache::tags(['alergias'])->flush();

        return redirect()
            ->route('allergies.index')
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

        Cache::tags(['alergias'])->flush();

        return redirect()
            ->route('allergies.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'La alergia ' . $allergy->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Allergy $allergy)
    {
        $allergyName = $allergy->name;
        $allergy->update(['is_active' => false]);

        Cache::tags(['alergias'])->flush();

        return redirect()
            ->route('allergies.index')
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

        Cache::tags(['alergias'])->flush();

        return redirect()
            ->route('allergies.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La alergia ' . $allergy->name . ' ha sido reactivada correctamente.'
            ]);
    }
}