<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)->orderBy('name')->paginate(10);
        return view('modules.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('modules.exams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:exams,code',
        ]);

        Exam::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Examen creado exitosamente.');
    }

    public function edit(Exam $exam)
    {
        return view('modules.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:20|unique:exams,code,' . $exam->id,
        ]);

        $exam->update($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Examen actualizado exitosamente.');
    }

    public function destroy(Exam $exam)
    {
        $exam->update(['is_active' => false]);

        return redirect()->route('exams.index')
            ->with('success', 'Examen eliminado exitosamente.');
    }
} 