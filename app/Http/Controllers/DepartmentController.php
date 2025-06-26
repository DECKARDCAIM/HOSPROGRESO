<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Country; 
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Municipality;

class DepartmentController extends Controller
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
        $search = $request->query('search', '');
        $country_id = $request->country_id;

        $countries = Country::all();

        $departments = Department::where('is_active', $status === 'active' ? 1 : 0)
            ->when($country_id, function ($query) use ($country_id) {
                return $query->where('country_id', $country_id);
            })
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(25)
            ->appends(['status' => $status, 'search' => $search, 'country_id' => $country_id]);

        return view('modules.ubication.departments.index', compact('departments', 'countries', 'status', 'search', 'country_id'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all(); // Carga todos los países desde la tabla `countries`
        return view('modules.ubication.departments.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id'
        ];
        $messages = [
            'name.required' => 'El nombre del departamento es obligatorio.',
            'name.min' => 'El nombre del departamento debe tener más de 3 caracteres.',
            'country_id.required' => 'Debe seleccionar un país.',
            'country_id.exists' => 'El país seleccionado no es válido.'
        ];
        $this->validate($request, $rules, $messages);

        $departments = new Department();
        $departments->name = $request->input('name');
        $departments->description = $request->input('description');
        $departments->country_id = $request->input('country_id');
        $departments->save();

        NotificationService::notifyCreate('Departamento', $departments->name);

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El departamento ' . $departments->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $countries = Country::all(); // Carga todos los países desde la tabla `countries`
        return view('modules.ubication.departments.edit', compact('department', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'nullable|boolean'
        ];
        $messages = [
            'name.required' => 'El nombre del departamento es obligatorio.',
            'name.min' => 'El nombre del departamento debe tener más de 3 caracteres.',
            'country_id.required' => 'Debe seleccionar un país.',
            'country_id.exists' => 'El país seleccionado no es válido.'
        ];
        $this->validate($request, $rules, $messages);

        $department->name = $request->input('name');
        $department->description = $request->input('description');
        $department->country_id = $request->input('country_id');
        $wasInactive = !$department->is_active;
        $department->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : $department->is_active;
        $department->save();

        // Reactivar en cascada si se activa
        if ($department->is_active && $wasInactive) {
            $municipalities = Municipality::where('department_id', $department->id)->get();
            foreach ($municipalities as $municipality) {
                $municipality->is_active = true;
                $municipality->save();
            }
        }

        NotificationService::notifyUpdate('Departamento', $department->name);

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El departamento ' . $department->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $departmentName = $department->name;
        $department->is_active = false;
        $department->save();

        // Eliminar lógicamente en cascada
        $municipalities = Municipality::where('department_id', $department->id)->get();
        foreach ($municipalities as $municipality) {
            $municipality->is_active = false;
            $municipality->save();
        }

        NotificationService::notifyDelete('Departamento', $departmentName);

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El departamento ' . $departmentName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $department = Department::findOrFail($id);
        $department->is_active = true;
        $department->save();
        // Reactivar municipios en cascada
        $municipalities = \App\Models\Municipality::where('department_id', $department->id)->get();
        foreach ($municipalities as $municipality) {
            $municipality->is_active = true;
            $municipality->save();
        }
        \App\Services\NotificationService::notifyUpdate('Departamento', $department->name);
        return redirect()->route('departamentos.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El departamento ' . $department->name . ' y sus municipios han sido reactivados correctamente.'
            ]);
    }
}
