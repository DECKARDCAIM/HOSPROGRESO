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
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/perfil/editar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::put('/perfil/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::put('/perfil/banner', [ProfileController::class, 'updateBanner'])->name('profile.updateBanner');
    Route::delete('/perfil/sesion/{session_id}', [ProfileController::class, 'logoutSession'])->name('profile.logoutSession');

// Rutas para Notificaciones
Route::prefix('notifications')->group(function () {
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

Route::resource('patients', App\Http\Controllers\PatientController::class)
    ->middleware('auth')
    ->parameters(['patients' => 'patient']);

Route::resource('medical-consultations', App\Http\Controllers\MedicalConsultationController::class)
    ->middleware('auth')
    ->parameters(['medical-consultations' => 'medicalConsultation']);