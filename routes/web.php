<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('panel');
    }
    return redirect()->route('login');
})->middleware('web'); // esto garantiza que funcione Auth::check()

Auth::routes();

Route::get('/panel', [App\Http\Controllers\HomeController::class, 'index'])->name('panel')->middleware('auth');
