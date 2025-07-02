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
            // Campos específicos para SIGSA 3H
            $table->foreignId('control_type_id')->nullable()->constrained('control_types')->comment('Tipo de control médico');
            $table->boolean('has_igss')->default(false)->comment('¿Tiene derecho IGSS?');
            $table->boolean('is_new_patient')->default(false)->comment('¿Paciente nuevo?');
            $table->string('diagnosis_cie10_code')->nullable()->comment('Código CIE-10 del diagnóstico');
            $table->text('prescribed_treatment')->nullable()->comment('Tratamiento y medicamentos formulados');
            
            // Referencias/Contra-referencias
            $table->boolean('was_referred')->default(false)->comment('¿Fue referido?');
            $table->boolean('comes_counter_referred')->default(false)->comment('¿Viene contra referido?');
            $table->boolean('comes_referred')->default(false)->comment('¿Viene referido?');
            $table->boolean('was_counter_referred')->default(false)->comment('¿Fue contra referido?');
            $table->string('reference_destination')->nullable()->comment('Destino de referencia');
            $table->text('reference_reason')->nullable()->comment('Motivo de referencia');
            
            // Campos adicionales del SIGSA 3H
            $table->integer('gestation_weeks')->nullable()->comment('Semanas de gestación (si aplica)');
            $table->text('sigsa_observations')->nullable()->comment('Observaciones adicionales para SIGSA');
            
            // Índices para reportes
            $table->index(['control_type_id', 'consultation_date']);
            $table->index(['has_igss', 'consultation_date']);
            $table->index(['is_new_patient', 'consultation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            $table->dropForeign(['control_type_id']);
            $table->dropColumn([
                'control_type_id',
                'has_igss',
                'is_new_patient',
                'diagnosis_cie10_code',
                'prescribed_treatment',
                'was_referred',
                'comes_counter_referred',
                'comes_referred',
                'was_counter_referred',
                'reference_destination',
                'reference_reason',
                'gestation_weeks',
                'sigsa_observations'
            ]);
        });
    }
};
