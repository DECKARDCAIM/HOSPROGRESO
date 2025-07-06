<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
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
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $country_id = $request->country_id;
        $department_id = $request->department_id;

        $countries = Country::all();
        $departments = collect();

        $municipalities = Municipality::where('is_active', $status === 'active' ? 1 : 0);

        if ($country_id) {
            $departments = Department::where('country_id', $country_id)->get();
            if ($department_id) {
                $municipalities->where('department_id', $department_id);
            } else {
                $municipalities->whereIn('department_id', $departments->pluck('id'));
            }
        }

        if ($search) {
            $municipalities->where('name', 'like', '%' . $search . '%');
        }

        $municipalities = $municipalities->paginate(25)
            ->appends(['status' => $status, 'search' => $search, 'country_id' => $country_id, 'department_id' => $department_id]);

        return view('modules.ubication.municipalities.index', compact('countries', 'departments', 'municipalities', 'status', 'search', 'country_id', 'department_id'));
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
    public function show(Municipality $municipality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Municipality $municipality)
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
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'nullable|boolean'
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
        $wasInactive = !$municipality->is_active;
        $municipality->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : $municipality->is_active;
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
        $municipality->is_active = false;
        $municipality->save();

        NotificationService::notifyDelete('Municipio', $municipalityName);

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El municipio ' . $municipalityName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $municipality = Municipality::findOrFail($id);
        $municipality->is_active = true;
        $municipality->save();
        \App\Services\NotificationService::notifyUpdate('Municipio', $municipality->name);
        return redirect()->route('municipios.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El municipio ' . $municipality->name . ' ha sido reactivado correctamente.'
            ]);
    }
}
