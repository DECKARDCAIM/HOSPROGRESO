<?php

namespace App\Http\Controllers;

use App\Models\Country;
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

        $countries = new Country();
        $countries->name = $request->input('name');
        $countries->description = $request->input('description');
        $countries->save();
        $notification = [
            'message' => 'El pais ' . $countries->name . ' se ha creado correctamente.',
            'alert-type' => 'Creación Éxitosa'
        ];
        return redirect()->route('paises.index')->with(compact('notification'));
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

        $country->name = $request->input('name');
        $country->description = $request->input('description');
        $country->save();
        $notification = [
            'message' => 'El pais ' . $country->name . ' se ha actualizado correctamente.',
            'alert-type' => 'Actualización Éxitosa'
        ];
        return redirect()->route('paises.index')->with(compact('notification'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();
        $notification = [
            'message' => 'El pais ' . $country->name . ' se ha eliminado correctamente.',
            'alert-type' => 'Eliminación Éxitosa'
        ];

        return redirect()->route('paises.index')->with(compact('notification'));
    }
}
