<?php

namespace App\Http\Controllers;

use App\Models\ContraceptiveMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContraceptiveMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');
        $type = $request->query('type', 'all');

        $page = (int) ($request->query('page', 1));
        $key = "metodos_anticonceptivos:index:v1:status={$status}:type={$type}:q=" . urlencode((string) $search) . ":p={$page}";
        $methods = Cache::tags(['metodos_anticonceptivos'])->remember($key, now()->addMinutes(10), function () use ($status, $search, $type) {
            return ContraceptiveMethod::select('id', 'name', 'description', 'type', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q
                            ->where('name', 'like', "%$search%")
                            ->orWhere('description', 'like', "%$search%");
                    });
                })
                ->when($type !== 'all', function ($query) use ($type) {
                    return $query->where('type', $type);
                })
                ->orderBy('type')
                ->orderBy('name')
                ->paginate(25);
        });
        $methods->appends($request->all());

        return view('modules.contraceptive_methods.index', compact('methods', 'status', 'search', 'type'));
    }

    public function create()
    {
        return view('modules.contraceptive_methods.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2|unique:contraceptive_methods,name',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:hormonal,barrera,natural,quirurgico,emergencia,otro'
        ];

        $messages = [
            'name.required' => 'El nombre del método anticonceptivo es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'Ya existe un método anticonceptivo con este nombre.',
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede exceder 255 caracteres.',
            'type.required' => 'El tipo de método es obligatorio.',
            'type.in' => 'El tipo de método seleccionado no es válido.'
        ];

        $request->validate($rules, $messages);

        $method = ContraceptiveMethod::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'is_active' => true
        ]);

        Cache::tags(['metodos_anticonceptivos'])->flush();

        return redirect()
            ->route('contraceptive-methods.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El método ' . $method->name . ' se ha creado correctamente.'
            ]);
    }

    public function show(ContraceptiveMethod $contraceptiveMethod)
    {
        return view('modules.contraceptive_methods.show', compact('contraceptiveMethod'));
    }

    public function edit(ContraceptiveMethod $contraceptiveMethod)
    {
        return view('modules.contraceptive_methods.edit', compact('contraceptiveMethod'));
    }

    public function update(Request $request, ContraceptiveMethod $contraceptiveMethod)
    {
        $rules = [
            'name' => 'required|min:2|unique:contraceptive_methods,name,' . $contraceptiveMethod->id,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:hormonal,barrera,natural,quirurgico,emergencia,otro'
        ];

        $messages = [
            'name.required' => 'El nombre del método anticonceptivo es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'Ya existe un método anticonceptivo con este nombre.',
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede exceder 255 caracteres.',
            'type.required' => 'El tipo de método es obligatorio.',
            'type.in' => 'El tipo de método seleccionado no es válido.'
        ];

        $request->validate($rules, $messages);

        $oldName = $contraceptiveMethod->name;

        $contraceptiveMethod->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type
        ]);

        Cache::tags(['metodos_anticonceptivos'])->flush();

        return redirect()
            ->route('contraceptive-methods.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'El método ' . $contraceptiveMethod->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(ContraceptiveMethod $contraceptiveMethod)
    {
        $methodName = $contraceptiveMethod->name;
        $contraceptiveMethod->is_active = false;
        $contraceptiveMethod->save();

        Cache::tags(['metodos_anticonceptivos'])->flush();

        return redirect()->route('contraceptive-methods.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El método ' . $methodName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $method = ContraceptiveMethod::findOrFail($id);
        $method->is_active = true;
        $method->save();

        Cache::tags(['metodos_anticonceptivos'])->flush();

        return redirect()
            ->route('contraceptive-methods.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El método ' . $method->name . ' ha sido reactivado correctamente.'
            ]);
    }
}