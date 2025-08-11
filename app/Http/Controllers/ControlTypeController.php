<?php

namespace App\Http\Controllers;

use App\Models\ControlType;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ControlTypeController extends Controller
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
        $key = "tipos_control:index:v1:status={$status}:q=".urlencode((string)$search).":p={$page}";
        $controlTypes = Cache::tags(['tipos_control','listados'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return ControlType::select('id','name','description','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $controlTypes->appends($request->all());

        return view('modules.control_types.index', compact('controlTypes', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.control_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:control_types,name',
            'description' => 'nullable|string|max:255'
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de control es obligatorio.',
            'name.unique' => 'Ya existe un tipo de control con este nombre.'
        ];

        $this->validate($request, $rules, $messages);

        $controlType = new ControlType();
        $controlType->name = $request->input('name');
        $controlType->description = $request->input('description');
        $controlType->is_active = true;
        $controlType->save();

        NotificationService::notifyCreate('Tipo de Control', $controlType->name);

        return redirect()->route('control-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El tipo de control ' . $controlType->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ControlType $controlType)
    {
        return view('modules.control_types.show', compact('controlType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ControlType $controlType)
    {
        return view('modules.control_types.edit', compact('controlType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ControlType $controlType)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:control_types,name,' . $controlType->id,
            'description' => 'nullable|string|max:255'
        ];

        $messages = [
            'name.required' => 'El nombre del tipo de control es obligatorio.',
            'name.unique' => 'Ya existe un tipo de control con este nombre.'
        ];

        $this->validate($request, $rules, $messages);

        $controlType->name = $request->input('name');
        $controlType->description = $request->input('description');
        $controlType->save();

        NotificationService::notifyUpdate('Tipo de Control', $controlType->name);

        return redirect()->route('control-types.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El tipo de control ' . $controlType->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(ControlType $controlType)
    {
        try {
            // Verificar si tiene consultas asociadas
            if ($controlType->medicalConsultations()->count() > 0) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Error de Eliminación',
                    'message' => 'No se puede eliminar un tipo de control que tiene consultas médicas asociadas.'
                ]);
            }

            $controlTypeName = $controlType->name;
            $controlType->is_active = false;
            $controlType->save();

            NotificationService::notifyDelete('Tipo de Control', $controlTypeName);

            return redirect()->route('control-types.index')->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El tipo de control ' . $controlTypeName . ' se ha eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al eliminar el tipo de control: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reactivar tipo de control inactivo.
     */
    public function reactivate($id)
    {
        $controlType = ControlType::findOrFail($id);
        $controlType->is_active = true;
        $controlType->save();

        NotificationService::notifyUpdate('Tipo de Control', $controlType->name);

        return redirect()->route('control-types.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El tipo de control ' . $controlType->name . ' ha sido reactivado correctamente.'
            ]);
    }
}
