<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MunicipalityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $countryId = $request->query('country_id');
        $departmentId = $request->query('department_id');
        $page = (int) ($request->query('page', 1));

        $countries = Cache::tags(['paises'])->remember(
            'paises:select:v1',
            now()->addHours(12),
            fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );

        $departments = collect();
        if ($countryId) {
            $departments = Cache::tags(['departamentos'])->remember(
                "departamentos:select:v1:country={$countryId}",
                now()->addHours(12),
                fn() => Department::where('country_id', $countryId)->orderBy('name')->get(['id', 'name', 'country_id'])
            );
        }

        $ttl = now()->addMinutes(10);
        $version = 'v2';
        $cacheKey = "municipios:index:{$version}:status={$status}:country=" . ($countryId ?: 'null')
            . ':dept=' . ($departmentId ?: 'null') . ':q=' . urlencode((string) $search) . ":p={$page}";

        // Limpiar cache para forzar actualización
        Cache::tags(['municipios'])->flush();
        
        $municipalities = Cache::tags(['municipios'])->remember($cacheKey, $ttl, function () use ($status, $search, $countryId, $departmentId, $departments) {
            $q = Municipality::select('id', 'name', 'description', 'department_id', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0);

            if ($countryId) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                } else {
                    $deptIds = $departments->pluck('id');
                    if ($deptIds->isNotEmpty()) {
                        $q->whereIn('department_id', $deptIds);
                    } else {
                        // Si no hay departamentos para el país seleccionado, no mostrar municipios
                        $q->whereRaw('1 = 0'); // Esto hace que la consulta no devuelva resultados
                    }
                }
            }

            if (!empty($search)) {
                $q->where('name', 'like', '%' . $search . '%');
            }

            return $q->orderBy('name')->paginate(25);
        });

        $municipalities->appends($request->all());

        return view('modules.ubication.municipalities.index', compact(
            'countries', 'departments', 'municipalities', 'status', 'search', 'countryId', 'departmentId'
        ));
    }

    public function create(Request $request)
    {
        $countries = Cache::tags(['paises'])->remember(
            'paises:select:v1',
            now()->addHours(12),
            fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );

        $departments = collect();
        if ($request->filled('country_id')) {
            $countryId = $request->country_id;
            $departments = Cache::tags(['departamentos'])->remember(
                "departamentos:select:v1:country={$countryId}",
                now()->addHours(12),
                fn() => Department::where('country_id', $countryId)->orderBy('name')->get(['id', 'name', 'country_id'])
            );
        }

        return view('modules.ubication.municipalities.create', compact('departments', 'countries'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ];
        $messages = [
            'name.required' => 'El nombre del municipio es obligatorio.',
            'name.min' => 'El nombre del municipio debe tener más de 3 caracteres.',
            'department_id.required' => 'Debe seleccionar un departamento.',
            'department_id.exists' => 'El departamento seleccionado no es válido.',
        ];
        $this->validate($request, $rules, $messages);

        $municipality = new Municipality();
        $municipality->name = $request->input('name');
        $municipality->description = $request->input('description');
        $municipality->department_id = $request->input('department_id');
        $municipality->is_active = true;
        $municipality->save();

        Cache::tags(['municipios'])->flush();

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'El municipio ' . $municipality->name . ' se ha creado correctamente.'
        ]);
    }

    public function show(Municipality $municipality)
    {
        return view('modules.ubication.municipalities.show', compact('municipality'));
    }

    public function edit(Municipality $municipality)
    {
        $ttl = now()->addHours(6);
        $version = 'v2';
        $cacheKey = "municipios:show:{$version}:{$municipality->id}";

        $cached = Cache::tags(['municipios'])->remember($cacheKey, $ttl, function () use ($municipality) {
            return $municipality->only(['id', 'name', 'description', 'department_id', 'is_active']);
        });
        $municipality->fill($cached);

        $countries = Cache::tags(['paises'])->remember(
            'paises:select:v1',
            now()->addHours(12),
            fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );

        $departments = Cache::tags(['departamentos'])->remember(
            'departamentos:select:all:v1',
            now()->addHours(12),
            fn() => Department::orderBy('name')->get(['id', 'name', 'country_id'])
        );

        return view('modules.ubication.municipalities.edit', compact('municipality', 'departments', 'countries'));
    }

    public function update(Request $request, Municipality $municipality)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'nullable|boolean',
        ];
        $messages = [
            'name.required' => 'El nombre del municipio es obligatorio.',
            'name.min' => 'El nombre del municipio debe tener más de 3 caracteres.',
            'department_id.required' => 'Debe seleccionar un departamento.',
            'department_id.exists' => 'El departamento seleccionado no es válido.',
        ];
        $this->validate($request, $rules, $messages);

        $municipality->name = $request->input('name');
        $municipality->description = $request->input('description');
        $municipality->department_id = $request->input('department_id');
        if ($request->has('is_active')) {
            $municipality->is_active = (bool) $request->input('is_active');
        }
        $municipality->save();

        Cache::tags(['municipios'])->flush();

        return redirect()->route('municipios.index')->with('toast', [
            'type' => 'success',
            'title' => 'Actualización Éxitosa',
            'message' => 'El municipio ' . $municipality->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Municipality $municipality)
    {
        $municipalityName = $municipality->name;
        $municipality->is_active = false;
        $municipality->save();

        Cache::tags(['municipios'])->flush();

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

        Cache::tags(['municipios'])->flush();

        return redirect()->route('municipios.index', ['status' => 'inactive'])->with('toast', [
            'type' => 'success',
            'title' => 'Reactivación Éxitosa',
            'message' => 'El municipio ' . $municipality->name . ' ha sido reactivado correctamente.'
        ]);
    }
}