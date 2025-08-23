<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SpecialtyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');
        $page = (int) ($request->query('page', 1));
        $ttl = now()->addMinutes(10);
        $cacheKey = "especialidades:index:v2:status={$status}:q=" . urlencode((string) $search) . ":p={$page}";

        $specialties = Cache::tags(['especialidades'])->remember($cacheKey, $ttl, function () use ($status, $search) {
            return Specialty::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });

        $specialties->appends($request->all());
        return view('modules.specialties.index', compact('specialties', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.specialties.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255'
        ];
        $messages = [
            'name.required' => 'El nombre de la especialidad es obligatorio.',
            'name.min' => 'El nombre de la especialidad debe tener más de 3 caracteres.'
        ];

        $this->validate($request, $rules, $messages);

        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        Cache::tags(['especialidades'])->flush();

        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'success',
            'title' => 'Creación Éxitosa',
            'message' => 'La especialidad ' . $specialty->name . ' se ha creado correctamente.'
        ]);
    }

    public function edit(Specialty $specialty)
    {
        $ttl = now()->addHours(6);
        $cacheKey = "especialidades:show:v2:{$specialty->id}";
        $cached = Cache::tags(['especialidades'])->remember($cacheKey, $ttl, fn() => $specialty->only(['id', 'name', 'description', 'is_active']));
        $specialty->fill($cached);
        return view('modules.specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'nullable|string|max:255'
        ];
        $messages = [
            'name.required' => 'El nombre de la especialidad es obligatorio.',
            'name.min' => 'El nombre de la especialidad debe tener más de 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);

        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        Cache::tags(['especialidades'])->flush();

        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'info',
            'title' => 'Actualización Éxitosa',
            'message' => 'La especialidad ' . $specialty->name . ' se ha actualizado correctamente.'
        ]);
    }

    public function destroy(Specialty $specialty)
    {
        $specialtyName = $specialty->name;
        $specialty->is_active = false;
        $specialty->save();

        Cache::tags(['especialidades'])->flush();

        return redirect()->route('especialidades.index')->with('toast', [
            'type' => 'warning',
            'title' => 'Eliminación Éxitosa',
            'message' => 'La especialidad ' . $specialtyName . ' se ha eliminado correctamente.'
        ]);
    }

    public function reactivate($id)
    {
        $specialty = Specialty::findOrFail($id);
        $specialty->is_active = true;
        $specialty->save();

        Cache::tags(['especialidades'])->flush();

        return redirect()
            ->route('especialidades.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La especialidad ' . $specialty->name . ' ha sido reactivada correctamente.'
            ]);
    }
}