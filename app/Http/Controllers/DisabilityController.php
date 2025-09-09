<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DisabilityController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $key = "discapacidades:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}";
        $disabilities = Cache::tags(['discapacidades'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return Disability::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $disabilities->appends(['status' => $status, 'search' => $search]);
        return view('modules.disabilities.index', compact('disabilities', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.disabilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $disability = Disability::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        Cache::tags(['discapacidades'])->flush();

        return redirect()
            ->route('disabilities.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La discapacidad ' . $disability->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Disability $disability)
    {
        return view('modules.disabilities.edit', compact('disability'));
    }

    public function update(Request $request, Disability $disability)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $disability->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Cache::tags(['discapacidades'])->flush();

        return redirect()
            ->route('disabilities.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'La discapacidad ' . $disability->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Disability $disability)
    {
        $disabilityName = $disability->name;
        $disability->update(['is_active' => false]);

        Cache::tags(['discapacidades'])->flush();

        return redirect()
            ->route('disabilities.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La discapacidad ' . $disabilityName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $disability = Disability::findOrFail($id);
        $disability->is_active = true;
        $disability->save();

        Cache::tags(['discapacidades'])->flush();

        return redirect()
            ->route('disabilities.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La discapacidad ' . $disability->name . ' ha sido reactivada correctamente.'
            ]);
    }
}