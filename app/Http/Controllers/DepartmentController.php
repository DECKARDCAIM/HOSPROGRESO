<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $country_id = $request->country_id;

        $countries = Cache::tags(['paises', 'catalogos'])->remember('paises:select:v1', now()->addHours(12), fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name']));

        $page = (int) ($request->query('page', 1));
        $key = "departamentos:index:v1:status={$status}:country={$country_id}:q=" . urlencode((string) $search) . ":p={$page}";
        $departments = Cache::tags(['departamentos'])->remember($key, now()->addMinutes(10), function () use ($status, $country_id, $search) {
            return Department::select('id', 'name', 'description', 'country_id', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($country_id, function ($query) use ($country_id) {
                    return $query->where('country_id', $country_id);
                })
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $departments->appends(['status' => $status, 'search' => $search, 'country_id' => $country_id]);

        return view('modules.ubication.departments.index', compact('departments', 'countries', 'status', 'search', 'country_id'));
    }

    public function create()
    {
        $countries = Cache::tags(['paises', 'catalogos'])->remember('paises:select:v1', now()->addHours(12), fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        return view('modules.ubication.departments.create', compact('countries'));
    }

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

        Cache::tags(['departamentos', 'municipios'])->flush();

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El departamento ' . $departments->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(Department $department)
    {
        $countries = Cache::tags(['paises', 'catalogos'])->remember(
            'paises:select:v1',
            now()->addHours(12),
            fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );

        return view('modules.ubication.departments.edit', compact('department', 'countries'));
    }

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
        $department->is_active = $request->has('is_active') ? (bool) $request->input('is_active') : $department->is_active;
        $department->save();

        if ($department->is_active && $wasInactive) {
            $municipalities = Municipality::where('department_id', $department->id)->get();
            foreach ($municipalities as $municipality) {
                $municipality->is_active = true;
                $municipality->save();
            }
        }

        Cache::tags(['departamentos', 'municipios'])->flush();

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'success',
            'title' => 'Actualización Éxitosa',
            'message' => 'El departamento ' . $department->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Department $department)
    {
        $departmentName = $department->name;
        $department->is_active = false;
        $department->save();

        $municipalities = Municipality::where('department_id', $department->id)->get();
        foreach ($municipalities as $municipality) {
            $municipality->is_active = false;
            $municipality->save();
        }

        Cache::tags(['departamentos', 'municipios'])->flush();

        return redirect()->route('departamentos.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'El departamento ' . $departmentName . ' con sus municipios se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $department = Department::findOrFail($id);
        $department->is_active = true;
        $department->save();
        $municipalities = \App\Models\Municipality::where('department_id', $department->id)->get();
        foreach ($municipalities as $municipality) {
            $municipality->is_active = true;
            $municipality->save();
        }

        Cache::tags(['departamentos', 'municipios'])->flush();

        return redirect()
            ->route('departamentos.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El departamento ' . $department->name . ' y sus municipios han sido reactivados correctamente.'
            ]);
    }
}