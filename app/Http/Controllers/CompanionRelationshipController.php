<?php

namespace App\Http\Controllers;

use App\Models\CompanionRelationship;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanionRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');
        
        $page = (int) ($request->query('page', 1));
        $key = "relaciones_acompanantes:index:v1:status={$status}:q=".urlencode((string)$search).":p={$page}";
        $relationships = Cache::tags(['relaciones_acompanantes','listados'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return CompanionRelationship::select('id','name','description','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                          ->orWhere('description', 'like', "%$search%");
                    });
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $relationships->appends($request->all());
            
        return view('modules.companion_relationships.index', compact('relationships', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.companion_relationships.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        NotificationService::notifyCreate('Relación de Acompañante', $relationship->name);

        return redirect()->route('companion-relationships.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La relación ' . $relationship->name . ' se ha creado correctamente.'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanionRelationship $companionRelationship)
    {
        return view('modules.companion_relationships.show', compact('companionRelationship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanionRelationship $companionRelationship)
    {
        return view('modules.companion_relationships.edit', compact('companionRelationship'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        NotificationService::notifyUpdate('Relación de Acompañante', $oldName . ' → ' . $companionRelationship->name);

        return redirect()->route('companion-relationships.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'La relación ' . $companionRelationship->name . ' se ha actualizado correctamente.'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanionRelationship $companionRelationship)
    {
        $relationshipName = $companionRelationship->name;
        $companionRelationship->is_active = false;
        $companionRelationship->save();
        NotificationService::notifyDelete('Relación de Acompañante', $relationshipName);
        return redirect()->route('companion-relationships.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'La relación ' . $relationshipName . ' se ha eliminado correctamente.'
        ]);
    }

    /**
     * Reactivar relación inactiva.
     */
    public function reactivate($id)
    {
        $relationship = CompanionRelationship::findOrFail($id);
        $relationship->is_active = true;
        $relationship->save();
        NotificationService::notifyUpdate('Relación de Acompañante', $relationship->name);
        return redirect()->route('companion-relationships.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La relación ' . $relationship->name . ' ha sido reactivada correctamente.'
            ]);
    }
}