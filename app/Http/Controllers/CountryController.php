<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Country as ModelsCountry;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return view('modules.ubication.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.ubication.countries.create');
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
            'name.required' => 'El nombre del pais es obligatorio.',
            'name.min' => 'El nombre del pais debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $countries = new Country();
        $countries->name = $request->input('name');
        $countries->description = $request->input('description');
        $countries->save();

        NotificationService::notifyCreate('País', $countries->name);

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El país ' . $countries->name . ' se ha creado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('modules.ubication.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255'
        ];
        $messages = [
            'name.required' => 'El nombre del pais es obligatorio.',
            'name.min' => 'El nombre del pais debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $country->name = $request->input('name');
        $country->description = $request->input('description');
        $country->save();

        NotificationService::notifyUpdate('País', $country->name);

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El país ' . $country->name . ' se ha actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $countryName = $country->name;
        $country->delete();

        NotificationService::notifyDelete('País', $countryName);

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El país ' . $countryName . ' se ha eliminado correctamente.'
        ]);
    }
}
