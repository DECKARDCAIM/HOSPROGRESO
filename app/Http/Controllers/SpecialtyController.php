<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use App\Models\Specialty as ModelsSpecialty;

class SpecialtyController extends Controller
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
        $specialties = Specialty::all();
        return view('modules.specialties.index', compact('specialties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.specialties.create');
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

        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        return redirect()->route('especialidades.index')->with('success', 'Especialidad creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialty $specialty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialty $specialty)
    {
        return view('modules.specialties.edit', compact('specialty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialty $specialty)
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

        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        return redirect()->route('especialidades.index')->with('success', 'Especialidad modificada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return redirect()->route('especialidades.index')->with('success', 'Especialidad eliminada con éxito.');
    }
}
