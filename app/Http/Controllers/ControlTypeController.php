<?php

namespace App\Http\Controllers;

use App\Models\ControlType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ControlTypeController extends Controller
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
        $key = "tipos_control:index:v1:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";
        $controlTypes = Cache::tags(['tipos_control'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return ControlType::select('id', 'name', 'description', 'is_active')
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

    public function create()
    {
        return view('modules.control_types.create');
    }

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

        Cache::tags(['tipos_control'])->flush();

        return redirect()->route('control-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El tipo de control ' . $controlType->name . ' se ha creado correctamente.'
        ]);
    }

    public function show(ControlType $controlType)
    {
        return view('modules.control_types.show', compact('controlType'));
    }

    public function edit(ControlType $controlType)
    {
        return view('modules.control_types.edit', compact('controlType'));
    }

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

        Cache::tags(['tipos_control'])->flush();

        return redirect()->route('control-types.index')->with('toast', [
            'type' => 'success',
            'title' => 'Actualización Éxitosa',
            'message' => 'El tipo de control ' . $controlType->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(ControlType $controlType)
    {
        try {
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

            Cache::tags(['tipos_control'])->flush();

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

    public function reactivate($id)
    {
        $controlType = ControlType::findOrFail($id);
        $controlType->is_active = true;
        $controlType->save();

        Cache::tags(['tipos_control'])->flush();

        return redirect()
            ->route('control-types.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El tipo de control ' . $controlType->name . ' ha sido reactivado correctamente.'
            ]);
    }
}