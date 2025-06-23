<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsultation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\LaboratoryTest;
use App\Models\Exam;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicalConsultationController extends Controller
{
    public function index()
    {
        $medicalConsultations = MedicalConsultation::with(['patient.clinicalRecord', 'doctor', 'specialty'])
            ->orderBy('consultation_date', 'desc')
            ->paginate(10);
        return view('modules.medical_consultations.index', compact('medicalConsultations'));
    }

    public function create()
    {
        $patients = Patient::with('clinicalRecord')->orderBy('created_at', 'desc')->get();
        $doctors = Doctor::with('specialty')->where('is_active', true)->orderBy('first_name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.create', compact(
            'patients', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'consultation_date' => 'required|date',
            'consultation_reason' => 'required|string',
            'medical_diagnosis' => 'nullable|string',
            'nursing_note' => 'nullable|string',
            'admission_note' => 'nullable|string',
            'prescribed_medications' => 'nullable|string',
            'reference_contrareference' => 'nullable|string',
            'laboratory_test_ids' => 'nullable|array',
            'laboratory_test_ids.*' => 'exists:laboratory_tests,id',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'exists:exams,id',
            'medication_ids' => 'nullable|array',
            'medication_ids.*' => 'exists:medications,id',
        ]);

        $data = $request->except(['laboratory_test_ids', 'exam_ids', 'medication_ids']);
        $data['consultation_date'] = now(); // Fecha y hora automática

        $medicalConsultation = MedicalConsultation::create($data);

        // Asociar pruebas de laboratorio
        if ($request->has('laboratory_test_ids')) {
            $medicalConsultation->laboratoryTests()->attach($request->laboratory_test_ids);
        }

        // Asociar exámenes
        if ($request->has('exam_ids')) {
            $medicalConsultation->exams()->attach($request->exam_ids);
        }

        // Asociar medicamentos
        if ($request->has('medication_ids')) {
            $medicalConsultation->medications()->attach($request->medication_ids);
        }

        return redirect()->route('medical-consultations.index')
            ->with('success', 'Consulta médica creada exitosamente.');
    }

    public function edit(MedicalConsultation $medicalConsultation)
    {
        $patients = Patient::with('clinicalRecord')->orderBy('created_at', 'desc')->get();
        $doctors = Doctor::with('specialty')->where('is_active', true)->orderBy('first_name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.edit', compact(
            'medicalConsultation', 'patients', 'doctors', 'specialties', 
            'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function update(Request $request, MedicalConsultation $medicalConsultation)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'consultation_date' => 'required|date',
            'consultation_reason' => 'required|string',
            'medical_diagnosis' => 'nullable|string',
            'nursing_note' => 'nullable|string',
            'admission_note' => 'nullable|string',
            'prescribed_medications' => 'nullable|string',
            'reference_contrareference' => 'nullable|string',
            'laboratory_test_ids' => 'nullable|array',
            'laboratory_test_ids.*' => 'exists:laboratory_tests,id',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'exists:exams,id',
            'medication_ids' => 'nullable|array',
            'medication_ids.*' => 'exists:medications,id',
        ]);

        $data = $request->except(['laboratory_test_ids', 'exam_ids', 'medication_ids']);

        $medicalConsultation->update($data);

        // Sincronizar pruebas de laboratorio
        $medicalConsultation->laboratoryTests()->sync($request->laboratory_test_ids ?? []);

        // Sincronizar exámenes
        $medicalConsultation->exams()->sync($request->exam_ids ?? []);

        // Sincronizar medicamentos
        $medicalConsultation->medications()->sync($request->medication_ids ?? []);

        return redirect()->route('medical-consultations.index')
            ->with('success', 'Consulta médica actualizada exitosamente.');
    }

    public function destroy(MedicalConsultation $medicalConsultation)
    {
        $medicalConsultation->delete();

        return redirect()->route('medical-consultations.index')
            ->with('success', 'Consulta médica eliminada exitosamente.');
    }
} 