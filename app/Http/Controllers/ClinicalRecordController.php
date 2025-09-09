<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use App\Models\CivilStatus;
use App\Models\ClinicalRecord;
use App\Models\Country;
use App\Models\Department;
use App\Models\Disability;
use App\Models\Ethnicity;
use App\Models\LinguisticCommunity;
use App\Models\Municipality;
use App\Models\Sex;
use App\Models\TemporaryPatient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClinicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $countries = Cache::tags(['paises', 'catalogos'])->remember('paises:select:v1', now()->addHours(12), fn() => Country::orderBy('name')->get(['id', 'name']));
        $departments = Cache::tags(['departamentos', 'catalogos'])->remember('departamentos:select:v1', now()->addHours(12), fn() => Department::orderBy('name')->get(['id', 'name', 'country_id']));
        $municipalities = Cache::tags(['municipios', 'catalogos'])->remember('municipios:select:v1', now()->addHours(12), fn() => Municipality::orderBy('name')->get(['id', 'name', 'department_id']));
        $linguisticCommunities = Cache::tags(['comunidades_linguisticas', 'catalogos'])->remember('linguistic-communities:select:v1', now()->addHours(12), fn() => LinguisticCommunity::orderBy('name')->get(['id', 'name']));
        $ethnicities = Cache::tags(['etnias', 'catalogos'])->remember('ethnicities:select:v1', now()->addHours(12), fn() => Ethnicity::orderBy('name')->get(['id', 'name']));
        $sexes = Cache::tags(['sexos', 'catalogos'])->remember('sexes:select:v1', now()->addHours(12), fn() => Sex::orderBy('name')->get(['id', 'name']));
        $civilStatuses = Cache::tags(['estados_civiles', 'catalogos'])->remember('civil-statuses:select:v1', now()->addHours(12), fn() => CivilStatus::orderBy('name')->get(['id', 'name']));

        $permanentRecords = ClinicalRecord::select([
            'id',
            'record_number',
            'first_name',
            'second_name',
            'third_name',
            'first_lastname',
            'second_lastname',
            'married_lastname',
            'cui',
            'sex_id',
            'civil_status_id',
            'birth_date',
            'country_id',
            'department_id',
            'municipality_id',
            'specific_residence',
            'created_at',
            DB::raw("'permanent' as record_type"),
            'old_registration_number'
        ])
            ->when($request->q, function ($query, $q) {
                $terms = array_filter(explode(' ', trim($q)));

                $query->where(function ($q2) use ($q, $terms) {
                    $q2
                        ->where('record_number', 'like', "%$q%")
                        ->orWhere('cui', 'like', "%$q%")
                        ->orWhere('old_registration_number', 'like', "%$q%")
                        ->orWhere('specific_residence', 'like', "%$q%");

                    if (count($terms) > 1) {
                        $q2->orWhere(function ($q3) use ($terms) {
                            foreach ($terms as $term) {
                                $q3->where(function ($q4) use ($term) {
                                    $q4
                                        ->where('first_name', 'like', "%$term%")
                                        ->orWhere('second_name', 'like', "%$term%")
                                        ->orWhere('third_name', 'like', "%$term%")
                                        ->orWhere('first_lastname', 'like', "%$term%")
                                        ->orWhere('second_lastname', 'like', "%$term%")
                                        ->orWhere('married_lastname', 'like', "%$term%")
                                        ->orWhere('specific_residence', 'like', "%$term%");
                                });
                            }
                        });
                    } else {
                        $q2
                            ->orWhere('first_name', 'like', "%$q%")
                            ->orWhere('second_name', 'like', "%$q%")
                            ->orWhere('third_name', 'like', "%$q%")
                            ->orWhere('first_lastname', 'like', "%$q%")
                            ->orWhere('second_lastname', 'like', "%$q%")
                            ->orWhere('married_lastname', 'like', "%$q%")
                            ->orWhere('specific_residence', 'like', "%$q%");
                    }
                });
            })
            ->when($request->sex_id, function ($query, $sexId) {
                $query->where('sex_id', $sexId);
            })
            ->when($request->civil_status_id, function ($query, $civilStatusId) {
                $query->where('civil_status_id', $civilStatusId);
            })
            ->when($request->linguistic_community_id, function ($query, $linguisticCommunityId) {
                $query->where('linguistic_community_id', $linguisticCommunityId);
            })
            ->when($request->ethnicity_id, function ($query, $ethnicityId) {
                $query->where('ethnicity_id', $ethnicityId);
            })
            ->when($request->country_id, function ($query, $countryId) {
                $query->where('country_id', $countryId);
            })
            ->when($request->department_id, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($request->municipality_id, function ($query, $municipalityId) {
                $query->where('municipality_id', $municipalityId);
            })
            ->when($request->birth_date, function ($query, $birthDate) {
                $query->whereDate('birth_date', $birthDate);
            });

        $temporaryRecords = TemporaryPatient::select([
            'id',
            'registration_number as record_number',
            'first_name',
            'second_name',
            'third_name',
            'first_lastname',
            'second_lastname',
            'married_lastname',
            'cui',
            'sex_id',
            'civil_status_id',
            'birth_date',
            'country_id',
            'department_id',
            'municipality_id',
            'specific_residence',
            'created_at',
            DB::raw("'temporary' as record_type"),
            'registration_number as old_registration_number'
        ])
            ->where('is_processed', false)
            ->when($request->q, function ($query, $q) {
                $terms = array_filter(explode(' ', trim($q)));

                $query->where(function ($q2) use ($q, $terms) {
                    $q2
                        ->where('registration_number', 'like', "%$q%")
                        ->orWhere('specific_residence', 'like', "%$q%");

                    if (count($terms) > 1) {
                        $q2->orWhere(function ($q3) use ($terms) {
                            foreach ($terms as $term) {
                                $q3->where(function ($q4) use ($term) {
                                    $q4
                                        ->where('first_name', 'like', "%$term%")
                                        ->orWhere('second_name', 'like', "%$term%")
                                        ->orWhere('third_name', 'like', "%$term%")
                                        ->orWhere('first_lastname', 'like', "%$term%")
                                        ->orWhere('second_lastname', 'like', "%$term%")
                                        ->orWhere('married_lastname', 'like', "%$term%")
                                        ->orWhere('specific_residence', 'like', "%$term%");
                                });
                            }
                        });
                    } else {
                        $q2
                            ->orWhere('first_name', 'like', "%$q%")
                            ->orWhere('second_name', 'like', "%$q%")
                            ->orWhere('third_name', 'like', "%$q%")
                            ->orWhere('first_lastname', 'like', "%$q%")
                            ->orWhere('second_lastname', 'like', "%$q%")
                            ->orWhere('married_lastname', 'like', "%$q%")
                            ->orWhere('specific_residence', 'like', "%$q%");
                    }
                });
            })
            ->when($request->sex_id, function ($query, $sexId) {
                $query->where('sex_id', $sexId);
            })
            ->when($request->civil_status_id, function ($query, $civilStatusId) {
                $query->where('civil_status_id', $civilStatusId);
            })
            ->when($request->linguistic_community_id, function ($query, $linguisticCommunityId) {
                $query->where('linguistic_community_id', $linguisticCommunityId);
            })
            ->when($request->ethnicity_id, function ($query, $ethnicityId) {
                $query->where('ethnicity_id', $ethnicityId);
            })
            ->when($request->country_id, function ($query, $countryId) {
                $query->where('country_id', $countryId);
            })
            ->when($request->department_id, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($request->municipality_id, function ($query, $municipalityId) {
                $query->where('municipality_id', $municipalityId);
            })
            ->when($request->birth_date, function ($query, $birthDate) {
                $query->whereDate('birth_date', $birthDate);
            });

        if ($request->record_type === 'permanent') {
            $combinedRecords = $permanentRecords;
        } elseif ($request->record_type === 'temporary') {
            $combinedRecords = $temporaryRecords;
        } else {
            $combinedRecords = $permanentRecords->union($temporaryRecords);
        }

        $page = (int) ($request->query('page', 1));
        $cacheKey = 'clinical-records:index:v1:' . md5(json_encode($request->all()) . ":p={$page}");
        $clinicalRecords = Cache::tags(['expedientes'])->remember($cacheKey, now()->addMinutes(10), function () use ($combinedRecords) {
            return $combinedRecords->orderBy('created_at', 'desc')->paginate(25);
        });
        $clinicalRecords->appends($request->all());

        $stats = [
            'total_permanent' => ClinicalRecord::count(),
            'total_temporary' => TemporaryPatient::notProcessed()->count(),
            'total_combined' => ClinicalRecord::count() + TemporaryPatient::notProcessed()->count()
        ];

        $allDepartments = $departments;
        $allMunicipalities = $municipalities;

        return view('modules.clinical_records.index', compact(
            'clinicalRecords',
            'countries',
            'departments',
            'municipalities',
            'linguisticCommunities',
            'ethnicities',
            'sexes',
            'civilStatuses',
            'stats',
            'allDepartments',
            'allMunicipalities'
        ));
    }

    public function create(Request $request)
    {
        $sexes = Cache::tags(['sexos', 'catalogos'])->remember('sexes:active:v1', now()->addHours(12), fn() => Sex::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $civilStatuses = Cache::tags(['estados_civiles', 'catalogos'])->remember('civil-statuses:active:v1', now()->addHours(12), fn() => CivilStatus::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $linguisticCommunities = Cache::tags(['comunidades_linguisticas', 'catalogos'])->remember('linguistic-communities:active:v1', now()->addHours(12), fn() => LinguisticCommunity::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $ethnicities = Cache::tags(['etnias', 'catalogos'])->remember('ethnicities:active:v1', now()->addHours(12), fn() => Ethnicity::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $disabilities = Cache::tags(['discapacidades', 'catalogos'])->remember('disabilities:active:v1', now()->addHours(12), fn() => Disability::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $allergies = Cache::tags(['alergias', 'catalogos'])->remember('allergies:active:v1', now()->addHours(12), fn() => Allergy::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $countries = Cache::tags(['paises', 'catalogos'])->remember('paises:active:v1', now()->addHours(12), fn() => Country::where('is_active', true)->orderBy('name')->get(['id', 'name']));
        $departments = Cache::tags(['departamentos', 'catalogos'])->remember('departamentos:active:v1', now()->addHours(12), fn() => Department::where('is_active', true)->orderBy('name')->get(['id', 'name', 'country_id']));
        $municipalities = Cache::tags(['municipios', 'catalogos'])->remember('municipios:active:v1', now()->addHours(12), fn() => Municipality::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department_id']));

        $temporaryPatient = null;
        if ($request->has('temporary_id') && !empty($request->temporary_id)) {
            $temporaryPatient = TemporaryPatient::where('id', $request->temporary_id)
                ->where('is_processed', false)
                ->first();
        }

        return view('modules.clinical_records.create', compact(
            'sexes', 'civilStatuses', 'linguisticCommunities', 'ethnicities',
            'disabilities', 'allergies', 'countries', 'departments', 'municipalities',
            'temporaryPatient'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'first_lastname' => 'required|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'married_lastname' => 'nullable|string|max:255',
            'cui' => 'nullable|string|max:13|unique:clinical_records,cui',
            'sex_id' => 'required|exists:sexes,id',
            'civil_status_id' => 'required|exists:civil_statuses,id',
            'linguistic_community_id' => 'required|exists:linguistic_communities,id',
            'ethnicity_id' => 'required|exists:ethnicities,id',
            'birth_date' => 'required|date|before:today',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'department_id' => 'required|exists:departments,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'specific_residence' => 'nullable|string',
            'temporary_patient_id' => 'nullable|exists:temporary_patients,id'
        ]);

        DB::beginTransaction();
        try {
            $recordNumber = 'EXP-' . date('Y') . '-' . str_pad(ClinicalRecord::count() + 1, 6, '0', STR_PAD_LEFT);
            $data = $request->except(['disability_id', 'allergy_id', 'temporary_patient_id']);
            $data['record_number'] = $recordNumber;

            if ($request->has('temporary_patient_id') && !empty($request->temporary_patient_id)) {
                $temporaryPatient = TemporaryPatient::find($request->temporary_patient_id);
                if ($temporaryPatient) {
                    $data['old_registration_number'] = $temporaryPatient->registration_number;
                }
            }

            $clinicalRecord = ClinicalRecord::create($data);
            $clinicalRecord->disabilities()->sync($request->disability_id ?? []);
            $clinicalRecord->allergies()->sync($request->allergy_id ?? []);

            $migratedMessage = '';
            if ($request->has('temporary_patient_id') && !empty($request->temporary_patient_id)) {
                $temporaryPatient = TemporaryPatient::find($request->temporary_patient_id);
                if ($temporaryPatient) {
                    $temporaryPatient->markAsProcessed();
                    $temporaryPatient->delete();
                    $migratedMessage = ' Los datos temporales han sido migrados exitosamente.';
                }
            }

            DB::commit();

            Cache::tags(['expedientes'])->flush();

            return redirect()
                ->route('clinical-records.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Creación Éxitosa',
                    'message' => 'El expediente clínico ' . $clinicalRecord->record_number . ' se ha creado correctamente.' . $migratedMessage
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear el expediente: ' . $e->getMessage()
                ]);
        }
    }

    public function edit(ClinicalRecord $clinicalRecord)
    {
        $sexes = Sex::where('is_active', true)->orderBy('name')->get();
        $civilStatuses = CivilStatus::where('is_active', true)->orderBy('name')->get();
        $linguisticCommunities = LinguisticCommunity::where('is_active', true)->orderBy('name')->get();
        $ethnicities = Ethnicity::where('is_active', true)->orderBy('name')->get();
        $disabilities = Disability::where('is_active', true)->orderBy('name')->get();
        $allergies = Allergy::where('is_active', true)->orderBy('name')->get();
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();

        return view('modules.clinical_records.edit', compact(
            'clinicalRecord', 'sexes', 'civilStatuses', 'linguisticCommunities', 'ethnicities',
            'disabilities', 'allergies', 'countries', 'departments', 'municipalities'
        ));
    }

    public function update(Request $request, ClinicalRecord $clinicalRecord)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'first_lastname' => 'required|string|max:255',
            'second_lastname' => 'nullable|string|max:255',
            'married_lastname' => 'nullable|string|max:255',
            'cui' => 'nullable|string|max:13|unique:clinical_records,cui,' . $clinicalRecord->id,
            'sex_id' => 'required|exists:sexes,id',
            'civil_status_id' => 'required|exists:civil_statuses,id',
            'linguistic_community_id' => 'required|exists:linguistic_communities,id',
            'ethnicity_id' => 'required|exists:ethnicities,id',
            'birth_date' => 'required|date|before:today',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'department_id' => 'required|exists:departments,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'specific_residence' => 'nullable|string',
        ]);

        $data = $request->except(['disability_id', 'allergy_id']);
        $clinicalRecord->update($data);
        $clinicalRecord->disabilities()->sync($request->disability_id ?? []);
        $clinicalRecord->allergies()->sync($request->allergy_id ?? []);

        Cache::tags(['expedientes'])->flush();

        return redirect()
            ->route('clinical-records.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'El expediente clínico ' . $clinicalRecord->record_number . ' se ha actualizado correctamente.'
            ]);
    }

    public function show($id)
    {
        $clinicalRecord = ClinicalRecord::with([
            'medicalConsultations.doctor.specialty',
            'medicalConsultations.laboratoryTests',
            'medicalConsultations.exams',
            'medicalConsultations.medications',
            'appointments.doctor.specialty',
            'appointments.scheduleType'
        ])->findOrFail($id);

        // Obtener doctores y especialidades para los modales
        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:active:full:v1', now()->addHours(6), function () {
            return \App\Models\Doctor::with('specialty:id,name')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('first_lastname')
                ->get();
        });

            $specialties = Cache::tags(['especialidades','catalogos'])->remember(
                'especialidades:select:v2',
                now()->addHours(12),
                fn()=> \App\Models\Specialty::where('is_active', true)->orderBy('name')->get(['id','name'])
            );

            $companionRelationships = Cache::tags(['relaciones_acompanantes','catalogos'])->remember(
                'relaciones_acompanantes:select:v1',
                now()->addHours(12),
                fn()=> \App\Models\CompanionRelationship::where('is_active', true)->orderBy('name')->get(['id','name'])
            );

            return view('modules.clinical_records.show', compact('clinicalRecord', 'doctors', 'specialties', 'companionRelationships'));
    }

    public function printPdf(ClinicalRecord $clinicalRecord)
    {
        $clinicalRecord->load([
            'sex', 'civilStatus', 'linguisticCommunity', 'ethnicity',
            'medicalConsultations.doctor', 'medicalConsultations.specialty',
            'medicalConsultations.laboratoryTests', 'medicalConsultations.exams', 'medicalConsultations.medications',
            'appointments.doctor.specialty', 'appointments.scheduleType', 'appointments.createdBy'
        ]);

        $pdf = Pdf::loadView('modules.clinical_records.print_pdf', compact('clinicalRecord'))
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

        return $pdf->stream('expediente_' . $clinicalRecord->record_number . '.pdf');
    }
}