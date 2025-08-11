<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar tarea automática: marcar citas perdidas al día siguiente a las 00:05
Schedule::command('appointments:mark-missed --force')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->emailOutputOnFailure(env('ADMIN_EMAIL', 'admin@hospitalprogreso.gt'))
    ->appendOutputTo(storage_path('logs/cron-appointments.log'))
    ->name('mark-missed-appointments-daily')
    ->description('Marca citas pendientes como perdidas al día siguiente a las 00:05');
