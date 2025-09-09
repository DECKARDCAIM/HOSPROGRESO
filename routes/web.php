<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Imports de Controllers
use App\Http\Controllers\AllergyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CivilStatusController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\CompanionRelationshipController;
use App\Http\Controllers\ContraceptiveMethodController;
use App\Http\Controllers\ControlTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisabilityController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EthnicityController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboratoryTestController;
use App\Http\Controllers\LinguisticCommunityController;
use App\Http\Controllers\MedicalConsultationController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PatientStatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleTypeController;
use App\Http\Controllers\SexController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorSubstitutionController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ClinicalFileController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Cache;
// Nota: Horizon se usa condicionalmente más abajo para evitar errores si no está instalado

/*
|--------------------------------------------------------------------------
| RUTAS PRINCIPALES Y AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

// Ruta raíz - Redirige según estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('panel');
    }
    return redirect()->route('login');
})->middleware('web');

// Rutas de autenticación de Laravel (registro y reset de contraseña deshabilitados)
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// Panel principal
Route::get('/panel', [HomeController::class, 'index'])
    ->name('panel')
    ->middleware('auth');

// Ruta 'home' (alias para panel)
Route::get('/home', [HomeController::class, 'index'])
    ->name('home')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| RUTAS DE PERFIL DE USUARIO
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/perfil/editar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::put('/perfil/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::delete('/perfil/photo', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');
    Route::put('/perfil/banner', [ProfileController::class, 'updateBanner'])->name('profile.updateBanner');
    Route::delete('/perfil/banner', [ProfileController::class, 'deleteBanner'])->name('profile.deleteBanner');
    Route::delete('/perfil/sesion/{session_id}', [ProfileController::class, 'logoutSession'])->name('profile.logoutSession');
});

// Ruta Horizon protegida (solo si el paquete está instalado)
if (class_exists(\Laravel\Horizon\Horizon::class)) {
    Route::middleware(['web','auth'])->group(function () {
        \Laravel\Horizon\Horizon::auth(function ($request) {
            return auth()->check();
        });
    });
}

// Eliminado endpoint API de estadísticas por requerimiento (no usar APIs)

// Rutas de notificaciones eliminadas por requerimiento de performance

/*
|--------------------------------------------------------------------------
| RUTAS DE UBICACIÓN GEOGRÁFICA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Países - Rutas individuales con permisos específicos
    Route::get('paises', [CountryController::class, 'index'])
        ->name('paises.index')
        ->middleware('permission:paises.ver');
    Route::get('paises/create', [CountryController::class, 'create'])
        ->name('paises.create')
        ->middleware('permission:paises.crear');
    Route::post('paises', [CountryController::class, 'store'])
        ->name('paises.store')
        ->middleware('permission:paises.crear');
    Route::get('paises/{country}', [CountryController::class, 'show'])
        ->name('paises.show')
        ->middleware('permission:paises.ver');
    Route::get('paises/{country}/edit', [CountryController::class, 'edit'])
        ->name('paises.edit')
        ->middleware('permission:paises.editar');
    Route::put('paises/{country}', [CountryController::class, 'update'])
        ->name('paises.update')
        ->middleware('permission:paises.editar');
    Route::delete('paises/{country}', [CountryController::class, 'destroy'])
        ->name('paises.destroy')
        ->middleware('permission:paises.eliminar');
    
    // Departamentos - Rutas individuales con permisos específicos
    Route::get('departamentos', [DepartmentController::class, 'index'])
        ->name('departamentos.index')
        ->middleware('permission:departamentos.ver');
    Route::get('departamentos/create', [DepartmentController::class, 'create'])
        ->name('departamentos.create')
        ->middleware('permission:departamentos.crear');
    Route::post('departamentos', [DepartmentController::class, 'store'])
        ->name('departamentos.store')
        ->middleware('permission:departamentos.crear');
    Route::get('departamentos/{department}', [DepartmentController::class, 'show'])
        ->name('departamentos.show')
        ->middleware('permission:departamentos.ver');
    Route::get('departamentos/{department}/edit', [DepartmentController::class, 'edit'])
        ->name('departamentos.edit')
        ->middleware('permission:departamentos.editar');
    Route::put('departamentos/{department}', [DepartmentController::class, 'update'])
        ->name('departamentos.update')
        ->middleware('permission:departamentos.editar');
    Route::delete('departamentos/{department}', [DepartmentController::class, 'destroy'])
        ->name('departamentos.destroy')
        ->middleware('permission:departamentos.eliminar');
    
    // Municipios - Rutas individuales con permisos específicos
    Route::get('municipios', [MunicipalityController::class, 'index'])
        ->name('municipios.index')
        ->middleware('permission:municipios.ver');
    Route::get('municipios/create', [MunicipalityController::class, 'create'])
        ->name('municipios.create')
        ->middleware('permission:municipios.crear');
    Route::post('municipios', [MunicipalityController::class, 'store'])
        ->name('municipios.store')
        ->middleware('permission:municipios.crear');
    Route::get('municipios/{municipality}', [MunicipalityController::class, 'show'])
        ->name('municipios.show')
        ->middleware('permission:municipios.ver');
    Route::get('municipios/{municipality}/edit', [MunicipalityController::class, 'edit'])
        ->name('municipios.edit')
        ->middleware('permission:municipios.editar');
    Route::put('municipios/{municipality}', [MunicipalityController::class, 'update'])
        ->name('municipios.update')
        ->middleware('permission:municipios.editar');
    Route::delete('municipios/{municipality}', [MunicipalityController::class, 'destroy'])
        ->name('municipios.destroy')
        ->middleware('permission:municipios.eliminar');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE CATÁLOGOS MÉDICOS (PROTEGIDAS CON PERMISOS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Especialidades médicas - Rutas individuales con permisos específicos
    Route::get('especialidades', [SpecialtyController::class, 'index'])
        ->name('especialidades.index')
        ->middleware('permission:especialidades.ver');
    Route::get('especialidades/create', [SpecialtyController::class, 'create'])
        ->name('especialidades.create')
        ->middleware('permission:especialidades.crear');
    Route::post('especialidades', [SpecialtyController::class, 'store'])
        ->name('especialidades.store')
        ->middleware('permission:especialidades.crear');
    Route::get('especialidades/{specialty}', [SpecialtyController::class, 'show'])
        ->name('especialidades.show')
        ->middleware('permission:especialidades.ver');
    Route::get('especialidades/{specialty}/edit', [SpecialtyController::class, 'edit'])
        ->name('especialidades.edit')
        ->middleware('permission:especialidades.editar');
    Route::put('especialidades/{specialty}', [SpecialtyController::class, 'update'])
        ->name('especialidades.update')
        ->middleware('permission:especialidades.editar');
    Route::delete('especialidades/{specialty}', [SpecialtyController::class, 'destroy'])
        ->name('especialidades.destroy')
        ->middleware('permission:especialidades.eliminar');
    
    // Tipos de horario - Rutas individuales con permisos específicos
    Route::get('schedule-types', [ScheduleTypeController::class, 'index'])
        ->name('schedule-types.index')
        ->middleware('permission:tipos_horario.ver');
    Route::get('schedule-types/create', [ScheduleTypeController::class, 'create'])
        ->name('schedule-types.create')
        ->middleware('permission:tipos_horario.crear');
    Route::post('schedule-types', [ScheduleTypeController::class, 'store'])
        ->name('schedule-types.store')
        ->middleware('permission:tipos_horario.crear');
    Route::get('schedule-types/{scheduleType}', [ScheduleTypeController::class, 'show'])
        ->name('schedule-types.show')
        ->middleware('permission:tipos_horario.ver');
    Route::get('schedule-types/{scheduleType}/edit', [ScheduleTypeController::class, 'edit'])
        ->name('schedule-types.edit')
        ->middleware('permission:tipos_horario.editar');
    Route::put('schedule-types/{scheduleType}', [ScheduleTypeController::class, 'update'])
        ->name('schedule-types.update')
        ->middleware('permission:tipos_horario.editar');
    Route::delete('schedule-types/{scheduleType}', [ScheduleTypeController::class, 'destroy'])
        ->name('schedule-types.destroy')
        ->middleware('permission:tipos_horario.eliminar');
    
    // Ruta AJAX para obtener tipos de horario por especialidad
    Route::post('schedule-types/get-by-specialty', [ScheduleTypeController::class, 'getBySpecialty'])
        ->name('schedule-types.get-by-specialty')
        ->middleware('permission:tipos_horario.ver');
    
    // Sustituciones de doctores - Rutas individuales con permisos específicos
    Route::get('doctor-substitutions', [DoctorSubstitutionController::class, 'index'])
        ->name('doctor-substitutions.index')
        ->middleware('permission:doctores.sustituciones.ver');
    Route::get('doctor-substitutions/create', [DoctorSubstitutionController::class, 'create'])
        ->name('doctor-substitutions.create')
        ->middleware('permission:doctores.sustituciones.crear');
    Route::post('doctor-substitutions', [DoctorSubstitutionController::class, 'store'])
        ->name('doctor-substitutions.store')
        ->middleware('permission:doctores.sustituciones.crear');
    Route::get('doctor-substitutions/{doctorSubstitution}', [DoctorSubstitutionController::class, 'show'])
        ->name('doctor-substitutions.show')
        ->middleware('permission:doctores.sustituciones.ver');
    Route::post('doctor-substitutions/{doctorSubstitution}/complete', [DoctorSubstitutionController::class, 'complete'])
        ->name('doctor-substitutions.complete')
        ->middleware('permission:doctores.sustituciones.editar');
    Route::post('doctor-substitutions/{doctorSubstitution}/cancel', [DoctorSubstitutionController::class, 'cancel'])
        ->name('doctor-substitutions.cancel')
        ->middleware('permission:doctores.sustituciones.editar');
    Route::post('doctor-substitutions/{doctorSubstitution}/assign-new-doctor', [DoctorSubstitutionController::class, 'assignNewDoctor'])
        ->name('doctor-substitutions.assign-new-doctor')
        ->middleware('permission:doctores.sustituciones.editar');
    
    // Ruta AJAX para obtener doctores disponibles para sustitución
    Route::post('doctor-substitutions/get-available-doctors', [DoctorSubstitutionController::class, 'getAvailableDoctors'])
        ->name('doctor-substitutions.get-available-doctors')
        ->middleware('permission:doctores.sustituciones.ver');
    
    // Tipos de control - Rutas individuales con permisos específicos
    Route::get('control-types', [ControlTypeController::class, 'index'])
        ->name('control-types.index')
        ->middleware('permission:tipos_control.ver');
    Route::get('control-types/create', [ControlTypeController::class, 'create'])
        ->name('control-types.create')
        ->middleware('permission:tipos_control.crear');
    Route::post('control-types', [ControlTypeController::class, 'store'])
        ->name('control-types.store')
        ->middleware('permission:tipos_control.crear');
    Route::get('control-types/{controlType}', [ControlTypeController::class, 'show'])
        ->name('control-types.show')
        ->middleware('permission:tipos_control.ver');
    Route::get('control-types/{controlType}/edit', [ControlTypeController::class, 'edit'])
        ->name('control-types.edit')
        ->middleware('permission:tipos_control.editar');
    Route::put('control-types/{controlType}', [ControlTypeController::class, 'update'])
        ->name('control-types.update')
        ->middleware('permission:tipos_control.editar');
    Route::delete('control-types/{controlType}', [ControlTypeController::class, 'destroy'])
        ->name('control-types.destroy')
        ->middleware('permission:tipos_control.eliminar');
    
    // Sexos - Rutas individuales con permisos específicos
    Route::get('sexes', [SexController::class, 'index'])
        ->name('sexes.index')
        ->middleware('permission:sexos.ver');
    Route::get('sexes/create', [SexController::class, 'create'])
        ->name('sexes.create')
        ->middleware('permission:sexos.crear');
    Route::post('sexes', [SexController::class, 'store'])
        ->name('sexes.store')
        ->middleware('permission:sexos.crear');
    Route::get('sexes/{sex}', [SexController::class, 'show'])
        ->name('sexes.show')
        ->middleware('permission:sexos.ver');
    Route::get('sexes/{sex}/edit', [SexController::class, 'edit'])
        ->name('sexes.edit')
        ->middleware('permission:sexos.editar');
    Route::put('sexes/{sex}', [SexController::class, 'update'])
        ->name('sexes.update')
        ->middleware('permission:sexos.editar');
    Route::delete('sexes/{sex}', [SexController::class, 'destroy'])
        ->name('sexes.destroy')
        ->middleware('permission:sexos.eliminar');
    
    // Estados civiles - Rutas individuales con permisos específicos
    Route::get('civil-statuses', [CivilStatusController::class, 'index'])
        ->name('civil-statuses.index')
        ->middleware('permission:estados_civiles.ver');
    Route::get('civil-statuses/create', [CivilStatusController::class, 'create'])
        ->name('civil-statuses.create')
        ->middleware('permission:estados_civiles.crear');
    Route::post('civil-statuses', [CivilStatusController::class, 'store'])
        ->name('civil-statuses.store')
        ->middleware('permission:estados_civiles.crear');
    Route::get('civil-statuses/{civilStatus}', [CivilStatusController::class, 'show'])
        ->name('civil-statuses.show')
        ->middleware('permission:estados_civiles.ver');
    Route::get('civil-statuses/{civilStatus}/edit', [CivilStatusController::class, 'edit'])
        ->name('civil-statuses.edit')
        ->middleware('permission:estados_civiles.editar');
    Route::put('civil-statuses/{civilStatus}', [CivilStatusController::class, 'update'])
        ->name('civil-statuses.update')
        ->middleware('permission:estados_civiles.editar');
    Route::delete('civil-statuses/{civilStatus}', [CivilStatusController::class, 'destroy'])
        ->name('civil-statuses.destroy')
        ->middleware('permission:estados_civiles.eliminar');
    
    // Comunidades lingüísticas - Rutas individuales con permisos específicos
    Route::get('linguistic-communities', [LinguisticCommunityController::class, 'index'])
        ->name('linguistic-communities.index')
        ->middleware('permission:comunidades_linguisticas.ver');
    Route::get('linguistic-communities/create', [LinguisticCommunityController::class, 'create'])
        ->name('linguistic-communities.create')
        ->middleware('permission:comunidades_linguisticas.crear');
    Route::post('linguistic-communities', [LinguisticCommunityController::class, 'store'])
        ->name('linguistic-communities.store')
        ->middleware('permission:comunidades_linguisticas.crear');
    Route::get('linguistic-communities/{linguisticCommunity}', [LinguisticCommunityController::class, 'show'])
        ->name('linguistic-communities.show')
        ->middleware('permission:comunidades_linguisticas.ver');
    Route::get('linguistic-communities/{linguisticCommunity}/edit', [LinguisticCommunityController::class, 'edit'])
        ->name('linguistic-communities.edit')
        ->middleware('permission:comunidades_linguisticas.editar');
    Route::put('linguistic-communities/{linguisticCommunity}', [LinguisticCommunityController::class, 'update'])
        ->name('linguistic-communities.update')
        ->middleware('permission:comunidades_linguisticas.editar');
    Route::delete('linguistic-communities/{linguisticCommunity}', [LinguisticCommunityController::class, 'destroy'])
        ->name('linguistic-communities.destroy')
        ->middleware('permission:comunidades_linguisticas.eliminar');
    
    // Etnias - Rutas individuales con permisos específicos
    Route::get('ethnicities', [EthnicityController::class, 'index'])
        ->name('ethnicities.index')
        ->middleware('permission:etnias.ver');
    Route::get('ethnicities/create', [EthnicityController::class, 'create'])
        ->name('ethnicities.create')
        ->middleware('permission:etnias.crear');
    Route::post('ethnicities', [EthnicityController::class, 'store'])
        ->name('ethnicities.store')
        ->middleware('permission:etnias.crear');
    Route::get('ethnicities/{ethnicity}', [EthnicityController::class, 'show'])
        ->name('ethnicities.show')
        ->middleware('permission:etnias.ver');
    Route::get('ethnicities/{ethnicity}/edit', [EthnicityController::class, 'edit'])
        ->name('ethnicities.edit')
        ->middleware('permission:etnias.editar');
    Route::put('ethnicities/{ethnicity}', [EthnicityController::class, 'update'])
        ->name('ethnicities.update')
        ->middleware('permission:etnias.editar');
    Route::delete('ethnicities/{ethnicity}', [EthnicityController::class, 'destroy'])
        ->name('ethnicities.destroy')
        ->middleware('permission:etnias.eliminar');
    
    // Discapacidades - Rutas individuales con permisos específicos
    Route::get('disabilities', [DisabilityController::class, 'index'])
        ->name('disabilities.index')
        ->middleware('permission:discapacidades.ver');
    Route::get('disabilities/create', [DisabilityController::class, 'create'])
        ->name('disabilities.create')
        ->middleware('permission:discapacidades.crear');
    Route::post('disabilities', [DisabilityController::class, 'store'])
        ->name('disabilities.store')
        ->middleware('permission:discapacidades.crear');
    Route::get('disabilities/{disability}', [DisabilityController::class, 'show'])
        ->name('disabilities.show')
        ->middleware('permission:discapacidades.ver');
    Route::get('disabilities/{disability}/edit', [DisabilityController::class, 'edit'])
        ->name('disabilities.edit')
        ->middleware('permission:discapacidades.editar');
    Route::put('disabilities/{disability}', [DisabilityController::class, 'update'])
        ->name('disabilities.update')
        ->middleware('permission:discapacidades.editar');
    Route::delete('disabilities/{disability}', [DisabilityController::class, 'destroy'])
        ->name('disabilities.destroy')
        ->middleware('permission:discapacidades.eliminar');
    
    // Alergias - Rutas individuales con permisos específicos
    Route::get('allergies', [AllergyController::class, 'index'])
        ->name('allergies.index')
        ->middleware('permission:alergias.ver');
    Route::get('allergies/create', [AllergyController::class, 'create'])
        ->name('allergies.create')
        ->middleware('permission:alergias.crear');
    Route::post('allergies', [AllergyController::class, 'store'])
        ->name('allergies.store')
        ->middleware('permission:alergias.crear');
    Route::get('allergies/{allergy}', [AllergyController::class, 'show'])
        ->name('allergies.show')
        ->middleware('permission:alergias.ver');
    Route::get('allergies/{allergy}/edit', [AllergyController::class, 'edit'])
        ->name('allergies.edit')
        ->middleware('permission:alergias.editar');
    Route::put('allergies/{allergy}', [AllergyController::class, 'update'])
        ->name('allergies.update')
        ->middleware('permission:alergias.editar');
    Route::delete('allergies/{allergy}', [AllergyController::class, 'destroy'])
        ->name('allergies.destroy')
        ->middleware('permission:alergias.eliminar');
    
    // Pruebas de laboratorio - Rutas individuales con permisos específicos
    Route::get('laboratory-tests', [LaboratoryTestController::class, 'index'])
        ->name('laboratory-tests.index')
        ->middleware('permission:pruebas_laboratorio.ver');
    Route::get('laboratory-tests/create', [LaboratoryTestController::class, 'create'])
        ->name('laboratory-tests.create')
        ->middleware('permission:pruebas_laboratorio.crear');
    Route::post('laboratory-tests', [LaboratoryTestController::class, 'store'])
        ->name('laboratory-tests.store')
        ->middleware('permission:pruebas_laboratorio.crear');
    Route::get('laboratory-tests/{laboratoryTest}', [LaboratoryTestController::class, 'show'])
        ->name('laboratory-tests.show')
        ->middleware('permission:pruebas_laboratorio.ver');
    Route::get('laboratory-tests/{laboratoryTest}/edit', [LaboratoryTestController::class, 'edit'])
        ->name('laboratory-tests.edit')
        ->middleware('permission:pruebas_laboratorio.editar');
    Route::put('laboratory-tests/{laboratoryTest}', [LaboratoryTestController::class, 'update'])
        ->name('laboratory-tests.update')
        ->middleware('permission:pruebas_laboratorio.editar');
    Route::delete('laboratory-tests/{laboratoryTest}', [LaboratoryTestController::class, 'destroy'])
        ->name('laboratory-tests.destroy')
        ->middleware('permission:pruebas_laboratorio.eliminar');
    
    // Exámenes - Rutas individuales con permisos específicos
    Route::get('exams', [ExamController::class, 'index'])
        ->name('exams.index')
        ->middleware('permission:examenes.ver');
    Route::get('exams/create', [ExamController::class, 'create'])
        ->name('exams.create')
        ->middleware('permission:examenes.crear');
    Route::post('exams', [ExamController::class, 'store'])
        ->name('exams.store')
        ->middleware('permission:examenes.crear');
    Route::get('exams/{exam}', [ExamController::class, 'show'])
        ->name('exams.show')
        ->middleware('permission:examenes.ver');
    Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])
        ->name('exams.edit')
        ->middleware('permission:examenes.editar');
    Route::put('exams/{exam}', [ExamController::class, 'update'])
        ->name('exams.update')
        ->middleware('permission:examenes.editar');
    Route::delete('exams/{exam}', [ExamController::class, 'destroy'])
        ->name('exams.destroy')
        ->middleware('permission:examenes.eliminar');
    
    // Medicamentos - Rutas individuales con permisos específicos
    Route::get('medications', [MedicationController::class, 'index'])
        ->name('medications.index')
        ->middleware('permission:medicamentos.ver');
    Route::get('medications/create', [MedicationController::class, 'create'])
        ->name('medications.create')
        ->middleware('permission:medicamentos.crear');
    Route::post('medications', [MedicationController::class, 'store'])
        ->name('medications.store')
        ->middleware('permission:medicamentos.crear');
    Route::get('medications/{medication}', [MedicationController::class, 'show'])
        ->name('medications.show')
        ->middleware('permission:medicamentos.ver');
    Route::get('medications/{medication}/edit', [MedicationController::class, 'edit'])
        ->name('medications.edit')
        ->middleware('permission:medicamentos.editar');
    Route::put('medications/{medication}', [MedicationController::class, 'update'])
        ->name('medications.update')
        ->middleware('permission:medicamentos.editar');
    Route::delete('medications/{medication}', [MedicationController::class, 'destroy'])
        ->name('medications.destroy')
        ->middleware('permission:medicamentos.eliminar');
    
    // Relaciones de acompañantes - Rutas individuales con permisos específicos
    Route::get('companion-relationships', [CompanionRelationshipController::class, 'index'])
        ->name('companion-relationships.index')
        ->middleware('permission:relaciones_acompanantes.ver');
    Route::get('companion-relationships/create', [CompanionRelationshipController::class, 'create'])
        ->name('companion-relationships.create')
        ->middleware('permission:relaciones_acompanantes.crear');
    Route::post('companion-relationships', [CompanionRelationshipController::class, 'store'])
        ->name('companion-relationships.store')
        ->middleware('permission:relaciones_acompanantes.crear');
    Route::get('companion-relationships/{companionRelationship}', [CompanionRelationshipController::class, 'show'])
        ->name('companion-relationships.show')
        ->middleware('permission:relaciones_acompanantes.ver');
    Route::get('companion-relationships/{companionRelationship}/edit', [CompanionRelationshipController::class, 'edit'])
        ->name('companion-relationships.edit')
        ->middleware('permission:relaciones_acompanantes.editar');
    Route::put('companion-relationships/{companionRelationship}', [CompanionRelationshipController::class, 'update'])
        ->name('companion-relationships.update')
        ->middleware('permission:relaciones_acompanantes.editar');
    Route::delete('companion-relationships/{companionRelationship}', [CompanionRelationshipController::class, 'destroy'])
        ->name('companion-relationships.destroy')
        ->middleware('permission:relaciones_acompanantes.eliminar');
    
    // Ruta adicional para reactivar relaciones
    Route::post('companion-relationships/{id}/reactivate', [CompanionRelationshipController::class, 'reactivate'])
        ->name('companion-relationships.reactivate')
        ->middleware('permission:relaciones_acompanantes.editar');
    
    // Métodos anticonceptivos - Rutas individuales con permisos específicos
    Route::get('contraceptive-methods', [ContraceptiveMethodController::class, 'index'])
        ->name('contraceptive-methods.index')
        ->middleware('permission:metodos_anticonceptivos.ver');
    Route::get('contraceptive-methods/create', [ContraceptiveMethodController::class, 'create'])
        ->name('contraceptive-methods.create')
        ->middleware('permission:metodos_anticonceptivos.crear');
    Route::post('contraceptive-methods', [ContraceptiveMethodController::class, 'store'])
        ->name('contraceptive-methods.store')
        ->middleware('permission:metodos_anticonceptivos.crear');
    Route::get('contraceptive-methods/{contraceptiveMethod}', [ContraceptiveMethodController::class, 'show'])
        ->name('contraceptive-methods.show')
        ->middleware('permission:metodos_anticonceptivos.ver');
    Route::get('contraceptive-methods/{contraceptiveMethod}/edit', [ContraceptiveMethodController::class, 'edit'])
        ->name('contraceptive-methods.edit')
        ->middleware('permission:metodos_anticonceptivos.editar');
    Route::put('contraceptive-methods/{contraceptiveMethod}', [ContraceptiveMethodController::class, 'update'])
        ->name('contraceptive-methods.update')
        ->middleware('permission:metodos_anticonceptivos.editar');
    Route::delete('contraceptive-methods/{contraceptiveMethod}', [ContraceptiveMethodController::class, 'destroy'])
        ->name('contraceptive-methods.destroy')
        ->middleware('permission:metodos_anticonceptivos.eliminar');
    
    // Ruta adicional para reactivar métodos anticonceptivos
    Route::post('contraceptive-methods/{id}/reactivate', [ContraceptiveMethodController::class, 'reactivate'])
        ->name('contraceptive-methods.reactivate')
        ->middleware('permission:metodos_anticonceptivos.editar');
    
    // Estados del paciente - Rutas individuales con permisos específicos
    Route::get('patient-statuses', [PatientStatusController::class, 'index'])
        ->name('patient-statuses.index')
        ->middleware('permission:estados_paciente.ver');
    Route::get('patient-statuses/create', [PatientStatusController::class, 'create'])
        ->name('patient-statuses.create')
        ->middleware('permission:estados_paciente.crear');
    Route::post('patient-statuses', [PatientStatusController::class, 'store'])
        ->name('patient-statuses.store')
        ->middleware('permission:estados_paciente.crear');
    Route::get('patient-statuses/{patientStatus}', [PatientStatusController::class, 'show'])
        ->name('patient-statuses.show')
        ->middleware('permission:estados_paciente.ver');
    Route::get('patient-statuses/{patientStatus}/edit', [PatientStatusController::class, 'edit'])
        ->name('patient-statuses.edit')
        ->middleware('permission:estados_paciente.editar');
    Route::put('patient-statuses/{patientStatus}', [PatientStatusController::class, 'update'])
        ->name('patient-statuses.update')
        ->middleware('permission:estados_paciente.editar');
    Route::delete('patient-statuses/{patientStatus}', [PatientStatusController::class, 'destroy'])
        ->name('patient-statuses.destroy')
        ->middleware('permission:estados_paciente.eliminar');
    // Ruta adicional para reactivar estados del paciente
    Route::post('patient-statuses/{id}/reactivate', [PatientStatusController::class, 'reactivate'])
        ->name('patient-statuses.reactivate')
        ->middleware('permission:estados_paciente.editar');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE MÓDULOS PRINCIPALES MÉDICOS (PROTEGIDAS CON PERMISOS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Doctores - Rutas individuales con permisos específicos
    Route::get('doctors', [DoctorController::class, 'index'])
        ->name('doctors.index')
        ->middleware('permission:doctores.ver');
    Route::get('doctors/create', [DoctorController::class, 'create'])
        ->name('doctors.create')
        ->middleware('permission:doctores.crear');
    Route::post('doctors', [DoctorController::class, 'store'])
        ->name('doctors.store')
        ->middleware('permission:doctores.crear');
    Route::get('doctors/{doctor}', [DoctorController::class, 'show'])
        ->name('doctors.show')
        ->middleware('permission:doctores.ver');
    Route::get('doctors/{doctor}/edit', [DoctorController::class, 'edit'])
        ->name('doctors.edit')
        ->middleware('permission:doctores.editar');
    Route::put('doctors/{doctor}', [DoctorController::class, 'update'])
        ->name('doctors.update')
        ->middleware('permission:doctores.editar');
    Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])
        ->name('doctors.destroy')
        ->middleware('permission:doctores.eliminar');
    
    // Días Festivos - Rutas individuales con permisos específicos
    Route::get('holidays', [HolidayController::class, 'index'])
        ->name('holidays.index')
        ->middleware('permission:dias_festivos.ver');
    Route::get('holidays/create', [HolidayController::class, 'create'])
        ->name('holidays.create')
        ->middleware('permission:dias_festivos.crear');
    Route::post('holidays', [HolidayController::class, 'store'])
        ->name('holidays.store')
        ->middleware('permission:dias_festivos.crear');
    Route::get('holidays/{holiday}/edit', [HolidayController::class, 'edit'])
        ->name('holidays.edit')
        ->middleware('permission:dias_festivos.editar');
    Route::put('holidays/{holiday}', [HolidayController::class, 'update'])
        ->name('holidays.update')
        ->middleware('permission:dias_festivos.editar');
    Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])
        ->name('holidays.destroy')
        ->middleware('permission:dias_festivos.eliminar');
    Route::post('holidays/{id}/reactivate', [HolidayController::class, 'reactivate'])
        ->name('holidays.reactivate')
        ->middleware('permission:dias_festivos.reactivar');
    
    
    // Expedientes clínicos - Rutas individuales con permisos específicos
    Route::get('clinical-records', [ClinicalRecordController::class, 'index'])
        ->name('clinical-records.index')
        ->middleware('permission:emergencia.expedientes.ver|consulta_externa.expedientes.ver');
    Route::get('clinical-records/create', [ClinicalRecordController::class, 'create'])
        ->name('clinical-records.create')
        ->middleware('permission:emergencia.expedientes.crear|consulta_externa.expedientes.crear');
    Route::post('clinical-records', [ClinicalRecordController::class, 'store'])
        ->name('clinical-records.store')
        ->middleware('permission:emergencia.expedientes.crear|consulta_externa.expedientes.crear');
    Route::get('clinical-records/{clinicalRecord}', [ClinicalRecordController::class, 'show'])
        ->name('clinical-records.show')
        ->middleware('permission:emergencia.expedientes.ver|consulta_externa.expedientes.ver');
    Route::get('clinical-records/{clinicalRecord}/edit', [ClinicalRecordController::class, 'edit'])
        ->name('clinical-records.edit')
        ->middleware('permission:emergencia.expedientes.editar|consulta_externa.expedientes.editar');
    Route::put('clinical-records/{clinicalRecord}', [ClinicalRecordController::class, 'update'])
        ->name('clinical-records.update')
        ->middleware('permission:emergencia.expedientes.editar|consulta_externa.expedientes.editar');
    Route::delete('clinical-records/{clinicalRecord}', [ClinicalRecordController::class, 'destroy'])
        ->name('clinical-records.destroy')
        ->middleware('permission:emergencia.expedientes.eliminar|consulta_externa.expedientes.eliminar');
    
    // Consultas médicas - Rutas individuales con permisos específicos
    Route::get('medical-consultations', [MedicalConsultationController::class, 'index'])
        ->name('medical-consultations.index')
        ->middleware('permission:emergencia.consultas.ver|consulta_externa.consultas.ver');
    Route::get('medical-consultations/create', [MedicalConsultationController::class, 'create'])
        ->name('medical-consultations.create')
        ->middleware('permission:emergencia.consultas.crear|consulta_externa.consultas.crear');
    Route::post('medical-consultations', [MedicalConsultationController::class, 'store'])
        ->name('medical-consultations.store')
        ->middleware('permission:emergencia.consultas.crear|consulta_externa.consultas.crear');
    Route::get('medical-consultations/{medicalConsultation}', [MedicalConsultationController::class, 'show'])
        ->name('medical-consultations.show')
        ->middleware('permission:emergencia.consultas.ver|consulta_externa.consultas.ver');
    Route::get('medical-consultations/{medicalConsultation}/edit', [MedicalConsultationController::class, 'edit'])
        ->name('medical-consultations.edit')
        ->middleware('permission:emergencia.consultas.editar|consulta_externa.consultas.editar');
    Route::put('medical-consultations/{medicalConsultation}', [MedicalConsultationController::class, 'update'])
        ->name('medical-consultations.update')
        ->middleware('permission:emergencia.consultas.editar|consulta_externa.consultas.editar');
    Route::delete('medical-consultations/{medicalConsultation}', [MedicalConsultationController::class, 'destroy'])
        ->name('medical-consultations.destroy')
        ->middleware('permission:emergencia.consultas.eliminar|consulta_externa.consultas.eliminar');
    
    // Citas médicas - Rutas individuales con permisos específicos
    Route::get('appointments', [AppointmentController::class, 'index'])
        ->name('appointments.index')
        ->middleware('permission:emergencia.citas.ver|consulta_externa.citas.ver');
    Route::get('appointments/create', [AppointmentController::class, 'create'])
        ->name('appointments.create')
        ->middleware('permission:emergencia.citas.crear|consulta_externa.citas.crear');
    Route::post('appointments', [AppointmentController::class, 'store'])
        ->name('appointments.store')
        ->middleware('permission:emergencia.citas.crear|consulta_externa.citas.crear');
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])
        ->name('appointments.show')
        ->middleware('permission:emergencia.citas.ver|consulta_externa.citas.ver');
    Route::get('appointments/{appointment}/edit', [AppointmentController::class, 'edit'])
        ->name('appointments.edit')
        ->middleware('permission:emergencia.citas.editar|consulta_externa.citas.editar');
    Route::put('appointments/{appointment}', [AppointmentController::class, 'update'])
        ->name('appointments.update')
        ->middleware('permission:emergencia.citas.editar|consulta_externa.citas.editar');
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->name('appointments.destroy')
        ->middleware('permission:emergencia.citas.eliminar|consulta_externa.citas.eliminar');
});

/*
|--------------------------------------------------------------------------
| RUTAS ADICIONALES DE CONSULTAS MÉDICAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Proceso general (redirector automático)
    Route::get('medical-consultations/{medicalConsultation}/process', [MedicalConsultationController::class, 'process'])
        ->name('medical-consultations.process');
    
    // Procesos específicos por tipo de paciente/especialidad
    Route::get('medical-consultations/{medicalConsultation}/process-nursing', [MedicalConsultationController::class, 'processNursing'])
        ->name('medical-consultations.process-nursing');
    
    Route::get('medical-consultations/{medicalConsultation}/process-adult', [MedicalConsultationController::class, 'processAdult'])
        ->name('medical-consultations.process-adult');
    
    Route::get('medical-consultations/{medicalConsultation}/process-gynecological', [MedicalConsultationController::class, 'processGynecological'])
        ->name('medical-consultations.process-gynecological');
    
    Route::get('medical-consultations/{medicalConsultation}/process-pediatric', [MedicalConsultationController::class, 'processPediatric'])
        ->name('medical-consultations.process-pediatric');
    
    Route::get('medical-consultations/{medicalConsultation}/print', [MedicalConsultationController::class, 'print'])
        ->name('medical-consultations.print');
});

/*
|--------------------------------------------------------------------------
| RUTAS ADICIONALES DE CITAS MÉDICAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Rutas AJAX para citas
    Route::post('appointments/get-doctors', [AppointmentController::class, 'getDoctorsBySpecialty'])
        ->name('appointments.get-doctors');
    
    Route::post('appointments/get-schedule-types', [AppointmentController::class, 'getScheduleTypesByDoctor'])
        ->name('appointments.get-schedule-types');
    
    Route::post('appointments/get-available-dates', [AppointmentController::class, 'getAvailableDates'])
        ->name('appointments.get-available-dates');
    
    Route::post('appointments/get-next-slot', [AppointmentController::class, 'getNextAvailableSlot'])
        ->name('appointments.get-next-slot');
    
    // Gestión de citas
    Route::put('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');
    
    Route::post('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
        ->name('appointments.reschedule');
    
    Route::get('appointments/{appointment}/print', [AppointmentController::class, 'printPdf'])
        ->name('appointments.print');
    
    Route::post('appointments/print-multiple', [AppointmentController::class, 'printMultiplePdf'])
        ->name('appointments.print-multiple');
});

/*
|--------------------------------------------------------------------------
| RUTAS ADICIONALES DE EXPEDIENTES CLÍNICOS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('clinical-records/{clinicalRecord}/print', [ClinicalRecordController::class, 'printPdf'])
        ->name('clinical-records.print');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE ADMINISTRACIÓN DEL SISTEMA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Gestión de roles - Rutas individuales con permisos específicos
    Route::get('roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('permission:roles.ver');
    Route::get('roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('permission:roles.crear');
    Route::post('roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:roles.crear');
    Route::get('roles/{role}', [RoleController::class, 'show'])
        ->name('roles.show')
        ->middleware('permission:roles.ver');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('permission:roles.editar');
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:roles.editar');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:roles.eliminar');
    
    // Gestión de permisos por rol
    Route::get('roles/{role}/permissions', [PermissionController::class, 'edit'])
        ->name('roles.permissions.edit')
        ->middleware('permission:roles.permisos');
    Route::put('roles/{role}/permissions', [PermissionController::class, 'update'])
        ->name('roles.permissions.update')
        ->middleware('permission:roles.permisos');
    
    // Ruta para página de acceso denegado (sin permisos requeridos)
    Route::get('permission-denied', function () {
        return view('errors.permission-denied');
    })->name('permission.denied');
    
    // Gestión de usuarios - Rutas individuales con permisos específicos
    Route::get('usuarios', [UserController::class, 'index'])
        ->name('usuarios.index')
        ->middleware('permission:usuarios.ver');
    Route::get('usuarios/create', [UserController::class, 'create'])
        ->name('usuarios.create')
        ->middleware('permission:usuarios.crear');
    Route::post('usuarios', [UserController::class, 'store'])
        ->name('usuarios.store')
        ->middleware('permission:usuarios.crear');
    Route::get('usuarios/{user}', [UserController::class, 'show'])
        ->name('usuarios.show')
        ->middleware('permission:usuarios.ver');
    Route::get('usuarios/{user}/edit', [UserController::class, 'edit'])
        ->name('usuarios.edit')
        ->middleware('permission:usuarios.editar');
    Route::put('usuarios/{user}', [UserController::class, 'update'])
        ->name('usuarios.update')
        ->middleware('permission:usuarios.editar');
    Route::delete('usuarios/{user}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy')
        ->middleware('permission:usuarios.eliminar');
    
    Route::put('usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('usuarios.reset-password')
        ->middleware('permission:usuarios.reset_password');
    
    Route::get('usuarios/{user}/print-credentials', [UserController::class, 'printCredentials'])
        ->name('usuarios.print-credentials')
        ->middleware('permission:usuarios.ver');
    
    Route::get('usuarios/{user}/generate-password', [UserController::class, 'generateNewPassword'])
        ->name('usuarios.generate-password')
        ->middleware('permission:usuarios.editar');
    
    /*
    |--------------------------------------------------------------------------
    | RUTAS DE IMPORTACIÓN DE DATOS
    |--------------------------------------------------------------------------
    */
    
    // Módulo de importación y backup - Rutas protegidas con permisos específicos
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])
            ->name('index')
            ->middleware('permission:import.acceso');
        Route::post('/process', [ImportController::class, 'import'])
            ->name('process')
            ->middleware('permission:import.importar');
        
        // Ruta para consultar progreso de importación (AJAX)
        Route::get('/progress', [ImportController::class, 'getProgress'])
            ->name('progress')
            ->middleware('permission:import.acceso');
        
        // Ruta de backup
        Route::get('/backup/full', [ImportController::class, 'generateFullBackup'])
            ->name('backup.full')
            ->middleware('permission:import.importar');
    });

    
});

/*
|--------------------------------------------------------------------------
| RUTAS DE REACTIVACIÓN (SOFT DELETE)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Ubicación geográfica
    Route::post('paises/{id}/reactivate', [CountryController::class, 'reactivate'])
        ->name('paises.reactivate')
        ->middleware('permission:paises.reactivar');
    Route::post('departamentos/{id}/reactivate', [DepartmentController::class, 'reactivate'])
        ->name('departamentos.reactivate')
        ->middleware('permission:departamentos.reactivar');
    Route::post('municipios/{id}/reactivate', [MunicipalityController::class, 'reactivate'])
        ->name('municipios.reactivate')
        ->middleware('permission:municipios.reactivar');
    
    // Catálogos médicos
    Route::post('/especialidades/{id}/reactivate', [SpecialtyController::class, 'reactivate'])->name('especialidades.reactivate');
    Route::post('schedule-types/{id}/reactivate', [ScheduleTypeController::class, 'reactivate'])
        ->name('schedule-types.reactivate')
        ->middleware('permission:tipos_horario.reactivar');
    Route::post('control-types/{id}/reactivate', [ControlTypeController::class, 'reactivate'])->name('control-types.reactivate');
    Route::post('sexes/{id}/reactivate', [SexController::class, 'reactivate'])->name('sexes.reactivate');
    Route::post('civil-statuses/{id}/reactivate', [CivilStatusController::class, 'reactivate'])->name('civil-statuses.reactivate');
    Route::post('linguistic-communities/{id}/reactivate', [LinguisticCommunityController::class, 'reactivate'])->name('linguistic-communities.reactivate');
    Route::post('ethnicities/{id}/reactivate', [EthnicityController::class, 'reactivate'])->name('ethnicities.reactivate');
    Route::post('disabilities/{id}/reactivate', [DisabilityController::class, 'reactivate'])->name('disabilities.reactivate');
    Route::post('allergies/{id}/reactivate', [AllergyController::class, 'reactivate'])->name('allergies.reactivate');
    Route::post('laboratory-tests/{id}/reactivate', [LaboratoryTestController::class, 'reactivate'])->name('laboratory-tests.reactivate');
    Route::post('exams/{id}/reactivate', [ExamController::class, 'reactivate'])->name('exams.reactivate');
    Route::post('medications/{id}/reactivate', [MedicationController::class, 'reactivate'])->name('medications.reactivate');
    
    // Módulos principales
    Route::post('doctors/{id}/reactivate', [DoctorController::class, 'reactivate'])->name('doctors.reactivate');
    
    // Administración
    Route::post('roles/{id}/reactivate', [RoleController::class, 'reactivate'])->name('roles.reactivate');
    Route::post('usuarios/{id}/reactivate', [UserController::class, 'reactivate'])
    ->name('usuarios.reactivate')
    ->middleware('permission:usuarios.reactivar');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE REPORTES (PROTEGIDAS CON PERMISOS)
|--------------------------------------------------------------------------
*/
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reportes.ver');
    Route::post('/sigsa-3h', [ReportController::class, 'generateSigsa'])->name('reports.generate-sigsa')->middleware('permission:reportes.generar');
    Route::post('/preview', [ReportController::class, 'preview'])->name('reports.preview')->middleware('permission:reportes.ver');
    Route::get('/statistics', [ReportController::class, 'statistics'])->name('reports.statistics')->middleware('permission:reportes.ver');
    Route::get('/report-toast', [ReportController::class, 'showReportToast'])->name('reports.show-toast')->middleware('permission:reportes.ver');
});