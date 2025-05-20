<?php

namespace App\Http\Controllers;

use App\Models\municipality;
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
        'name' => 'required|string|min:5',
        'department_id' => 'required|exists:departments,id',
        'description' => 'nullable|string|max:320',
    ];

    $messages = [
        'name.required' => 'El campo nombre es obligatorio.',
        'name.string' => 'El campo nombre debe ser una cadena de texto.',
        'name.min' => 'El campo nombre debe tener al menos 5 caracteres.',

        'department_id.required' => 'El campo departamento es obligatorio.',
        'department_id.exists' => 'El departamento seleccionado no es válido.',

        'description.string' => 'El campo descripción debe ser una cadena de texto.',
        'description.max' => 'El campo descripción no puede tener más de 320 caracteres.',
    ];

    $this->validate($request, $rules, $messages);

    $municipality = new Municipality();
    $municipality->department_id = $request->department_id;
    $municipality->name = $request->name;
    $municipality->description = $request->description;
    $municipality->save();

    $notification = [
        'message' => 'El municipio ' . $municipality->name . ' se ha creado correctamente.',
        'alert-type' => 'Creación Éxitosa'
    ];

    return redirect()->route('municipios.index')->with(compact('notification'));
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
    public function update(Request $request, municipality $municipality)
    {
        $rules = [
            'name' => 'required|string|min:5',
            'description' => 'nullable|string|max:320',
        ];
        $messages = [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.min' => 'El campo nombre debe tener al menos 5 caracteres.',
            'description.string' => 'El campo descripción debe ser una cadena de texto.',
            'description.max' => 'El campo descripción no puede tener más de 320 caracteres.',
        ];
        
        $this->validate($request, $rules, $messages);

        $municipality->name = $request->input('name');
        $municipality->department_id = $request->input('department_id');
        $municipality->description = $request->input('description');
        $municipality->save();
        $notification = [
            'message' => 'El Municipio ' . $municipality->name . ' se ha actualizado correctamente.',
            'alert-type' => 'Actualización Éxitosa'
        ];
        return redirect()->route('municipios.index')->with(compact('notification'));
    }
}
