<?php

namespace App\Http\Controllers;

use App\Models\CompanionRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanionRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');

        $page = (int) ($request->query('page', 1));
        $key = "relaciones_acompanantes:index:v1:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";
        $relationships = Cache::tags(['relaciones_acompanantes'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return CompanionRelationship::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q
                            ->where('name', 'like', "%$search%")
                            ->orWhere('description', 'like', "%$search%");
                    });
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $relationships->appends($request->all());

        return view('modules.companion_relationships.index', compact('relationships', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.companion_relationships.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2|unique:companion_relationships,name',
            'description' => 'nullable|string|max:255'
        ];

        $messages = [
            'name.required' => 'El nombre de la relación es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'Ya existe una relación con este nombre.',
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede exceder 255 caracteres.'
        ];

        $request->validate($rules, $messages);

        $relationship = CompanionRelationship::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        Cache::tags(['relaciones_acompanantes'])->flush();

        return redirect()
            ->route('companion-relationships.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La relación ' . $relationship->name . ' se ha creado correctamente.'
            ]);
    }

    public function show(CompanionRelationship $companionRelationship)
    {
        return view('modules.companion_relationships.show', compact('companionRelationship'));
    }

    public function edit(CompanionRelationship $companionRelationship)
    {
        return view('modules.companion_relationships.edit', compact('companionRelationship'));
    }

    public function update(Request $request, CompanionRelationship $companionRelationship)
    {
        $rules = [
            'name' => 'required|min:2|unique:companion_relationships,name,' . $companionRelationship->id,
            'description' => 'nullable|string|max:255'
        ];

        $messages = [
            'name.required' => 'El nombre de la relación es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'Ya existe una relación con este nombre.',
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede exceder 255 caracteres.'
        ];

        $request->validate($rules, $messages);

        $oldName = $companionRelationship->name;

        $companionRelationship->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        Cache::tags(['relaciones_acompanantes'])->flush();

        return redirect()
            ->route('companion-relationships.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'La relación ' . $companionRelationship->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(CompanionRelationship $companionRelationship)
    {
        $relationshipName = $companionRelationship->name;
        $companionRelationship->is_active = false;
        $companionRelationship->save();

        Cache::tags(['relaciones_acompanantes'])->flush();
        
        return redirect()->route('companion-relationships.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'La relación ' . $relationshipName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $relationship = CompanionRelationship::findOrFail($id);
        $relationship->is_active = true;
        $relationship->save();

        Cache::tags(['relaciones_acompanantes'])->flush();

        return redirect()
            ->route('companion-relationships.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La relación ' . $relationship->name . ' ha sido reactivada correctamente.'
            ]);
    }
}