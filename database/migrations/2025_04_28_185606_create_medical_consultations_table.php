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
            $table->string('status')->default('abierta');
            $table->enum('attention_type', ['emergencia', 'consulta_externa'])->default('consulta_externa');
            $table->text('physical_exam')->nullable();
            $table->enum('final_status', ['egresado', 'hospitalizado', 'referido', 'fallecido'])->nullable();
            
            // Campos específicos para SIGSA 3H
            $table->foreignId('patient_status_id')->nullable()->constrained('patient_statuses');
            $table->text('prescribed_treatment')->nullable()->comment('Tratamiento y medicamentos formulados');
            $table->integer('gestation_weeks')->nullable()->comment('Semanas de gestación (si aplica)');
            $table->text('sigsa_observations')->nullable()->comment('Observaciones adicionales para SIGSA');
            
            // Campos de Acompañante (Adultos)
            $table->string('companion_name', 100)->nullable();
            $table->string('companion_phone', 8)->nullable();
            $table->string('companion_email', 100)->nullable();
            $table->string('companion_dpi', 13)->nullable();
            $table->foreignId('companion_relationship_id')->nullable()->constrained('companion_relationships');
            $table->string('companion_address', 255)->nullable();
            
            // Campos Gineco-Obstétricos
            $table->boolean('is_pregnant')->nullable();
            $table->date('last_menstrual_period')->nullable();
            $table->integer('menstrual_cycle')->nullable();
            $table->integer('pregnancies_count')->nullable();
            $table->integer('births_count')->nullable();
            $table->integer('abortions_count')->nullable();
            $table->integer('cesareans_count')->nullable();
            $table->foreignId('contraceptive_method_id')->nullable()->constrained('contraceptive_methods');
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
            
            // Campos de sustitución de doctores
            $table->foreignId('substitution_id')->nullable()->constrained('doctor_substitutions')->onDelete('set null');
            $table->boolean('is_substituted')->default(false);
            $table->timestamp('substituted_at')->nullable();
            $table->timestamp('returned_to_original_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['substitution_id', 'is_substituted']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_consultations');
    }
}; 