<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsultation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\LaboratoryTest;
use App\Models\Exam;
use App\Models\Medication;
use App\Models\ControlType;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\ClinicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class MedicalConsultationController extends Controller
{
    public function index()
    {
        $query = MedicalConsultation::with(['clinicalRecord:id,record_number,first_name,first_lastname', 'doctor:id,first_name,first_lastname', 'specialty:id,name'])
            ->select('id','clinical_record_id','doctor_id','specialty_id','status','attention_type','consultation_date')
            ->whereIn('status', ['abierta', 'en_proceso']);

        // Filtrar por tipo de atención según el rol del usuario
        $user = auth()->user();
        if ($user->isEmergency()) {
            $query->where('attention_type', 'emergencia');
        } elseif ($user->isConsultation()) {
            $query->where('attention_type', 'consulta_externa');
        }

        $page = (int) (request()->query('page', 1));
        $cacheKey = 'consultas:abiertas:index:v1:user=' . auth()->id() . ':p=' . $page;
        $medicalConsultations = Cache::tags(['consultas','listados'])->remember($cacheKey, now()->addMinutes(5), function () use ($query) {
            return $query->orderBy('created_at', 'desc')->paginate(25);
        });

        return view('modules.medical_consultations.index', compact('medicalConsultations'));
    }

    public function create(Request $request)
    {
        // Verificar si se proporciona el ID del expediente clínico
        if (!$request->has('clinical_record_id')) {
            return redirect()->route('clinical-records.index')
                ->with('error', [
                    'title' => 'Acceso No Válido',
                    'message' => 'Para crear una consulta médica debe acceder desde un expediente clínico específico. Seleccione un expediente de la lista.'
                ]);
        }

        // Validar que el ID del expediente clínico sea válido
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id'
        ]);

        $clinicalRecord = ClinicalRecord::findOrFail($request->clinical_record_id);
        
        // Obtener doctores y especialidades para los modales
        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)
                ->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));

        return view('modules.medical_consultations.create', compact('clinicalRecord', 'doctors', 'specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
            'attention_type' => 'required|in:emergencia,consulta_externa',
            'professional_type' => 'required|in:doctor,nurse',
            'specialty_id' => 'required_if:professional_type,doctor|exists:specialties,id',
            'doctor_id' => 'required_if:professional_type,doctor|exists:doctors,id',
        ]);

        // Validaciones específicas por sexo y edad
        if ($request->professional_type === 'doctor' && $request->specialty_id) {
            $clinicalRecord = ClinicalRecord::with('sex')->findOrFail($request->clinical_record_id);
            $specialty = Specialty::findOrFail($request->specialty_id);
            
            // Validar especialidades ginecológicas
            if (stripos($specialty->name, 'ginec') !== false || stripos($specialty->name, 'obstet') !== false) {
                // Si es hombre, no puede acceder a ginecología
                if ($clinicalRecord->sex && strtolower($clinicalRecord->sex->name) === 'masculino') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', [
                            'title' => 'Especialidad No Válida',
                            'message' => 'Los pacientes masculinos no pueden ser atendidos en ' . $specialty->name . '. Por favor, selecciona otra especialidad.'
                        ]);
                }
            }
        }

        // Crear la consulta médica
        $data = [
            'clinical_record_id' => $request->clinical_record_id,
            'attention_type' => $request->attention_type,
            'status' => 'abierta',
            'consultation_date' => now(),
            'consultation_reason' => 'Pendiente de completar',
            'doctor_id' => $request->professional_type === 'doctor' ? $request->doctor_id : null,
            'specialty_id' => $request->professional_type === 'doctor' ? $request->specialty_id : null,
        ];

        $medicalConsultation = MedicalConsultation::create($data);

        NotificationService::notifyCreate('Consulta Médica', 'Consulta #' . $medicalConsultation->id);

        // Determinar a qué vista de process redirigir
        $processRoute = $this->determineProcessRoute($medicalConsultation);

        return redirect()->route($processRoute, $medicalConsultation->id)
            ->with('success', [
                'title' => 'Consulta Creada',
                'message' => 'La consulta se ha creado correctamente. Continúe con el proceso.'
            ]);
    }

    public function process(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la historia esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta historia clínica ya ha sido finalizada.');
        }

        // Determinar automáticamente la ruta correcta según el tipo de consulta
        $processRoute = $this->determineProcessRoute($medicalConsultation);
        
        // Redirigir a la vista específica correspondiente
        return redirect()->route($processRoute, $medicalConsultation->id);
    }

    public function processAdult(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la consulta esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses = \App\Models\PatientStatus::active()->orderBy('name')->get();

        return view('modules.medical_consultations.process_adult', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'patientStatuses'
        ));
    }

    public function processNursing(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la consulta esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses = \App\Models\PatientStatus::active()->orderBy('name')->get();

        return view('modules.medical_consultations.process_nursing', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'patientStatuses'
        ));
    }

    public function processGynecological(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la consulta esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $contraceptiveMethods = \App\Models\ContraceptiveMethod::active()->orderBy('type')->orderBy('name')->get();
        $patientStatuses = \App\Models\PatientStatus::active()->orderBy('name')->get();

        return view('modules.medical_consultations.process_gynecological', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'contraceptiveMethods', 'patientStatuses'
        ));
    }

    public function processPediatric(MedicalConsultation $medicalConsultation)
    {
        // Verificar que la consulta esté abierta o en proceso
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
        return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Doctor::with('specialty')->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('first_lastname')
            ->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $laboratoryTests = LaboratoryTest::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('name')->get();
        $medications = Medication::where('is_active', true)->orderBy('name')->get();
        $controlTypes = ControlType::where('is_active', true)->orderBy('name')->get();
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses = \App\Models\PatientStatus::active()->orderBy('name')->get();

        return view('modules.medical_consultations.process_pediatric', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'patientStatuses'
        ));
    }



    public function show(MedicalConsultation $medicalConsultation)
    {
        $medicalConsultation->load([
            'clinicalRecord', 'doctor', 'specialty', 'laboratoryTests', 'exams', 'medications',
            'companionRelationship', 'guardianRelationship', 'contraceptiveMethod', 'patientStatus'
        ]);
        
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

        // Validaciones condicionales para consultas de enfermería vs doctor
        $rules = [
            'consultation_date' => 'required|date',
            'consultation_reason' => 'required|string',
            'final_status' => 'required|in:egresado,hospitalizado,referido,fallecido',
        ];

        // Si tiene doctor_id, validar doctor y especialidad
        if ($medicalConsultation->doctor_id) {
            $rules['doctor_id'] = 'required|exists:doctors,id';
            $rules['specialty_id'] = 'required|exists:specialties,id';
        }

        $request->validate($rules + [
            'medical_diagnosis' => 'nullable|string',
            'diagnosis_cie10_code' => 'nullable|string|max:10',
            'prescribed_treatment' => 'nullable|string',
            'nursing_note' => 'nullable|string',
            'admission_note' => 'nullable|string',
            'emergency_vital_signs' => 'nullable|string',
            'emergency_trauma_assessment' => 'nullable|string',
            'emergency_treatment_plan' => 'nullable|string',
            'consultation_physical_exam' => 'nullable|string',
            'consultation_treatment_plan' => 'nullable|string',
            'reference_contrareference' => 'nullable|string',
            'control_type_id' => 'nullable|exists:control_types,id',
            'is_new_patient' => 'nullable|boolean',
            'has_igss' => 'nullable|boolean',
            'gestation_weeks' => 'nullable|integer|min:1|max:42',
            'was_referred' => 'nullable|boolean',
            'comes_counter_referred' => 'nullable|boolean',
            'comes_referred' => 'nullable|boolean',
            'was_counter_referred' => 'nullable|boolean',
            'reference_destination' => 'nullable|string',
            'reference_reason' => 'nullable|string',
            'sigsa_observations' => 'nullable|string',
            'laboratory_test_ids' => 'nullable|array',
            'laboratory_test_ids.*' => 'exists:laboratory_tests,id',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'exists:exams,id',
            'medication_ids' => 'nullable|array',
            'medication_ids.*' => 'exists:medications,id',
            // Validaciones para campos de relaciones
            'companion_name' => 'nullable|string|max:100',
            'companion_phone' => 'nullable|string|max:8',
            'companion_email' => 'nullable|email|max:100',
            'companion_dpi' => 'nullable|string|max:13',
            'companion_relationship_id' => 'required_with:companion_name|exists:companion_relationships,id',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:8',
            'guardian_email' => 'nullable|email|max:100',
            'guardian_dpi' => 'nullable|string|max:13',
            'guardian_relationship_id' => 'required_with:guardian_name|exists:companion_relationships,id',
            'guardian_address' => 'nullable|string|max:200',
            'emergency_contact' => 'nullable|string|max:8',
            // Validaciones para campos gineco-obstétricos
            'is_pregnant' => 'nullable|boolean',
            'last_menstrual_period' => 'nullable|date',
            'menstrual_cycle' => 'nullable|integer|min:20|max:35',
            'pregnancies_count' => 'nullable|integer|min:0',
            'births_count' => 'nullable|integer|min:0',
            'abortions_count' => 'nullable|integer|min:0',
            'cesareans_count' => 'nullable|integer|min:0',
            'contraceptive_method_id' => 'nullable|exists:contraceptive_methods,id',
            'gynecological_history' => 'nullable|string',
            // Validaciones para campos pediátricos
            'birth_weight' => 'nullable|numeric|min:0.5|max:10',
            'current_weight' => 'nullable|numeric|min:0.5|max:200',
            'current_height' => 'nullable|numeric|min:30|max:250',
            'head_circumference' => 'nullable|numeric|min:20|max:70',
            'vaccination_status' => 'nullable|string|max:50',
            'feeding_type' => 'nullable|string|max:50',
            'development_milestones' => 'nullable|string|max:50',
            'pediatric_history' => 'nullable|string',
            'parent_instructions' => 'nullable|string',
            // Validaciones para estados finales
            'hospital_service' => 'nullable|string|max:100',
            'death_date' => 'nullable|date',
            'death_cause' => 'nullable|string|max:200',
            // Validaciones para nuevos campos
            'patient_status_id' => 'required|exists:patient_statuses,id',
            'admission_note' => 'nullable|string|max:500',
        ], [
            // Mensajes personalizados
            'companion_relationship_id.required_with' => 'Debe seleccionar la relación del acompañante cuando se especifica el nombre.',
            'guardian_relationship_id.required_with' => 'Debe seleccionar la relación del tutor/guardián cuando se especifica el nombre.',
            'companion_phone.max' => 'El teléfono del acompañante no puede tener más de 8 caracteres.',
            'companion_dpi.max' => 'El DPI del acompañante no puede tener más de 13 caracteres.',
            'guardian_phone.max' => 'El teléfono del tutor/guardián no puede tener más de 8 caracteres.',
            'guardian_dpi.max' => 'El DPI del tutor/guardián no puede tener más de 13 caracteres.',
            'emergency_contact.max' => 'El contacto de emergencia no puede tener más de 8 caracteres.',
            'final_status.required' => 'Debe seleccionar un estado final para la consulta.',
            'patient_status_id.required' => 'Debe seleccionar un estado del paciente.',
            'admission_note.max' => 'La nota de admisión no puede tener más de 500 caracteres.',
        ]);

        $data = $request->except(['laboratory_test_ids', 'exam_ids', 'medication_ids']);
        $data['consultation_date'] = \Carbon\Carbon::parse($request->consultation_date);
        
        // Lógica automática: Si el estado del paciente es "Fallecido", establecer final_status como "fallecido"
        if ($request->patient_status_id) {
            $patientStatus = \App\Models\PatientStatus::find($request->patient_status_id);
            if ($patientStatus && strtolower($patientStatus->name) === 'fallecido') {
                $data['final_status'] = 'fallecido';
            }
        }
        
        // Convertir checkboxes
        $data['is_new_patient'] = $request->has('is_new_patient');
        $data['has_igss'] = $request->has('has_igss');
        $data['was_referred'] = $request->has('was_referred');
        $data['comes_counter_referred'] = $request->has('comes_counter_referred');
        $data['comes_referred'] = $request->has('comes_referred');
        $data['was_counter_referred'] = $request->has('was_counter_referred');
        
        // Finalizar la historia clínica
        $data['status'] = 'finalizada';

        $medicalConsultation->update($data);

        // Sincronizar pruebas de laboratorio
        $medicalConsultation->laboratoryTests()->sync($request->laboratory_test_ids ?? []);

        // Sincronizar exámenes
        $medicalConsultation->exams()->sync($request->exam_ids ?? []);

        // Sincronizar medicamentos
        $medicalConsultation->medications()->sync($request->medication_ids ?? []);

        NotificationService::notifyUpdate('Historia Clínica', 'Historia #' . $medicalConsultation->id . ' finalizada');

        return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
            ->with('success', [
                'title' => 'Historia Clínica Finalizada',
                'message' => 'La historia clínica se ha finalizado correctamente. Estado: ' . ucfirst($request->final_status)
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
        $medicalConsultation->load([
            'clinicalRecord.sex', 'clinicalRecord', 'doctor', 'specialty', 'laboratoryTests', 'exams', 'medications',
            'companionRelationship', 'guardianRelationship', 'contraceptiveMethod', 'patientStatus'
        ]);
        
        $pdf = Pdf::loadView('modules.medical_consultations.print_pdf', compact('medicalConsultation'))
                  ->setPaper('A4', 'portrait')
                  ->setOption('enable-local-file-access', true)
                  ->setOption('page-size', 'A4')
                  ->setOption('margin-top', '20mm')
                  ->setOption('margin-right', '15mm')
                  ->setOption('margin-bottom', '30mm')
                  ->setOption('margin-left', '15mm')
                  ->setOption('encoding', 'UTF-8')
                  ->setOption('enable-javascript', true)
                  ->setOption('javascript-delay', 1000)
                  ->setOption('enable-smart-shrinking', true)
                  ->setOption('no-stop-slow-scripts', true);
        
        return $pdf->stream('historia_clinica_'.$medicalConsultation->id.'.pdf');
    }

    /**
     * Determina a qué ruta de process redirigir según la especialidad y edad del paciente
     */
    private function determineProcessRoute(MedicalConsultation $medicalConsultation)
    {
        // Si no tiene doctor (enfermería), usa process de enfermería
        if (!$medicalConsultation->doctor_id) {
            return 'medical-consultations.process-nursing';
        }

        $clinicalRecord = $medicalConsultation->clinicalRecord;
        $specialty = $medicalConsultation->specialty;

        // Calcular edad del paciente
        $age = $clinicalRecord->birth_date ? $clinicalRecord->birth_date->age : 0;

        // Prioridad 1: Especialidades gineco-obstétricas (independiente de la edad)
        if ($specialty && (
            stripos($specialty->name, 'ginec') !== false || 
            stripos($specialty->name, 'obstet') !== false ||
            strtolower($specialty->name) === 'ginecoobstetricia'
        )) {
            // Mujeres con especialidad ginecológica van a vista ginecológica (cualquier edad)
            return 'medical-consultations.process-gynecological';
        }
        
        // Prioridad 2: Edad menor de 18 años para otras especialidades
        if ($age < 18) {
            // Menores de 18 años con especialidades no ginecológicas -> Pediatría
            return 'medical-consultations.process-pediatric';
        }

        // Prioridad 3: Adultos con otras especialidades
        return 'medical-consultations.process-adult';
    }

    /**
     * Determina automáticamente el servicio de hospitalización
     */
    private function determineHospitalService(MedicalConsultation $medicalConsultation)
    {
        $clinicalRecord = $medicalConsultation->clinicalRecord;
        $specialty = $medicalConsultation->specialty;
        
        // Calcular edad
        $age = $clinicalRecord->birth_date ? $clinicalRecord->birth_date->age : 0;
        
        if ($age < 18) {
            return 'Pediatría';
        }
        
        if ($specialty && (
            stripos($specialty->name, 'ginec') !== false || 
            stripos($specialty->name, 'obstet') !== false ||
            strtolower($specialty->name) === 'ginecoobstetricia'
        )) {
            return 'Ginecología';
        }
        
        if ($clinicalRecord->sex && strtolower($clinicalRecord->sex->name) === 'masculino') {
            return 'Encamamiento Hombre';
        }
        
        return 'Encamamiento Mujer';
    }
} 