<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LaboratoryTestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $key = "pruebas_laboratorio:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}";
        $laboratoryTests = Cache::tags(['pruebas_laboratorio'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return LaboratoryTest::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $laboratoryTests->appends(['status' => $status, 'search' => $search]);
        return view('modules.laboratory_tests.index', compact('laboratoryTests', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.laboratory_tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $laboratoryTest = LaboratoryTest::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        Cache::tags(['pruebas_laboratorio'])->flush();

        return redirect()
            ->route('laboratory-tests.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La prueba de laboratorio ' . $laboratoryTest->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(LaboratoryTest $laboratoryTest)
    {
        return view('modules.laboratory_tests.edit', compact('laboratoryTest'));
    }

    public function update(Request $request, LaboratoryTest $laboratoryTest)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $laboratoryTest->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Cache::tags(['pruebas_laboratorio'])->flush();

        return redirect()
            ->route('laboratory-tests.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'La prueba de laboratorio ' . $laboratoryTest->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(LaboratoryTest $laboratoryTest)
    {
        $laboratoryTestName = $laboratoryTest->name;
        $laboratoryTest->update(['is_active' => false]);

        Cache::tags(['pruebas_laboratorio'])->flush();

        return redirect()
            ->route('laboratory-tests.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La prueba de laboratorio ' . $laboratoryTestName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $laboratoryTest = LaboratoryTest::findOrFail($id);
        $laboratoryTest->is_active = true;
        $laboratoryTest->save();

        Cache::tags(['pruebas_laboratorio'])->flush();

        return redirect()
            ->route('laboratory-tests.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La prueba de laboratorio ' . $laboratoryTest->name . ' ha sido reactivada correctamente.'
            ]);
    }
}