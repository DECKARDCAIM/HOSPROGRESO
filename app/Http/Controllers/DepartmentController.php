<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Country; 
use App\Services\NotificationService;
use Illuminate\Http\Request;

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
    // Cargar todos los países
    $countries = Country::all();

    // Filtrar departamentos por país si se pasa el parámetro 'country_id'
    $departments = Department::when($request->country_id, function ($query) use ($request) {
        return $query->where('country_id', $request->country_id); // Filtra los departamentos por el país
    })->get();

    return view('modules.ubication.departments.index', compact('departments', 'countries'));
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
            'country_id' => 'required|exists:countries,id'
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
        $department->save();

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
        $department->delete();

        NotificationService::notifyDelete('Departamento', $departmentName);

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El departamento ' . $departmentName . ' se ha eliminado correctamente.'
        ]);
    }
}
