<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/panel', [App\Http\Controllers\HomeController::class, 'index'])->name('panel');
