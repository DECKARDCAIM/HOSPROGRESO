<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Cambiar appointment_date de dateTime a date (solo fecha)
            $table->date('appointment_date_new')->after('schedule_type_id');
        });
        
        // Migrar datos existentes
        DB::statement('UPDATE appointments SET appointment_date_new = DATE(appointment_date)');
        
        Schema::table('appointments', function (Blueprint $table) {
            // Eliminar campos de turno y hora
            $table->dropColumn(['appointment_date', 'slot_number']);
        });
        
        Schema::table('appointments', function (Blueprint $table) {
            // Renombrar la nueva columna
            $table->renameColumn('appointment_date_new', 'appointment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Restaurar campos originales
            $table->dateTime('appointment_date_old')->after('schedule_type_id');
            $table->integer('slot_number')->after('appointment_date_old');
        });
        
        // Restaurar datos (asumiendo turno 1 por defecto)
        DB::statement('UPDATE appointments SET appointment_date_old = CONCAT(appointment_date, " 08:00:00"), slot_number = 1');
        
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('appointment_date');
        });
        
        Schema::table('appointments', function (Blueprint $table) {
            $table->renameColumn('appointment_date_old', 'appointment_date');
        });
    }
};
