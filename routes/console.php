<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar tareas automáticas
Schedule::command('appointments:mark-missed --force')
    ->hourly() // Ejecutar cada hora
    ->withoutOverlapping() // No ejecutar si ya hay una instancia corriendo
    ->onOneServer() // Solo ejecutar en un servidor (si tienes múltiples)
    ->runInBackground() // Ejecutar en segundo plano
    ->emailOutputOnFailure(env('ADMIN_EMAIL', 'admin@hospitalprogreso.gt')) // Enviar email si falla
    ->appendOutputTo(storage_path('logs/cron-appointments.log')); // Log de ejecuciones

// Verificar citas perdidas cada 30 minutos durante horarios laborales (6 AM - 8 PM)
Schedule::command('appointments:mark-missed --force')
    ->everyThirtyMinutes()
    ->between('06:00', '20:00') // Solo durante horarios laborales
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->name('check-missed-appointments-frequent')
    ->description('Verificación frecuente de citas perdidas durante horarios laborales');
