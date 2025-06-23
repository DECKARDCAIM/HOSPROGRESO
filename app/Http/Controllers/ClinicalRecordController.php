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
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClinicalRecordController extends Controller
{
    public function index()
    {
        $clinicalRecords = ClinicalRecord::with(['sex', 'civilStatus', 'country', 'department', 'municipality'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('modules.clinical_records.index', compact('clinicalRecords'));
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
            'disability_id' => 'nullable|exists:disabilities,id',
            'allergy_id' => 'nullable|exists:allergies,id',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'department_id' => 'required|exists:departments,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'specific_residence' => 'nullable|string',
        ]);

        // Generar número de expediente único
        $recordNumber = 'EXP-' . date('Y') . '-' . str_pad(ClinicalRecord::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $data = $request->all();
        $data['record_number'] = $recordNumber;

        ClinicalRecord::create($data);

        return redirect()->route('clinical-records.index')
            ->with('success', 'Expediente clínico creado exitosamente.');
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
            'disability_id' => 'nullable|exists:disabilities,id',
            'allergy_id' => 'nullable|exists:allergies,id',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'department_id' => 'required|exists:departments,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'specific_residence' => 'nullable|string',
        ]);

        $clinicalRecord->update($request->all());

        return redirect()->route('clinical-records.index')
            ->with('success', 'Expediente clínico actualizado exitosamente.');
    }

    public function destroy(ClinicalRecord $clinicalRecord)
    {
        // Verificar si tiene historias clínicas asociadas
        if ($clinicalRecord->patients()->count() > 0) {
            return redirect()->route('clinical-records.index')
                ->with('error', 'No se puede eliminar el expediente porque tiene historias clínicas asociadas.');
        }

        $clinicalRecord->delete();

        return redirect()->route('clinical-records.index')
            ->with('success', 'Expediente clínico eliminado exitosamente.');
    }
} 