<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MedicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));

        $ttl = now()->addMinutes(10);
        $version = 'v2';
        $cacheKey = "medicamentos:index:{$version}:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";

        $medications = Cache::tags(['medicamentos', 'listados'])->remember($cacheKey, $ttl, function () use ($status, $search) {
            return Medication::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->paginate(25);
        });

        $medications->appends($request->all());

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
            'is_active' => true,
        ]);

        Cache::tags(['medicamentos'])->flush();

        return redirect()->route('medications.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El medicamento ' . $medication->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(Medication $medication)
    {
        $ttl = now()->addHours(6);
        $version = 'v2';
        $cacheKey = "medicamentos:show:{$version}:{$medication->id}";

        $cached = Cache::tags(['medicamentos'])->remember($cacheKey, $ttl, function () use ($medication) {
            return $medication->only(['id', 'name', 'description', 'is_active']);
        });

        $medication->fill($cached);

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

        Cache::tags(['medicamentos'])->flush();

        return redirect()->route('medications.index')->with('toast', [
            'type' => 'success',
            'title' => 'Actualización Éxitosa',
            'message' => 'El medicamento ' . $medication->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Medication $medication)
    {
        $medicationName = $medication->name;

        $medication->update(['is_active' => false]);

        Cache::tags(['medicamentos'])->flush();

        return redirect()->route('medications.index')->with('toast', [
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

        Cache::tags(['medicamentos'])->flush();

        return redirect()->route('medications.index', ['status' => 'inactive'])->with('toast', [
            'type' => 'success',
            'title' => 'Reactivación Éxitosa',
            'message' => 'El medicamento ' . $medication->name . ' ha sido reactivado correctamente.'
        ]);
    }
}