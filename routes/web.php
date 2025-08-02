<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Imports de Controllers
use App\Http\Controllers\AllergyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CivilStatusController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\ControlTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisabilityController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EthnicityController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboratoryTestController;
use App\Http\Controllers\LinguisticCommunityController;
use App\Http\Controllers\MedicalConsultationController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleTypeController;
use App\Http\Controllers\SexController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ClinicalFileController;

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

// Rutas de autenticación de Laravel
Auth::routes();

// Panel principal
Route::get('/panel', [HomeController::class, 'index'])
    ->name('panel')
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

/*
|--------------------------------------------------------------------------
| RUTAS DE NOTIFICACIONES
|--------------------------------------------------------------------------
*/
Route::prefix('notifications')->middleware('auth')->group(function () {
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread');
    Route::get('/all', [NotificationController::class, 'getAllNotifications'])->name('notifications.all');
    Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE UBICACIÓN GEOGRÁFICA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Países
    Route::resource('paises', CountryController::class)
        ->parameters(['paises' => 'country']);
    
    // Departamentos
    Route::resource('departamentos', DepartmentController::class)
        ->parameters(['departamentos' => 'department']);
    
    // Municipios
    Route::resource('municipios', MunicipalityController::class)
        ->parameters(['municipios' => 'municipality']);
});

/*
|--------------------------------------------------------------------------
| RUTAS DE CATÁLOGOS MÉDICOS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Especialidades médicas
    Route::resource('especialidades', SpecialtyController::class)
        ->parameters(['especialidades' => 'specialty']);
    
    // Tipos de horario
    Route::resource('schedule-types', ScheduleTypeController::class);
    
    // Tipos de control
    Route::resource('control-types', ControlTypeController::class)
        ->parameters(['control-types' => 'controlType']);
    
    // Sexos
    Route::resource('sexes', SexController::class)
        ->parameters(['sexes' => 'sex']);
    
    // Estados civiles
    Route::resource('civil-statuses', CivilStatusController::class)
        ->parameters(['civil-statuses' => 'civilStatus']);
    
    // Comunidades lingüísticas
    Route::resource('linguistic-communities', LinguisticCommunityController::class)
        ->parameters(['linguistic-communities' => 'linguisticCommunity']);
    
    // Etnias
    Route::resource('ethnicities', EthnicityController::class)
        ->parameters(['ethnicities' => 'ethnicity']);
    
    // Discapacidades
    Route::resource('disabilities', DisabilityController::class)
        ->parameters(['disabilities' => 'disability']);
    
    // Alergias
    Route::resource('allergies', AllergyController::class)
        ->parameters(['allergies' => 'allergy']);
    
    // Pruebas de laboratorio
    Route::resource('laboratory-tests', LaboratoryTestController::class)
        ->parameters(['laboratory-tests' => 'laboratoryTest']);
    
    // Exámenes
    Route::resource('exams', ExamController::class)
        ->parameters(['exams' => 'exam']);
    
    // Medicamentos
    Route::resource('medications', MedicationController::class)
        ->parameters(['medications' => 'medication']);
});

/*
|--------------------------------------------------------------------------
| RUTAS DE MÓDULOS PRINCIPALES MÉDICOS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Doctores
    Route::resource('doctors', DoctorController::class)
        ->parameters(['doctors' => 'doctor']);
    
    // Expedientes clínicos
    Route::resource('clinical-records', ClinicalRecordController::class)
        ->parameters(['clinical-records' => 'clinicalRecord']);
    
    // Consultas médicas
    Route::resource('medical-consultations', MedicalConsultationController::class)
        ->parameters(['medical-consultations' => 'medicalConsultation']);
    
    // Citas médicas
    Route::resource('appointments', AppointmentController::class);
});

/*
|--------------------------------------------------------------------------
| RUTAS ADICIONALES DE CONSULTAS MÉDICAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Sistema de pasos para consultas médicas
    Route::get('medical-consultations/{medicalConsultation}/process', [MedicalConsultationController::class, 'process'])
        ->name('medical-consultations.process');
    
    Route::post('medical-consultations/{medicalConsultation}/update-process', [MedicalConsultationController::class, 'updateProcess'])
        ->name('medical-consultations.update-process');
    
    Route::post('medical-consultations/{medicalConsultation}/finalize', [MedicalConsultationController::class, 'finalize'])
        ->name('medical-consultations.finalize');
    
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
    // Gestión de roles
    Route::resource('roles', RoleController::class)
        ->parameters(['roles' => 'role']);
    
    // Gestión de usuarios
    Route::resource('usuarios', UserController::class)
        ->parameters(['usuarios' => 'user']);
    
    Route::put('usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('usuarios.reset-password');
    
    /*
    |--------------------------------------------------------------------------
    | RUTAS DE IMPORTACIÓN DE DATOS
    |--------------------------------------------------------------------------
    */
    
    // Módulo de importación y backup
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/process', [ImportController::class, 'import'])->name('process');
        
        // Ruta de backup
        Route::get('/backup/full', [ImportController::class, 'generateFullBackup'])->name('backup.full');
    });
    
    /*
    |--------------------------------------------------------------------------
    | RUTAS DE ARCHIVO CLÍNICO
    |--------------------------------------------------------------------------
    */
    
    // Módulo de archivo clínico
    Route::prefix('clinical-file')->name('clinical-file.')->group(function () {
        Route::get('/', [ClinicalFileController::class, 'index'])->name('index');
        Route::get('/archived', [ClinicalFileController::class, 'archived'])->name('archived');
        Route::get('/{id}', [ClinicalFileController::class, 'show'])->name('show');
        Route::post('/{id}/mark-printed', [ClinicalFileController::class, 'markAsPrinted'])->name('mark-printed');
        Route::post('/{id}/archive', [ClinicalFileController::class, 'markAsArchived'])->name('archive');
        Route::post('/{id}/reactivate', [ClinicalFileController::class, 'reactivate'])->name('reactivate');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS DE REACTIVACIÓN (SOFT DELETE)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Ubicación geográfica
    Route::post('paises/{id}/reactivate', [CountryController::class, 'reactivate'])->name('paises.reactivate');
    Route::post('departamentos/{id}/reactivate', [DepartmentController::class, 'reactivate'])->name('departamentos.reactivate');
    Route::post('municipios/{id}/reactivate', [MunicipalityController::class, 'reactivate'])->name('municipios.reactivate');
    
    // Catálogos médicos
    Route::post('/especialidades/{id}/reactivate', [SpecialtyController::class, 'reactivate'])->name('especialidades.reactivate');
    Route::post('schedule-types/{id}/reactivate', [ScheduleTypeController::class, 'reactivate'])->name('schedule-types.reactivate');
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
    Route::post('usuarios/{id}/reactivate', [UserController::class, 'reactivate'])->name('usuarios.reactivate');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE REPORTES
|--------------------------------------------------------------------------
*/
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/sigsa-3h', [ReportController::class, 'generateSigsa'])->name('reports.generate-sigsa');
    Route::post('/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');
});