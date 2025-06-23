<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ClinicalRecord;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('clinicalRecord')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('modules.patients.index', compact('patients'));
    }

    public function create()
    {
        $clinicalRecords = ClinicalRecord::orderBy('first_name')->get();
        return view('modules.patients.create', compact('clinicalRecords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
        ]);

        // Verificar si ya existe una historia clínica para este expediente
        $existingPatient = Patient::where('clinical_record_id', $request->clinical_record_id)->first();
        if ($existingPatient) {
            return redirect()->back()
                ->with('error', 'Ya existe una historia clínica para este expediente.')
                ->withInput();
        }

        // Generar número de historia clínica único
        $historyNumber = 'HC-' . date('Y') . '-' . str_pad(Patient::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $data = $request->all();
        $data['history_number'] = $historyNumber;

        Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', 'Historia clínica creada exitosamente.');
    }

    public function edit(Patient $patient)
    {
        $clinicalRecords = ClinicalRecord::orderBy('first_name')->get();
        return view('modules.patients.edit', compact('patient', 'clinicalRecords'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
        ]);

        // Verificar si ya existe otra historia clínica para este expediente
        $existingPatient = Patient::where('clinical_record_id', $request->clinical_record_id)
            ->where('id', '!=', $patient->id)
            ->first();
        if ($existingPatient) {
            return redirect()->back()
                ->with('error', 'Ya existe una historia clínica para este expediente.')
                ->withInput();
        }

        $patient->update($request->all());

        return redirect()->route('patients.index')
            ->with('success', 'Historia clínica actualizada exitosamente.');
    }

    public function destroy(Patient $patient)
    {
        // Verificar si tiene consultas médicas asociadas
        if ($patient->medicalConsultations()->count() > 0) {
            return redirect()->route('patients.index')
                ->with('error', 'No se puede eliminar la historia clínica porque tiene consultas médicas asociadas.');
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Historia clínica eliminada exitosamente.');
    }
} 