<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->foreignId('specialty_id')->nullable()->constrained('specialties');
            $table->datetime('consultation_date');
            $table->text('consultation_reason');
            $table->text('medical_diagnosis')->nullable();
            $table->text('nursing_note')->nullable();
            $table->text('admission_note')->nullable();
            $table->text('prescribed_medications')->nullable();
            $table->text('reference_contrareference')->nullable();
            $table->string('status')->default('abierta');
            $table->enum('attention_type', ['emergencia', 'consulta_externa'])->default('consulta_externa');
            $table->text('emergency_vital_signs')->nullable();
            $table->text('emergency_trauma_assessment')->nullable();
            $table->text('emergency_treatment_plan')->nullable();
            $table->text('consultation_physical_exam')->nullable();
            $table->text('consultation_treatment_plan')->nullable();
            $table->enum('final_status', ['egresado', 'hospitalizado', 'referido', 'fallecido'])->nullable();
            
            // Campos específicos para SIGSA 3H
            $table->foreignId('control_type_id')->nullable()->constrained('control_types')->comment('Tipo de control médico');
            $table->boolean('has_igss')->default(false)->comment('¿Tiene derecho IGSS?');
            $table->boolean('is_new_patient')->default(false)->comment('¿Paciente nuevo?');
            $table->string('diagnosis_cie10_code')->nullable()->comment('Código CIE-10 del diagnóstico');
            $table->text('prescribed_treatment')->nullable()->comment('Tratamiento y medicamentos formulados');
            $table->boolean('was_referred')->default(false)->comment('¿Fue referido?');
            $table->boolean('comes_counter_referred')->default(false)->comment('¿Viene contra referido?');
            $table->boolean('comes_referred')->default(false)->comment('¿Viene referido?');
            $table->boolean('was_counter_referred')->default(false)->comment('¿Fue contra referido?');
            $table->string('reference_destination')->nullable()->comment('Destino de referencia');
            $table->text('reference_reason')->nullable()->comment('Motivo de referencia');
            $table->integer('gestation_weeks')->nullable()->comment('Semanas de gestación (si aplica)');
            $table->text('sigsa_observations')->nullable()->comment('Observaciones adicionales para SIGSA');
            
            // Campos de Acompañante (Adultos)
            $table->string('companion_name', 100)->nullable();
            $table->string('companion_phone', 20)->nullable();
            $table->string('companion_email', 100)->nullable();
            $table->string('companion_dpi', 20)->nullable();
            $table->string('companion_relationship', 50)->nullable();
            
            // Campos de Tutor/Padre/Madre (Pediatría)
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_email', 100)->nullable();
            $table->string('guardian_dpi', 20)->nullable();
            $table->string('guardian_relationship', 50)->nullable();
            $table->string('guardian_address', 200)->nullable();
            $table->string('emergency_contact', 20)->nullable();
            
            // Campos Gineco-Obstétricos
            $table->boolean('is_pregnant')->nullable();
            $table->date('last_menstrual_period')->nullable();
            $table->integer('menstrual_cycle')->nullable();
            $table->integer('pregnancies_count')->nullable();
            $table->integer('births_count')->nullable();
            $table->integer('abortions_count')->nullable();
            $table->integer('cesareans_count')->nullable();
            $table->string('contraceptive_method', 50)->nullable();
            $table->text('gynecological_history')->nullable();
            
            // Campos Pediátricos
            $table->decimal('birth_weight', 5, 2)->nullable();
            $table->decimal('current_weight', 5, 2)->nullable();
            $table->decimal('current_height', 5, 1)->nullable();
            $table->decimal('head_circumference', 4, 1)->nullable();
            $table->string('vaccination_status', 50)->nullable();
            $table->string('feeding_type', 50)->nullable();
            $table->string('development_milestones', 50)->nullable();
            $table->text('pediatric_history')->nullable();
            $table->text('parent_instructions')->nullable();
            
            // Campos de Estados Finales Extendidos
            $table->string('hospital_service', 100)->nullable();
            $table->datetime('death_date')->nullable();
            $table->string('death_cause', 200)->nullable();
            
            $table->timestamps();
            
            $table->index(['control_type_id', 'consultation_date']);
            $table->index(['has_igss', 'consultation_date']);
            $table->index(['is_new_patient', 'consultation_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_consultations');
    }
}; 