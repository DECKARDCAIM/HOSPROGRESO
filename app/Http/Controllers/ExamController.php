<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search', '');
        $exams = Exam::where('is_active', $status === 'active' ? 1 : 0)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends(['status' => $status, 'search' => $search]);
        return view('modules.exams.index', compact('exams', 'status', 'search'));
    }

    public function create()
    {
        return view('modules.exams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $exam = Exam::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        NotificationService::notifyCreate('Examen', $exam->name);
        return redirect()->route('exams.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El examen ' . $exam->name . ' se ha creado correctamente.'
            ]);
    }

    public function edit(Exam $exam)
    {
        return view('modules.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $exam->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        NotificationService::notifyUpdate('Examen', $exam->name);
        return redirect()->route('exams.index')
            ->with('toast', [
                'type' => 'info',
                'title' => 'Actualización Éxitosa',
                'message' => 'El examen ' . $exam->name . ' se ha actualizado correctamente.'
            ]);
    }

    public function destroy(Exam $exam)
    {
        $examName = $exam->name;
        $exam->update(['is_active' => false]);

        NotificationService::notifyDelete('Examen', $examName);

        return redirect()->route('exams.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El examen ' . $examName . ' se ha eliminado correctamente.'
            ]);
    }

    public function reactivate($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->is_active = true;
        $exam->save();
        NotificationService::notifyUpdate('Examen', $exam->name);
        return redirect()->route('exams.index', ['status' => 'inactive'])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El examen ' . $exam->name . ' ha sido reactivado correctamente.'
            ]);
    }
} 