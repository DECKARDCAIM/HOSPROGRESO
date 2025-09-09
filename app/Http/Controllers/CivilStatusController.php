<?php

namespace App\Http\Controllers;

use App\Models\CivilStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CivilStatusController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $civilStatuses = Cache::tags(['estados_civiles'])->remember(
            "civil-statuses:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}",
            now()->addMinutes(10),
            function () use ($status, $search) {
                return CivilStatus::select('id', 'name', 'description', 'is_active')
                    ->where('is_active', $status === 'active' ? 1 : 0)
                    ->when($search, function ($query, $search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orderBy('name')
                    ->paginate(25);
            }
        );
        $civilStatuses->appends(['status' => $status, 'search' => $search]);
        return view('modules.civil_statuses.index', compact('civilStatuses', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.civil_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $civilStatus = CivilStatus::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        Cache::tags(['estados_civiles'])->flush();

        return redirect()
            ->route('civil-statuses.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El estado civil ' . $civilStatus->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(CivilStatus $civilStatus)
    {
        return view('modules.civil_statuses.edit', compact('civilStatus'));
    }

    public function update(Request $request, CivilStatus $civilStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $civilStatus->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Cache::tags(['estados_civiles'])->flush();

        return redirect()
            ->route('civil-statuses.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'El estado civil ' . $civilStatus->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(CivilStatus $civilStatus)
    {
        $civilStatus->update(['is_active' => false]);

        Cache::tags(['estados_civiles'])->flush();

        return redirect()
            ->route('civil-statuses.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El estado civil ' . $civilStatus->name . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $civilStatus = CivilStatus::findOrFail($id);
        $civilStatus->is_active = true;
        $civilStatus->save();

        Cache::tags(['estados_civiles'])->flush();

        return redirect()
            ->route('civil-statuses.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El estado civil ' . $civilStatus->name . ' ha sido reactivado correctamente.'
            ]);
    }
}