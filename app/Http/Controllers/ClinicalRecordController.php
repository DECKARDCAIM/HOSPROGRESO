<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\Sex;
use App\Models\CivilStatus;
use App\Models\LinguisticCommunity;
use App\Models\Ethnicity;
use App\Models\Disability;
use App\Models\Allergy;
use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ClinicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();
        $linguisticCommunities = LinguisticCommunity::orderBy('name')->get();
        $ethnicities = Ethnicity::orderBy('name')->get();
        $sexes = Sex::orderBy('name')->get();
        $civilStatuses = CivilStatus::orderBy('name')->get();

        $clinicalRecords = ClinicalRecord::with(['sex', 'civilStatus', 'country', 'department', 'municipality'])
            ->when($request->q, function ($query, $q) {
                $query->where(function($q2) use ($q) {
                    $q2->where('record_number', 'like', "%$q%")
                        ->orWhere('first_name', 'like', "%$q%")
                        ->orWhere('second_name', 'like', "%$q%")
                        ->orWhere('third_name', 'like', "%$q%")
                        ->orWhere('first_lastname', 'like', "%$q%")
                        ->orWhere('second_lastname', 'like', "%$q%")
                        ->orWhere('married_lastname', 'like', "%$q%")
                        ->orWhere('cui', 'like', "%$q%")
                    ;
                });
            })
            ->when($request->country_id, fn($q, $id) => $q->where('country_id', $id))
            ->when($request->department_id, fn($q, $id) => $q->where('department_id', $id))
            ->when($request->municipality_id, fn($q, $id) => $q->where('municipality_id', $id))
            ->when($request->linguistic_community_id, fn($q, $id) => $q->where('linguistic_community_id', $id))
            ->when($request->ethnicity_id, fn($q, $id) => $q->where('ethnicity_id', $id))
            ->when($request->sex_id, fn($q, $id) => $q->where('sex_id', $id))
            ->when($request->civil_status_id, fn($q, $id) => $q->where('civil_status_id', $id))
            ->when($request->birth_date, fn($q, $date) => $q->whereDate('birth_date', $date))
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->appends($request->all());

        return view('modules.clinical_records.index', compact(
            'clinicalRecords',
            'countries',
            'departments',
            'municipalities',
            'linguisticCommunities',
            'ethnicities',
            'sexes',
            'civilStatuses'
        ));
    }

    public function create()
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

        return view('modules.clinical_records.create', compact(
            'sexes', 'civilStatuses', 'linguisticCommunities', 'ethnicities',
            'disabilities', 'allergies', 'countries', 'departments', 'municipalities'
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
            'cui' => 'required|string|max:13|unique:clinical_records,cui',
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

        // Generar número de expediente único
        $recordNumber = 'EXP-' . date('Y') . '-' . str_pad(ClinicalRecord::count() + 1, 6, '0', STR_PAD_LEFT);
        $data = $request->except(['disability_id', 'allergy_id']);
        $data['record_number'] = $recordNumber;

        $clinicalRecord = ClinicalRecord::create($data);
        $clinicalRecord->disabilities()->sync($request->disability_id ?? []);
        $clinicalRecord->allergies()->sync($request->allergy_id ?? []);

        // Crear notificación
        NotificationService::notifyCreate(
            'Expediente Clínico',
            $clinicalRecord->record_number . ' - ' . $clinicalRecord->first_name . ' ' . $clinicalRecord->first_lastname
        );

        return redirect()->route('clinical-records.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El expediente clínico ' . $clinicalRecord->record_number . ' se ha creado correctamente.'
            ]);
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
            'cui' => 'required|string|max:13|unique:clinical_records,cui,' . $clinicalRecord->id,
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

        // Crear notificación de actualización
        NotificationService::notifyUpdate(
            'Expediente Clínico',
            $clinicalRecord->record_number . ' - ' . $clinicalRecord->first_name . ' ' . $clinicalRecord->first_lastname
        );

        return redirect()->route('clinical-records.index')
            ->with('toast', [
                'type' => 'info',
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

        return view('modules.clinical_records.show', compact('clinicalRecord'));
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
        
        return $pdf->stream('expediente_'.$clinicalRecord->record_number.'.pdf');
    }
} 