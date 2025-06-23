<?php

namespace App\Http\Controllers;

use App\Models\LinguisticCommunity;
use Illuminate\Http\Request;

class LinguisticCommunityController extends Controller
{
    public function index()
    {
        $linguisticCommunities = LinguisticCommunity::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.linguistic_communities.index', compact('linguisticCommunities'));
    }

    public function create()
    {
        return view('modules.linguistic_communities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:linguistic_communities,code',
        ]);

        LinguisticCommunity::create($request->all());

        return redirect()->route('linguistic-communities.index')
            ->with('success', 'Comunidad lingüística creada exitosamente.');
    }

    public function edit(LinguisticCommunity $linguisticCommunity)
    {
        return view('modules.linguistic_communities.edit', compact('linguisticCommunity'));
    }

    public function update(Request $request, LinguisticCommunity $linguisticCommunity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:linguistic_communities,code,' . $linguisticCommunity->id,
        ]);

        $linguisticCommunity->update($request->all());

        return redirect()->route('linguistic-communities.index')
            ->with('success', 'Comunidad lingüística actualizada exitosamente.');
    }

    public function destroy(LinguisticCommunity $linguisticCommunity)
    {
        $linguisticCommunity->update(['is_active' => false]);

        return redirect()->route('linguistic-communities.index')
            ->with('success', 'Comunidad lingüística eliminada exitosamente.');
    }
} 