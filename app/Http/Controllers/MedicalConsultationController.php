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
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalConsultationController extends Controller
{
    public function index()
    {
        $query = MedicalConsultation::with(['clinicalRecord', 'doctor', 'specialty'])
            ->whereIn('status', ['abierta', 'en_proceso']);

        // Filtrar por tipo de atención según el rol del usuario
        $user = auth()->user();
        if (method_exists($user, 'isEmergency') && $user->isEmergency()) {
            $query->where('attention_type', 'emergencia');
        } elseif (method_exists($user, 'isConsultation') && $user->isConsultation()) {
            $query->where('attention_type', 'consulta_externa');
        }

        $medicalConsultations = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('modules.medical_consultations.index', compact('medicalConsultations'));
    }

    public function create(Request $request)
    {
        // Validar que se proporcione el ID del expediente clínico
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id'
        ]);

        $clinicalRecord = ClinicalRecord::findOrFail($request->clinical_record_id);
        
        // Determinar el tipo de atención basado en el rol del usuario
        $attentionType = auth()->user()->isEmergency() ? 'emergencia' : 'consulta_externa';

        return view('modules.medical_consultations.create', compact('clinicalRecord', 'attentionType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
            'attention_type' => 'required|in:emergencia,consulta_externa',
        ]);

        // Crear solo la historia básica inicial
        $data = [
            'clinical_record_id' => $request->clinical_record_id,
            'attention_type' => $request->attention_type,
            'status' => 'abierta',
            'consultation_date' => now(),
            'consultation_reason' => 'Pendiente de completar',
            'doctor_id' => null, // Se asignará en el proceso
            'specialty_id' => null, // Se asignará en el proceso
        ];

        $medicalConsultation = MedicalConsultation::create($data);

        NotificationService::notifyCreate('Historia Clínica', 'Historia #' . $medicalConsultation->id);

        return redirect()->route('medical-consultations.process', $medicalConsultation->id)
            ->with('success', [
                'title' => 'Historia Clínica Creada',
                'message' => 'La historia clínica se ha creado correctamente. Continúe con el proceso.'
            ]);
    }

    public function process(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la historia esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta historia clínica ya ha sido finalizada.');
        }

        $doctors = Doctor::where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('first_lastname')
            ->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.process', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function updateProcess(Request $request, MedicalConsultation $medicalConsultation)
    {
        // Verificar que la historia esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return response()->json([
                'success' => false,
                'message' => 'Esta historia clínica ya ha sido finalizada.'
            ], 400);
        }

        $step = $request->input('step');
        $rules = [];
        
        switch ($step) {
            case 1:
                $rules = [
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'consultation_reason' => 'required|string',
                ];
                break;
            case 2:
                $rules = [
            'medical_diagnosis' => 'nullable|string',
            'nursing_note' => 'nullable|string',
            'admission_note' => 'nullable|string',
                ];
                if ($medicalConsultation->isEmergency()) {
                    $rules = array_merge($rules, [
                        'emergency_vital_signs' => 'nullable|string',
                        'emergency_trauma_assessment' => 'nullable|string',
                        'emergency_treatment_plan' => 'nullable|string',
                    ]);
                } else {
                    $rules = array_merge($rules, [
                        'consultation_physical_exam' => 'nullable|string',
                        'consultation_treatment_plan' => 'nullable|string',
                    ]);
                }
                break;
            case 3:
                $rules = [
            'laboratory_test_ids' => 'nullable|array',
            'laboratory_test_ids.*' => 'exists:laboratory_tests,id',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'exists:exams,id',
                ];
                break;
            case 4:
                $rules = [
            'medication_ids' => 'nullable|array',
            'medication_ids.*' => 'exists:medications,id',
                    'reference_contrareference' => 'nullable|string',
                ];
                break;
            case 5:
                $rules = [
                    'final_status' => 'required|in:hospitalizado,egresado',
                ];
                break;
        }
        
        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $allErrors = collect($e->errors())->flatten()->implode(' ');
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . $allErrors,
            ], 422);
        }

        // Actualizar campos según el paso
        $data = [];
        switch ($step) {
            case 1:
                $data = [
                    'doctor_id' => $request->doctor_id,
                    'specialty_id' => $request->specialty_id,
                    'consultation_reason' => $request->consultation_reason,
                ];
                break;
            case 2:
                $data = [
                    'medical_diagnosis' => $request->medical_diagnosis,
                    'nursing_note' => $request->nursing_note,
                    'admission_note' => $request->admission_note,
                ];
                if ($medicalConsultation->isEmergency()) {
                    $data = array_merge($data, [
                        'emergency_vital_signs' => $request->emergency_vital_signs,
                        'emergency_trauma_assessment' => $request->emergency_trauma_assessment,
                        'emergency_treatment_plan' => $request->emergency_treatment_plan,
                    ]);
                } else {
                    $data = array_merge($data, [
                        'consultation_physical_exam' => $request->consultation_physical_exam,
                        'consultation_treatment_plan' => $request->consultation_treatment_plan,
                    ]);
                }
                break;
            case 3:
                // Solo relaciones
                break;
            case 4:
                $data = [
                    'reference_contrareference' => $request->reference_contrareference,
                ];
                break;
            case 5:
                // Solo finalización
                break;
        }
        
        if ($step < 5) {
            $data['status'] = 'en_proceso';
        }
        
        if (!empty($data)) {
            $medicalConsultation->update($data);
        }

        // Sincronizar relaciones
        if ($step == 3) {
            $medicalConsultation->laboratoryTests()->sync($request->laboratory_test_ids ?? []);
            $medicalConsultation->exams()->sync($request->exam_ids ?? []);
        }
        if ($step == 4) {
            $medicalConsultation->medications()->sync($request->medication_ids ?? []);
        }

        // Si es el paso final, finalizar la historia
        if ($step == 5 && $request->has('final_status')) {
            $medicalConsultation->status = 'finalizada';
            $medicalConsultation->final_status = $request->final_status;
            $medicalConsultation->save();
            
            NotificationService::notifyUpdate('Historia Clínica', 'Historia #' . $medicalConsultation->id . ' finalizada');
            
            return response()->json([
                'success' => true,
                'message' => 'Historia clínica finalizada correctamente.',
                'final_status' => $request->final_status,
                'redirect_url' => route('clinical-records.show', $medicalConsultation->clinical_record_id),
                'toast' => [
                    'type' => 'success',
                    'title' => 'Historia Clínica Finalizada',
                    'message' => 'La historia clínica ha sido finalizada correctamente. Estado: ' . ucfirst($request->final_status)
                ]
            ]);
        }

        // Determinar el mensaje según el paso
        $stepMessages = [
            1 => 'Información básica guardada correctamente',
            2 => 'Evaluación médica guardada correctamente',
            3 => 'Pruebas y exámenes guardados correctamente',
            4 => 'Tratamiento guardado correctamente'
        ];

        return response()->json([
            'success' => true,
            'message' => $stepMessages[$step] ?? 'Paso ' . $step . ' guardado correctamente.',
            'next_step' => $step + 1,
            'toast' => [
                'type' => 'success',
                'title' => 'Guardado Exitoso',
                'message' => $stepMessages[$step] ?? 'Paso ' . $step . ' guardado correctamente.'
            ]
        ]);
    }

    public function finalize(Request $request, MedicalConsultation $medicalConsultation)
    {
        $request->validate([
            'final_status' => 'required|in:hospitalizado,egresado',
        ]);

        $medicalConsultation->status = 'finalizada';
        $medicalConsultation->final_status = $request->final_status;
        $medicalConsultation->save();

        NotificationService::notifyUpdate('Historia Clínica', 'Historia #' . $medicalConsultation->id . ' finalizada');

        return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
            ->with('success', [
                'title' => 'Historia Clínica Finalizada',
                'message' => 'La historia clínica se ha finalizado correctamente. Estado: ' . ucfirst($request->final_status)
            ]);
    }

    public function show(MedicalConsultation $medicalConsultation)
    {
        $medicalConsultation->load(['clinicalRecord', 'doctor', 'specialty', 'laboratoryTests', 'exams', 'medications']);
        
        return view('modules.medical_consultations.show', compact('medicalConsultation'));
    }

    public function edit(MedicalConsultation $medicalConsultation)
    {
        // Solo permitir edición si está abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede editar una historia clínica finalizada.');
        }

        $doctors = Doctor::where('is_active', true)->orderBy('full_name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('modules.medical_consultations.edit', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function update(Request $request, MedicalConsultation $medicalConsultation)
    {
        // Solo permitir actualización si está abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede editar una historia clínica finalizada.');
        }

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'consultation_date' => 'required|date',
            'consultation_reason' => 'required|string',
            'medical_diagnosis' => 'nullable|string',
            'nursing_note' => 'nullable|string',
            'admission_note' => 'nullable|string',
            'emergency_vital_signs' => 'nullable|string',
            'emergency_trauma_assessment' => 'nullable|string',
            'emergency_treatment_plan' => 'nullable|string',
            'consultation_physical_exam' => 'nullable|string',
            'consultation_treatment_plan' => 'nullable|string',
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

        NotificationService::notifyUpdate('Historia Clínica', 'Historia #' . $medicalConsultation->id);

        return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
            ->with('success', [
                'title' => 'Historia Clínica Actualizada',
                'message' => 'La historia clínica se ha actualizado correctamente.'
            ]);
    }

    public function destroy(MedicalConsultation $medicalConsultation)
    {
        // Solo permitir eliminación si está abierta
        if ($medicalConsultation->status !== 'abierta') {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede eliminar una historia clínica que ya ha sido procesada.');
        }

        $consultationId = $medicalConsultation->id;
        $clinicalRecordId = $medicalConsultation->clinical_record_id;
        $medicalConsultation->delete();

        NotificationService::notifyDelete('Historia Clínica', 'Historia #' . $consultationId);

        return redirect()->route('clinical-records.show', $clinicalRecordId)
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'La historia clínica #' . $consultationId . ' se ha eliminado correctamente.'
            ]);
    }

    public function print(MedicalConsultation $medicalConsultation)
    {
        $medicalConsultation->load(['clinicalRecord.sex', 'clinicalRecord', 'doctor', 'specialty', 'laboratoryTests', 'exams', 'medications']);
        $pdf = Pdf::loadView('modules.medical_consultations.print_pdf', compact('medicalConsultation'));
        return $pdf->stream('historia_clinica_'.$medicalConsultation->id.'.pdf');
    }
} 