<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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