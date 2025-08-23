<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
{
    $status = $request->query('status', 'active');
    $search = $request->query('search', '');

    $page = (int) ($request->query('page', 1));
    $countries = Cache::tags(['paises'])->remember(
        "paises:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}",
        now()->addMinutes(10),
        function () use ($status, $search) {
            return Country::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->paginate(25);
        }
    );

    $countries->appends(['status' => $status, 'search' => $search]);

    return view('modules.ubication.countries.index', compact('countries', 'status', 'search'));
}

    public function create()
    {
        return view('modules.ubication.countries.create');
    }

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

        $country = new Country();
        $country->name = $request->input('name');
        $country->description = $request->input('description');
        $country->save();

        Cache::tags(['paises','departamentos','municipios'])->flush();

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El país ' . $country->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(Country $country)
    {
        return view('modules.ubication.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ];
        $messages = [
            'name.required' => 'El nombre del pais es obligatorio.',
            'name.min' => 'El nombre del pais debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $country->name = $request->input('name');
        $country->description = $request->input('description');
        $wasInactive = !$country->is_active;
        $country->is_active = $request->has('is_active') ? (bool) $request->input('is_active') : $country->is_active;
        $country->save();

        if ($country->is_active && $wasInactive) {
            $departments = Department::where('country_id', $country->id)->get();
            foreach ($departments as $department) {
                $department->is_active = true;
                $department->save();
                $municipalities = Municipality::where('department_id', $department->id)->get();
                foreach ($municipalities as $municipality) {
                    $municipality->is_active = true;
                    $municipality->save();
                }
            }
        }

        Cache::tags(['paises','departamentos','municipios'])->flush();

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'El país ' . $country->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Country $country)
    {
        $countryName = $country->name;
        $country->is_active = false;
        $country->save();

        $departments = Department::where('country_id', $country->id)->get();
        foreach ($departments as $department) {
            $department->is_active = false;
            $department->save();
            $municipalities = Municipality::where('department_id', $department->id)->get();
            foreach ($municipalities as $municipality) {
                $municipality->is_active = false;
                $municipality->save();
            }
        }

        Cache::tags(['paises','departamentos','municipios'])->flush();

        return redirect()->route('paises.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El país ' . $countryName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $country = Country::findOrFail($id);
        $country->is_active = true;
        $country->save();

        $departments = \App\Models\Department::where('country_id', $country->id)->get();
        foreach ($departments as $department) {
            $department->is_active = true;
            $department->save();
            $municipalities = \App\Models\Municipality::where('department_id', $department->id)->get();
            foreach ($municipalities as $municipality) {
                $municipality->is_active = true;
                $municipality->save();
            }
        }

        Cache::tags(['paises','departamentos','municipios'])->flush();

        return redirect()
            ->route('paises.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El país ' . $country->name . ' y sus dependientes han sido reactivados correctamente.'
            ]);
    }
}