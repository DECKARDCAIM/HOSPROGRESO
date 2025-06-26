<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsultation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\LaboratoryTest;
use App\Models\Exam;
use App\Models\Medication;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\ClinicalRecord;

class MedicalConsultationController extends Controller
{
    public function index()
    {
        $medicalConsultations = MedicalConsultation::with(['clinicalRecord', 'doctor', 'specialty'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('modules.medical_consultations.index', compact('medicalConsultations'));
    }

    public function create()
    {
        $clinicalRecords = ClinicalRecord::orderBy('created_at', 'desc')->get();
        $doctors = Doctor::where('is_active', true)->orderBy('full_name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.create', compact(
            'clinicalRecords', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
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
        $data['consultation_date'] = \Carbon\Carbon::parse($request->consultation_date);

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

        NotificationService::notifyCreate('Consulta Médica', 'Consulta #' . $medicalConsultation->id);

        return redirect()->route('medical-consultations.index')
            ->with('success', [
                'title' => 'Consulta Creada',
                'message' => 'La consulta médica se ha creado correctamente.'
            ]);
    }

    public function edit(MedicalConsultation $medicalConsultation)
    {
        $clinicalRecords = ClinicalRecord::orderBy('created_at', 'desc')->get();
        $doctors = Doctor::where('is_active', true)->orderBy('full_name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.edit', compact(
            'medicalConsultation', 'clinicalRecords', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function update(Request $request, MedicalConsultation $medicalConsultation)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
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
        $data['consultation_date'] = \Carbon\Carbon::parse($request->consultation_date);

        $medicalConsultation->update($data);

        // Sincronizar pruebas de laboratorio
        $medicalConsultation->laboratoryTests()->sync($request->laboratory_test_ids ?? []);

        // Sincronizar exámenes
        $medicalConsultation->exams()->sync($request->exam_ids ?? []);

        // Sincronizar medicamentos
        $medicalConsultation->medications()->sync($request->medication_ids ?? []);

        NotificationService::notifyUpdate('Consulta Médica', 'Consulta #' . $medicalConsultation->id);

        return redirect()->route('medical-consultations.index')
            ->with('success', [
                'title' => 'Consulta Actualizada',
                'message' => 'La consulta médica se ha actualizado correctamente.'
            ]);
    }

    public function destroy(MedicalConsultation $medicalConsultation)
    {
        $consultationId = $medicalConsultation->id;
        $medicalConsultation->delete();

        NotificationService::notifyDelete('Consulta Médica', 'Consulta #' . $consultationId);

        return redirect()->route('medical-consultations.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La consulta médica #' . $consultationId . ' se ha eliminado correctamente.'
            ]);
    }
} 