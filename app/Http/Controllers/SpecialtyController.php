<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Specialty as ModelsSpecialty;
use Illuminate\Support\Facades\Cache;

class SpecialtyController extends Controller
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

        $ttl = now()->addMinutes(10);
        $cacheKey = "especialidades:index:v2:status={$status}:q=".urlencode((string) $search).":p={$page}";

        $specialties = Cache::tags(['especialidades','listados'])->remember($cacheKey, $ttl, function () use ($status, $search) {
            return Specialty::select('id','name','description','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });

        $specialties->appends($request->all());
        return view('modules.specialties.index', compact('specialties', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.specialties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255'
        ];
        $messages = [
            'name.required' => 'El nombre de la especialidad es obligatorio.',
            'name.min' => 'El nombre de la especialidad debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        NotificationService::notifyCreate('Especialidad', $specialty->name);

        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'La especialidad ' . $specialty->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialty $specialty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialty $specialty)
    {
        $ttl = now()->addHours(6);
        $cacheKey = "especialidades:show:v2:{$specialty->id}";
        $cached = Cache::tags(['especialidades'])->remember($cacheKey, $ttl, fn() => $specialty->only(['id','name','description','is_active']));
        // Reconstruir el modelo mínimo para la vista
        $specialty->fill($cached);
        return view('modules.specialties.edit', compact('specialty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialty $specialty)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255'
        ];
        $messages = [
            'name.required' => 'El nombre de la especialidad es obligatorio.',
            'name.min' => 'El nombre de la especialidad debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        NotificationService::notifyUpdate('Especialidad', $specialty->name);

        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'La especialidad ' . $specialty->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Specialty $specialty)
    {
        $specialtyName = $specialty->name;
        $specialty->is_active = false;
        $specialty->save();
        NotificationService::notifyDelete('Especialidad', $specialtyName);
        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'La especialidad ' . $specialtyName . ' se ha eliminado correctamente.'
        ]);
    }

    /**
     * Reactivar especialidad inactiva.
     */
    public function reactivate($id)
    {
        $specialty = Specialty::findOrFail($id);
        $specialty->is_active = true;
        $specialty->save();
        NotificationService::notifyUpdate('Especialidad', $specialty->name);
        return redirect()->route('especialidades.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La especialidad ' . $specialty->name . ' ha sido reactivada correctamente.'
            ]);
    }
}
