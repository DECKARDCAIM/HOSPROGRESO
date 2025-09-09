<?php

namespace App\Http\Controllers;

use App\Models\LinguisticCommunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LinguisticCommunityController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $page = (int) ($request->query('page', 1));
        $key = "comunidades_linguisticas:index:v1:status={$status}:q=" . urlencode($search) . ":p={$page}";
        $linguisticCommunities = Cache::tags(['comunidades_linguisticas'])->remember($key, now()->addMinutes(10), function () use ($status, $search) {
            return LinguisticCommunity::select('id', 'name', 'description', 'is_active')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(25);
        });
        $linguisticCommunities->appends(['status' => $status, 'search' => $search]);
        return view('modules.linguistic_communities.index', compact('linguisticCommunities', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.linguistic_communities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $linguisticCommunity = LinguisticCommunity::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        Cache::tags(['comunidades_linguisticas'])->flush();

        return redirect()
            ->route('linguistic-communities.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'La comunidad lingüística ' . $linguisticCommunity->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(LinguisticCommunity $linguisticCommunity)
    {
        return view('modules.linguistic_communities.edit', compact('linguisticCommunity'));
    }

    public function update(Request $request, LinguisticCommunity $linguisticCommunity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $linguisticCommunity->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Cache::tags(['comunidades_linguisticas'])->flush();

        return redirect()
            ->route('linguistic-communities.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'La comunidad lingüística ' . $linguisticCommunity->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(LinguisticCommunity $linguisticCommunity)
    {
        $linguisticCommunityName = $linguisticCommunity->name;
        $linguisticCommunity->update(['is_active' => false]);

        Cache::tags(['comunidades_linguisticas'])->flush();

        return redirect()
            ->route('linguistic-communities.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La comunidad lingüística ' . $linguisticCommunityName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $linguisticCommunity = LinguisticCommunity::findOrFail($id);
        $linguisticCommunity->is_active = true;
        $linguisticCommunity->save();

        Cache::tags(['comunidades_linguisticas'])->flush();

        return redirect()
            ->route('linguistic-communities.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'La comunidad lingüística ' . $linguisticCommunity->name . ' ha sido reactivada correctamente.'
            ]);
    }
}