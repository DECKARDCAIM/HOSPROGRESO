<?php

namespace App\Http\Controllers;

use App\Models\municipality;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Country;


class MunicipalityController extends Controller
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
    $countries = Country::all();
    $departments = collect();
    $municipalities = Municipality::query();

    // Si hay país seleccionado, filtra los departamentos
    if ($request->filled('country_id')) {
        $departments = Department::where('country_id', $request->country_id)->get();

        // Si además hay departamento, filtra por él
        if ($request->filled('department_id')) {
            $municipalities->where('department_id', $request->department_id);
        } else {
            // Filtra por todos los departamentos de ese país
            $municipalities->whereIn('department_id', $departments->pluck('id'));
        }
    }

    $municipalities = $municipalities->get();

    return view('modules.ubication.municipalities.index', compact('countries', 'departments', 'municipalities'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $countries = Country::all();

        $departments = collect(); // vacío por defecto

        if ($request->has('country_id') && $request->country_id) {
            $departments = Department::where('country_id', $request->country_id)->get();
        }
        return view('modules.ubication.municipalities.create', compact('departments', 'countries'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ];
        $messages = [
            'name.required' => 'El nombre del municipio es obligatorio.',
            'name.min' => 'El nombre del municipio debe tener más de 3 caracteres.',
            'department_id.required' => 'Debe seleccionar un departamento.',
            'department_id.exists' => 'El departamento seleccionado no es válido.'
        ];
        $this->validate($request, $rules, $messages);

        $municipality = new Municipality();
        $municipality->name = $request->input('name');
        $municipality->description = $request->input('description');
        $municipality->department_id = $request->input('department_id');
        $municipality->save();

        NotificationService::notifyCreate('Municipio', $municipality->name);

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El municipio ' . $municipality->name . ' se ha creado correctamente.'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(municipality $municipality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(municipality $municipality)
    {
        $departments = Department::all();
        $countries = Country::all(); // Carga todos los países desde la tabla `countries`
        return view('modules.ubication.municipalities.edit', compact('municipality', 'departments', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Municipality $municipality)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ];
        $messages = [
            'name.required' => 'El nombre del municipio es obligatorio.',
            'name.min' => 'El nombre del municipio debe tener más de 3 caracteres.',
            'department_id.required' => 'Debe seleccionar un departamento.',
            'department_id.exists' => 'El departamento seleccionado no es válido.'
        ];
        $this->validate($request, $rules, $messages);

        $municipality->name = $request->input('name');
        $municipality->description = $request->input('description');
        $municipality->department_id = $request->input('department_id');
        $municipality->save();

        NotificationService::notifyUpdate('Municipio', $municipality->name);

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El municipio ' . $municipality->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipality $municipality)
    {
        $municipalityName = $municipality->name;
        $municipality->delete();

        NotificationService::notifyDelete('Municipio', $municipalityName);

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El municipio ' . $municipalityName . ' se ha eliminado correctamente.'
        ]);
    }
}
