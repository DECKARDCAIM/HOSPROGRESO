<?php

namespace App\Http\Controllers;

use App\Models\Sex;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SexController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $key = "sexos:index:v1:status={$status}:q=".urlencode($search).":p={$page}";
        $sexes = Cache::tags(['sexos','listados'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return Sex::select('id','name','description','is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $sexes->appends(['status' => $status, 'search' => $search]);
        return view('modules.sexes.index', compact('sexes', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.sexes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $sex = Sex::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        NotificationService::notifyCreate('Sexo', $sex->name);
        return redirect()->route('sexes.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El sexo ' . $sex->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Sex $sex)
    {
        return view('modules.sexes.edit', compact('sex'));
    }

    public function update(Request $request, Sex $sex)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $sex->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        NotificationService::notifyUpdate('Sexo', $sex->name);
        return redirect()->route('sexes.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'El sexo ' . $sex->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Sex $sex)
    {
        $sexName = $sex->name;
        $sex->update(['is_active' => false]);

        NotificationService::notifyDelete('Sexo', $sexName);

        return redirect()->route('sexes.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El sexo ' . $sexName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $sex = Sex::findOrFail($id);
        $sex->is_active = true;
        $sex->save();
        NotificationService::notifyUpdate('Sexo', $sex->name);
        return redirect()->route('sexes.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El sexo ' . $sex->name . ' ha sido reactivado correctamente.'
            ]);
    }
} 