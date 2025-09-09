<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            // Eliminar campos que ya no se usan en las vistas
            $table->dropColumn([
                'admission_note',
                'emergency_vital_signs', 
                'emergency_treatment_plan',
                'consultation_treatment_plan',
                'diagnosis_cie10_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            // Restaurar campos en caso de rollback
            $table->text('admission_note')->nullable();
            $table->text('emergency_vital_signs')->nullable();
            $table->text('emergency_treatment_plan')->nullable();
            $table->text('consultation_treatment_plan')->nullable();
            $table->string('diagnosis_cie10_code')->nullable()->comment('Código CIE-10 del diagnóstico');
        });
    }
};