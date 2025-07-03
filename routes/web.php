<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\ScheduleTypeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('panel');
    }
    return redirect()->route('login');
})->middleware('web'); // esto garantiza que funcione Auth::check()

Auth::routes();
Route::get('/panel', [App\Http\Controllers\HomeController::class, 'index'])->name('panel')->middleware('auth');
    
// Ruta Resource para Especialidades
Route::resource('especialidades', App\Http\Controllers\SpecialtyController::class)
    ->middleware('auth')
    ->parameters(['especialidades' => 'specialty']);

// Ruta Resource para Country
Route::resource('paises', App\Http\Controllers\CountryController::class)
    ->middleware('auth')
    ->parameters(['paises' => 'country']);

// Ruta Resource para Department
Route::resource('departamentos', App\Http\Controllers\DepartmentController::class)
    ->middleware('auth')
    ->parameters(['departamentos' => 'department']);

// Ruta Resource para Municipality
Route::resource('municipios', App\Http\Controllers\MunicipalityController::class)
    ->middleware('auth')
    ->parameters(['municipios' => 'municipality']);

// Ruta para Perfil
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

// Rutas para Notificaciones
Route::prefix('notifications')->middleware('auth')->group(function () {
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread');
    Route::get('/all', [NotificationController::class, 'getAllNotifications'])->name('notifications.all');
    Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

// Rutas para Catálogos del Sistema Médico
Route::resource('sexes', App\Http\Controllers\SexController::class)
    ->middleware('auth')
    ->parameters(['sexes' => 'sex']);

Route::resource('civil-statuses', App\Http\Controllers\CivilStatusController::class)
    ->middleware('auth')
    ->parameters(['civil-statuses' => 'civilStatus']);

Route::resource('linguistic-communities', App\Http\Controllers\LinguisticCommunityController::class)
    ->middleware('auth')
    ->parameters(['linguistic-communities' => 'linguisticCommunity']);

Route::resource('ethnicities', App\Http\Controllers\EthnicityController::class)
    ->middleware('auth')
    ->parameters(['ethnicities' => 'ethnicity']);

Route::resource('disabilities', App\Http\Controllers\DisabilityController::class)
    ->middleware('auth')
    ->parameters(['disabilities' => 'disability']);

Route::resource('allergies', App\Http\Controllers\AllergyController::class)
    ->middleware('auth')
    ->parameters(['allergies' => 'allergy']);

Route::resource('laboratory-tests', App\Http\Controllers\LaboratoryTestController::class)
    ->middleware('auth')
    ->parameters(['laboratory-tests' => 'laboratoryTest']);

Route::resource('exams', App\Http\Controllers\ExamController::class)
    ->middleware('auth')
    ->parameters(['exams' => 'exam']);

Route::resource('medications', App\Http\Controllers\MedicationController::class)
    ->middleware('auth')
    ->parameters(['medications' => 'medication']);

// Rutas para Módulos Principales del Sistema Médico
Route::resource('doctors', App\Http\Controllers\DoctorController::class)
    ->middleware('auth')
    ->parameters(['doctors' => 'doctor']);

Route::resource('clinical-records', App\Http\Controllers\ClinicalRecordController::class)
    ->middleware('auth')
    ->parameters(['clinical-records' => 'clinicalRecord']);

// Rutas para Medical Consultations con sistema de steps
Route::resource('medical-consultations', App\Http\Controllers\MedicalConsultationController::class)
    ->middleware('auth')
    ->parameters(['medical-consultations' => 'medicalConsultation']);

// Rutas adicionales para el sistema de steps
Route::get('medical-consultations/{medicalConsultation}/process', [App\Http\Controllers\MedicalConsultationController::class, 'process'])
    ->name('medical-consultations.process')
    ->middleware('auth');

Route::post('medical-consultations/{medicalConsultation}/update-process', [App\Http\Controllers\MedicalConsultationController::class, 'updateProcess'])
    ->name('medical-consultations.update-process')
    ->middleware('auth');

Route::post('medical-consultations/{medicalConsultation}/finalize', [App\Http\Controllers\MedicalConsultationController::class, 'finalize'])
    ->name('medical-consultations.finalize')
    ->middleware('auth');

Route::get('medical-consultations/{medicalConsultation}/print', [App\Http\Controllers\MedicalConsultationController::class, 'print'])->name('medical-consultations.print')->middleware('auth');

// Rutas de reactivación - Requieren autenticación
Route::middleware('auth')->group(function () {
    Route::post('paises/{id}/reactivate', [App\Http\Controllers\CountryController::class, 'reactivate'])->name('paises.reactivate');
    Route::post('departamentos/{id}/reactivate', [App\Http\Controllers\DepartmentController::class, 'reactivate'])->name('departamentos.reactivate');
    Route::post('municipios/{id}/reactivate', [App\Http\Controllers\MunicipalityController::class, 'reactivate'])->name('municipios.reactivate');
    Route::post('/especialidades/{id}/reactivate', [App\Http\Controllers\SpecialtyController::class, 'reactivate'])->name('especialidades.reactivate');
    Route::post('doctors/{id}/reactivate', [App\Http\Controllers\DoctorController::class, 'reactivate'])->name('doctors.reactivate');
    Route::post('sexes/{id}/reactivate', [App\Http\Controllers\SexController::class, 'reactivate'])->name('sexes.reactivate');
    Route::post('civil-statuses/{id}/reactivate', [App\Http\Controllers\CivilStatusController::class, 'reactivate'])->name('civil-statuses.reactivate');
    Route::post('linguistic-communities/{id}/reactivate', [App\Http\Controllers\LinguisticCommunityController::class, 'reactivate'])->name('linguistic-communities.reactivate');
    Route::post('ethnicities/{id}/reactivate', [App\Http\Controllers\EthnicityController::class, 'reactivate'])->name('ethnicities.reactivate');
    Route::post('disabilities/{id}/reactivate', [App\Http\Controllers\DisabilityController::class, 'reactivate'])->name('disabilities.reactivate');
    Route::post('allergies/{id}/reactivate', [App\Http\Controllers\AllergyController::class, 'reactivate'])->name('allergies.reactivate');
    Route::post('laboratory-tests/{id}/reactivate', [App\Http\Controllers\LaboratoryTestController::class, 'reactivate'])->name('laboratory-tests.reactivate');
    Route::post('exams/{id}/reactivate', [App\Http\Controllers\ExamController::class, 'reactivate'])->name('exams.reactivate');
    Route::post('medications/{id}/reactivate', [App\Http\Controllers\MedicationController::class, 'reactivate'])->name('medications.reactivate');
    Route::post('schedule-types/{id}/reactivate', [App\Http\Controllers\ScheduleTypeController::class, 'reactivate'])->name('schedule-types.reactivate');
});

Route::get('clinical-records/{clinicalRecord}/print', [App\Http\Controllers\ClinicalRecordController::class, 'printPdf'])->name('clinical-records.print')->middleware('auth');

Route::resource('schedule-types', App\Http\Controllers\ScheduleTypeController::class)
    ->middleware('auth');

Route::resource('appointments', AppointmentController::class)->middleware('auth');

// Rutas AJAX para appointments
Route::post('appointments/get-doctors', [AppointmentController::class, 'getDoctorsBySpecialty'])->name('appointments.get-doctors')->middleware('auth');
Route::post('appointments/get-schedule-types', [AppointmentController::class, 'getScheduleTypesByDoctor'])->name('appointments.get-schedule-types')->middleware('auth');
Route::post('appointments/get-available-dates', [AppointmentController::class, 'getAvailableDates'])->name('appointments.get-available-dates')->middleware('auth');
Route::post('appointments/get-next-slot', [AppointmentController::class, 'getNextAvailableSlot'])->name('appointments.get-next-slot')->middleware('auth');

// Rutas adicionales para gestión de citas
Route::put('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status')->middleware('auth');
Route::post('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule')->middleware('auth');
Route::get('appointments/{appointment}/print', [AppointmentController::class, 'printPdf'])->name('appointments.print')->middleware('auth');
Route::post('appointments/print-multiple', [AppointmentController::class, 'printMultiplePdf'])->name('appointments.print-multiple')->middleware('auth');

// Rutas para Gestión de Roles
Route::resource('roles', App\Http\Controllers\RoleController::class)
    ->middleware('auth')
    ->parameters(['roles' => 'role']);

Route::post('roles/{id}/reactivate', [App\Http\Controllers\RoleController::class, 'reactivate'])
    ->name('roles.reactivate')
    ->middleware('auth');

// Rutas para Gestión de Usuarios
Route::resource('usuarios', App\Http\Controllers\UserController::class)
    ->middleware('auth')
    ->parameters(['usuarios' => 'user']);

Route::post('usuarios/{id}/reactivate', [App\Http\Controllers\UserController::class, 'reactivate'])
    ->name('usuarios.reactivate')
    ->middleware('auth');

Route::put('usuarios/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])
    ->name('usuarios.reset-password')
    ->middleware('auth');

// Rutas para Tipos de Control (SIGSA 3H)
Route::resource('control-types', App\Http\Controllers\ControlTypeController::class)
    ->middleware('auth')
    ->parameters(['control-types' => 'controlType']);

Route::post('control-types/{id}/reactivate', [App\Http\Controllers\ControlTypeController::class, 'reactivate'])
    ->name('control-types.reactivate')
    ->middleware('auth');

// Rutas para Módulo de Reportes SIGSA 3H
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/sigsa-3h', [App\Http\Controllers\ReportController::class, 'generateSigsa'])->name('reports.generate-sigsa');
    Route::post('/preview', [App\Http\Controllers\ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/statistics', [App\Http\Controllers\ReportController::class, 'statistics'])->name('reports.statistics');
});