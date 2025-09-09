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
use Illuminate\Http\Request;
use App\Models\ClinicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class MedicalConsultationController extends Controller
{
    public function index(Request $request)
    {
        $status    = $request->query('status', 'open');
        $search    = $request->query('search', '');
        $attention = $request->query('attention');
        $page      = (int) ($request->query('page', 1));

        $ttl      = now()->addMinutes(10);
        $version  = 'v3';
        $uid      = auth()->id();
        $cacheKey = "consultas:index:{$version}:u={$uid}:status={$status}:att=" . urlencode((string)$attention)
                  . ":q=" . urlencode((string)$search) . ":p={$page}";

        $user = auth()->user();

        $medicalConsultations = Cache::tags(['consultas','listados'])->remember($cacheKey, $ttl, function () use ($user, $status, $attention, $search) {
            $q = MedicalConsultation::with([
                    'clinicalRecord:id,record_number,first_name,first_lastname',
                    'doctor:id,first_name,first_lastname',
                    'specialty:id,name'
                ])
                ->select('id','clinical_record_id','doctor_id','specialty_id','status','attention_type','consultation_date','created_at');

            if ($status === 'open') {
                $q->where('status', 'abierta');
            } elseif ($status === 'in_progress') {
                $q->where('status', 'en_proceso');
            } elseif ($status === 'pending_medical') {
                $q->where('status', 'pendiente_evaluacion_medica');
            } else {
                $q->whereIn('status', ['abierta','en_proceso','pendiente_evaluacion_medica']);
            }

            if ($user->isEmergency()) {
                $q->where('attention_type', 'emergencia');
            } elseif ($user->isConsultation()) {
                $q->where('attention_type', 'consulta_externa');
            }

            if ($attention && in_array($attention, ['emergencia','consulta_externa'], true)) {
                $q->where('attention_type', $attention);
            }

            if (!empty($search)) {
                $like = '%' . $search . '%';
                $q->where(function ($w) use ($like) {
                    $w->whereHas('clinicalRecord', function ($cq) use ($like) {
                        $cq->where('record_number', 'like', $like)
                           ->orWhere('first_name', 'like', $like)
                           ->orWhere('first_lastname', 'like', $like);
                    })->orWhereHas('doctor', function ($dq) use ($like) {
                        $dq->where('first_name', 'like', $like)
                           ->orWhere('first_lastname', 'like', $like);
                    })->orWhereHas('specialty', function ($sq) use ($like) {
                        $sq->where('name', 'like', $like);
                    });
                });
            }

            return $q->orderByDesc('created_at')->paginate(25);
        });

        $medicalConsultations->appends($request->all());

        return view('modules.medical_consultations.index', compact('medicalConsultations','status','search','attention'));
    }

    public function create(Request $request)
    {
        if (!$request->has('clinical_record_id')) {
            return redirect()->route('clinical-records.index')
                ->with('error', [
                    'title' => 'Acceso No Válido',
                    'message' => 'Para crear una consulta médica debe acceder desde un expediente clínico específico. Seleccione un expediente de la lista.'
                ]);
        }

        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
            'attention_type' => 'nullable|in:emergencia,consulta_externa'
        ]);

        $clinicalRecord = ClinicalRecord::findOrFail($request->clinical_record_id);

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('first_lastname')
                ->get();
        });

        $specialties = Cache::tags(['especialidades','catalogos'])->remember(
            'especialidades:select:v2',
            now()->addHours(12),
            fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name'])
        );

        // Obtener información del rol del usuario
        $user = auth()->user();
        $userRole = $user->role;
        $userRoleId = $user->role_id;
        $userRoleName = $user->getRoleName();
        
        // Determinar el tipo de atención automático
        $autoAttentionType = null;
        
        // Si viene en la URL, usar ese valor
        if ($request->has('attention_type')) {
            $autoAttentionType = $request->attention_type;
        } else {
            // Si no, usar el rol del usuario
            if ($user->isEmergency()) {
                $autoAttentionType = 'emergencia';
            } elseif ($user->isConsultation()) {
                $autoAttentionType = 'consulta_externa';
            }
        }

        return view('modules.medical_consultations.create', compact(
            'clinicalRecord', 
            'doctors', 
            'specialties', 
            'userRole', 
            'userRoleId', 
            'userRoleName', 
            'autoAttentionType'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
            'attention_type'     => 'required|in:emergencia,consulta_externa',
            'professional_type'  => 'required|in:doctor,nurse',
            'specialty_id'       => 'required_if:professional_type,doctor|exists:specialties,id',
            'doctor_id'          => 'required_if:professional_type,doctor|exists:doctors,id',
            'consultation_reason' => 'required|string',
            'presion_arterial'   => 'nullable|string',
            'frecuencia_cardiaca' => 'nullable|string',
            'temperatura'        => 'nullable|string',
            'frecuencia_respiratoria' => 'nullable|string',
            'peso'               => 'nullable|string',
            'talla'              => 'nullable|string',
            'saturacion_o2'      => 'nullable|string',
            'glicemia'           => 'nullable|string',
            'companion_name'     => 'nullable|string',
            'companion_relationship_id' => 'nullable|exists:companion_relationships,id',
            'companion_phone'    => 'nullable|string|max:8',
            'companion_email'    => 'nullable|email',
            'companion_dpi'      => 'nullable|string|max:13',
            'companion_address'  => 'nullable|string',
            'nursing_note'       => 'nullable|string',
            'notas_adicionales'  => 'nullable|string',
        ]);

        if ($request->professional_type === 'doctor' && $request->specialty_id) {
            $clinicalRecord = ClinicalRecord::with('sex')->findOrFail($request->clinical_record_id);
            $specialty      = Specialty::findOrFail($request->specialty_id);

            if (stripos($specialty->name, 'ginec') !== false || stripos($specialty->name, 'obstet') !== false) {
                if ($clinicalRecord->sex && strtolower($clinicalRecord->sex->name) === 'masculino') {
                    return back()->withInput()->with('error', [
                        'title'   => 'Especialidad No Válida',
                        'message' => 'Los pacientes masculinos no pueden ser atendidos en ' . $specialty->name . '. Por favor, selecciona otra especialidad.'
                    ]);
                }
            }
        }

        $data = [
            'clinical_record_id'   => $request->clinical_record_id,
            'attention_type'       => $request->attention_type,
            'status'               => 'abierta',
            'consultation_date'    => now(),
            'consultation_reason'  => $request->consultation_reason ?? 'Pendiente de completar',
            'doctor_id'            => $request->professional_type === 'doctor' ? $request->doctor_id : null,
            'specialty_id'         => $request->professional_type === 'doctor' ? $request->specialty_id : null,
            // Campos del acompañante
            'companion_name'       => $request->companion_name,
            'companion_relationship_id' => $request->companion_relationship_id,
            'companion_phone'      => $request->companion_phone,
            'companion_email'      => $request->companion_email,
            'companion_dpi'        => $request->companion_dpi,
            'companion_address'    => $request->companion_address,
        ];

        // Siempre guardar los datos del modal (signos vitales, acompañante, etc.)
        $data['nursing_note'] = $this->formatNursingData($request);
        
        // Si es enfermería, cambiar status a pendiente de evaluación médica
        if ($request->professional_type === 'nurse') {
            $data['status'] = 'pendiente_evaluacion_medica';
        }

        $medicalConsultation = MedicalConsultation::create($data);

        Cache::tags(['consultas'])->flush();

        // Si es enfermería, redirigir al expediente con mensaje
        if ($request->professional_type === 'nurse') {
            return redirect()->route('clinical-records.show', $request->clinical_record_id)->with('success', [
                'title'   => 'Consulta de Enfermería Creada',
                'message' => 'Los signos vitales y datos del paciente han sido registrados. La consulta está pendiente de evaluación médica.'
            ]);
        }

        $processRoute = $this->determineProcessRoute($medicalConsultation);

        return redirect()->route($processRoute, $medicalConsultation->id)->with('success', [
            'title'   => 'Consulta Creada',
            'message' => 'La consulta se ha creado correctamente. Continúe con el proceso.'
        ]);
    }

    public function process(MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta historia clínica ya ha sido finalizada.');
        }

        // Si es una consulta de enfermería pendiente de evaluación médica, solo doctores pueden acceder
        if ($medicalConsultation->status === 'pendiente_evaluacion_medica') {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->hasRole('Doctor')) {
                return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                    ->with('error', 'Solo los doctores pueden evaluar consultas de enfermería.');
            }
        }

        $processRoute = $this->determineProcessRoute($medicalConsultation);
        return redirect()->route($processRoute, $medicalConsultation->id);
    }

    public function processAdult(MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties     = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams           = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications     = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes    = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses        = \App\Models\PatientStatus::active()->orderBy('name')->get();

        // Cargar la relación del parentesco si existe
        $medicalConsultation->load('companionRelationship');

        return view('modules.medical_consultations.process_adult', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'patientStatuses'
        ));
    }

    public function processNursing(MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties     = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams           = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications     = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes    = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses        = \App\Models\PatientStatus::active()->orderBy('name')->get();

        return view('modules.medical_consultations.process_nursing', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'patientStatuses'
        ));
    }

    public function processGynecological(MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get();
        });
        $specialties         = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests     = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams               = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications         = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes        = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $contraceptiveMethods   = \App\Models\ContraceptiveMethod::active()->orderBy('type')->orderBy('name')->get();
        $patientStatuses        = \App\Models\PatientStatus::active()->orderBy('name')->get();

        // Cargar la relación del parentesco si existe
        $medicalConsultation->load('companionRelationship');

        return view('modules.medical_consultations.process_gynecological', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications', 'controlTypes', 'companionRelationships', 'contraceptiveMethods', 'patientStatuses'
        ));
    }

    public function processPediatric(MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'Esta consulta ya ha sido finalizada.');
        }

        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:with-specialty:v1', now()->addHours(6), fn() =>
            Doctor::with('specialty:id,name')->where('is_active', true)->orderBy('first_name')->orderBy('first_lastname')->get(['id','first_name','first_lastname','specialty_id'])
        );
        $specialties     = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams           = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications     = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));
        $controlTypes    = Cache::tags(['tipos_control','catalogos'])->remember('control-types:active:v1', now()->addHours(12), fn()=> ControlType::where('is_active', true)->orderBy('name')->get(['id','name']));
        $companionRelationships = \App\Models\CompanionRelationship::active()->orderBy('name')->get();
        $patientStatuses        = \App\Models\PatientStatus::active()->orderBy('name')->get();

        // Cargar la relación del parentesco si existe
        $medicalConsultation->load('companionRelationship');

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
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede editar una historia clínica finalizada.');
        }

        $doctors         = Cache::tags(['doctores','catalogos'])->remember('doctores:active:for-edit:v1', now()->addHours(6), fn() => Doctor::where('is_active', true)->orderBy('full_name')->get(['id','full_name']));
        $specialties     = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn()=> Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $laboratoryTests = Cache::tags(['pruebas_laboratorio','catalogos'])->remember('laboratory-tests:active:v1', now()->addHours(12), fn()=> LaboratoryTest::where('is_active', true)->orderBy('name')->get(['id','name']));
        $exams           = Cache::tags(['examenes','catalogos'])->remember('exams:active:v1', now()->addHours(12), fn()=> Exam::where('is_active', true)->orderBy('name')->get(['id','name']));
        $medications     = Cache::tags(['medicamentos','catalogos'])->remember('medications:active:v1', now()->addHours(12), fn()=> Medication::where('is_active', true)->orderBy('name')->get(['id','name']));

        return view('modules.medical_consultations.edit', compact(
            'medicalConsultation', 'doctors', 'specialties', 'laboratoryTests', 'exams', 'medications'
        ));
    }

    public function update(Request $request, MedicalConsultation $medicalConsultation)
    {
        if (!in_array($medicalConsultation->status, ['abierta', 'en_proceso', 'pendiente_evaluacion_medica'])) {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede editar una historia clínica finalizada.');
        }

        $rules = [
            'consultation_date'   => 'required|date',
            'consultation_reason' => 'required|string',
            'final_status'        => 'required|in:egresado,hospitalizado,referido,fallecido',
        ];

        if ($medicalConsultation->doctor_id) {
            $rules['doctor_id']    = 'required|exists:doctors,id';
            $rules['specialty_id'] = 'required|exists:specialties,id';
        }

        $request->validate($rules + [
            'medical_diagnosis'            => 'nullable|string',
            'prescribed_treatment'         => 'nullable|string',
            'emergency_trauma_assessment'  => 'nullable|string',
            'consultation_physical_exam'   => 'nullable|string',
            'reference_contrareference'    => 'nullable|string',
            'control_type_id'              => 'nullable|exists:control_types,id',
            'is_new_patient'               => 'nullable|boolean',
            'has_igss'                     => 'nullable|boolean',
            'gestation_weeks'              => 'nullable|integer|min:1|max:42',
            'was_referred'                 => 'nullable|boolean',
            'comes_counter_referred'       => 'nullable|boolean',
            'comes_referred'               => 'nullable|boolean',
            'was_counter_referred'         => 'nullable|boolean',
            'reference_destination'        => 'nullable|string',
            'reference_reason'             => 'nullable|string',
            'sigsa_observations'           => 'nullable|string',
            'laboratory_test_ids'          => 'nullable|array',
            'laboratory_test_ids.*'        => 'exists:laboratory_tests,id',
            'exam_ids'                     => 'nullable|array',
            'exam_ids.*'                   => 'exists:exams,id',
            'medication_ids'               => 'nullable|array',
            'medication_ids.*'             => 'exists:medications,id',
            'companion_name'               => 'nullable|string|max:100',
            'companion_phone'              => 'nullable|string|max:8',
            'companion_email'              => 'nullable|email|max:100',
            'companion_dpi'                => 'nullable|string|max:13',
            'companion_relationship_id'    => 'required_with:companion_name|exists:companion_relationships,id',
            'guardian_name'                => 'nullable|string|max:100',
            'guardian_phone'               => 'nullable|string|max:8',
            'guardian_email'               => 'nullable|email|max:100',
            'guardian_dpi'                 => 'nullable|string|max:13',
            'guardian_relationship_id'     => 'required_with:guardian_name|exists:companion_relationships,id',
            'guardian_address'             => 'nullable|string|max:200',
            'emergency_contact'            => 'nullable|string|max:8',
            'is_pregnant'                  => 'nullable|boolean',
            'last_menstrual_period'        => 'nullable|date',
            'menstrual_cycle'              => 'nullable|integer|min:20|max:35',
            'pregnancies_count'            => 'nullable|integer|min:0',
            'births_count'                 => 'nullable|integer|min:0',
            'abortions_count'              => 'nullable|integer|min:0',
            'cesareans_count'              => 'nullable|integer|min:0',
            'contraceptive_method_id'      => 'nullable|exists:contraceptive_methods,id',
            'gynecological_history'        => 'nullable|string',
            'birth_weight'                 => 'nullable|numeric|min:0.5|max:10',
            'current_weight'               => 'nullable|numeric|min:0.5|max:200',
            'current_height'               => 'nullable|numeric|min:30|max:250',
            'head_circumference'           => 'nullable|numeric|min:20|max:70',
            'vaccination_status'           => 'nullable|string|max:50',
            'feeding_type'                 => 'nullable|string|max:50',
            'development_milestones'       => 'nullable|string|max:50',
            'pediatric_history'            => 'nullable|string',
            'parent_instructions'          => 'nullable|string',
            'hospital_service'             => 'nullable|string|max:100',
            'death_date'                   => 'nullable|date',
            'death_cause'                  => 'nullable|string|max:200',
            'patient_status_id'            => 'required|exists:patient_statuses,id',
        ], [
            'companion_relationship_id.required_with' => 'Debe seleccionar la relación del acompañante cuando se especifica el nombre.',
            'guardian_relationship_id.required_with'  => 'Debe seleccionar la relación del tutor/guardián cuando se especifica el nombre.',
            'companion_phone.max'                     => 'El teléfono del acompañante no puede tener más de 8 caracteres.',
            'companion_dpi.max'                       => 'El DPI del acompañante no puede tener más de 13 caracteres.',
            'guardian_phone.max'                      => 'El teléfono del tutor/guardián no puede tener más de 8 caracteres.',
            'guardian_dpi.max'                        => 'El DPI del tutor/guardián no puede tener más de 13 caracteres.',
            'emergency_contact.max'                   => 'El contacto de emergencia no puede tener más de 8 caracteres.',
            'final_status.required'                   => 'Debe seleccionar un estado final para la consulta.',
            'patient_status_id.required'              => 'Debe seleccionar un estado del paciente.',
        ]);

        $data = $request->except(['laboratory_test_ids', 'exam_ids', 'medication_ids']);
        $data['consultation_date'] = \Carbon\Carbon::parse($request->consultation_date);

        if ($request->patient_status_id) {
            $patientStatus = \App\Models\PatientStatus::find($request->patient_status_id);
            if ($patientStatus && strtolower($patientStatus->name) === 'fallecido') {
                $data['final_status'] = 'fallecido';
            }
        }

        $data['is_new_patient']        = $request->has('is_new_patient');
        $data['has_igss']              = $request->has('has_igss');
        $data['was_referred']          = $request->has('was_referred');
        $data['comes_counter_referred']= $request->has('comes_counter_referred');
        $data['comes_referred']        = $request->has('comes_referred');
        $data['was_counter_referred']  = $request->has('was_counter_referred');

        $data['status'] = 'finalizada';

        $medicalConsultation->update($data);

        $medicalConsultation->laboratoryTests()->sync($request->laboratory_test_ids ?? []);
        $medicalConsultation->exams()->sync($request->exam_ids ?? []);
        $medicalConsultation->medications()->sync($request->medication_ids ?? []);

        Cache::tags(['consultas'])->flush();

        return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)->with('success', [
            'title'   => 'Historia Clínica Finalizada',
            'message' => 'La historia clínica se ha finalizado correctamente. Estado: ' . ucfirst($request->final_status)
        ]);
    }

    public function destroy(MedicalConsultation $medicalConsultation)
    {
        if ($medicalConsultation->status !== 'abierta') {
            return redirect()->route('clinical-records.show', $medicalConsultation->clinical_record_id)
                ->with('error', 'No se puede eliminar una historia clínica que ya ha sido procesada.');
        }

        $consultationId   = $medicalConsultation->id;
        $clinicalRecordId = $medicalConsultation->clinical_record_id;

        $medicalConsultation->delete();

        Cache::tags(['consultas'])->flush();

        return redirect()->route('clinical-records.show', $clinicalRecordId)->with('toast', [
            'type'    => 'warning',
            'title'   => 'Eliminación Éxitosa',
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

    private function determineProcessRoute(MedicalConsultation $medicalConsultation)
    {
        if (!$medicalConsultation->doctor_id) {
            return 'medical-consultations.process-nursing';
        }

        $clinicalRecord = $medicalConsultation->clinicalRecord;
        $specialty      = $medicalConsultation->specialty;
        $age            = $clinicalRecord->birth_date ? $clinicalRecord->birth_date->age : 0;

        if ($specialty && (
            stripos($specialty->name, 'ginec') !== false ||
            stripos($specialty->name, 'obstet') !== false ||
            strtolower($specialty->name) === 'ginecoobstetricia'
        )) {
            return 'medical-consultations.process-gynecological';
        }

        if ($age < 18) {
            return 'medical-consultations.process-pediatric';
        }

        return 'medical-consultations.process-adult';
    }

    private function determineHospitalService(MedicalConsultation $medicalConsultation)
    {
        $clinicalRecord = $medicalConsultation->clinicalRecord;
        $specialty      = $medicalConsultation->specialty;

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

    /**
     * Formatear datos de enfermería para almacenar
     */
    private function formatNursingData($request)
    {
        $nursingData = [
            'signos_vitales' => [
                'presion_arterial' => $request->presion_arterial,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'temperatura' => $request->temperatura,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'peso' => $request->peso,
                'talla' => $request->talla,
                'saturacion_o2' => $request->saturacion_o2,
                'glicemia' => $request->glicemia,
            ],
            'acompanante' => [
                'nombre' => $request->companion_name,
                'parentesco_id' => $request->companion_relationship_id,
                'telefono' => $request->companion_phone,
                'email' => $request->companion_email,
                'dpi' => $request->companion_dpi,
                'direccion' => $request->companion_address,
            ],
            'notas_adicionales' => $request->notas_adicionales,
            'nursing_note' => $request->nursing_note,
            'fecha_registro' => now()->format('Y-m-d H:i:s'),
            'registrado_por' => auth()->user()->name,
        ];

        return json_encode($nursingData, JSON_UNESCAPED_UNICODE);
    }
}