<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Country; 
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
            'name' => 'required|string|min:5',
            'country_id' => 'required|exists:countries,id', // Asegúrate de que el país existe
            'description' => 'nullable|string|max:320',
        ];
        $messages = [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'country_id.required' => 'El campo país es obligatorio.',
            'name.min' => 'El campo nombre debe tener al menos 5 caracteres.',
            'description.string' => 'El campo descripción debe ser una cadena de texto.',
            'description.max' => 'El campo descripción no puede tener más de 320 caracteres.',
        ];
        
        $this->validate($request, $rules, $messages);

        $departments = new department();
        $departments->country_id = $request->input('country_id'); // Asigna el ID del país seleccionado
        $departments->name = $request->input('name');
        $departments->description = $request->input('description');
        $departments->save();
        $notification = [
            'message' => 'El departamento ' . $departments->name . ' se ha creado correctamente.',
            'alert-type' => 'Creación Éxitosa'
        ];
        return redirect()->route('departamentos.index')->with(compact('notification'));
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
            'name' => 'required|string|min:5',
            'country_id' => 'required|exists:countries,id', // Asegúrate de que el país existe
            'description' => 'nullable|string|max:320',
        ];
        $messages = [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'country_id.required' => 'El campo país es obligatorio.',
            'name.min' => 'El campo nombre debe tener al menos 5 caracteres.',
            'description.string' => 'El campo descripción debe ser una cadena de texto.',
            'description.max' => 'El campo descripción no puede tener más de 320 caracteres.',
        ];
        
        $this->validate($request, $rules, $messages);

        $department->name = $request->input('name');
        $department->country_id = $request->input('country_id'); // Asigna el ID del país seleccionado
        $department->description = $request->input('description');
        $department->save();
        $notification = [
            'message' => 'El departamento ' . $department->name . ' se ha actualizado correctamente.',
            'alert-type' => 'Actualización Éxitosa'
        ];
        return redirect()->route('departamentos.index')->with(compact('notification'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        $notification = [
            'message' => 'El departamento ' . $department->name . ' se ha eliminado correctamente.',
            'alert-type' => 'Eliminación Éxitosa'
        ];

        return redirect()->route('departamentos.index')->with(compact('notification'));
    }
}
