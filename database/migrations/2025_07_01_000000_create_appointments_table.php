<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->foreignId('clinical_record_id')->constrained('clinical_records');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('specialty_id')->constrained('specialties');
            $table->foreignId('schedule_type_id')->constrained('schedule_types');
            $table->dateTime('appointment_date');
            $table->integer('slot_number');
            $table->enum('attention_type', ['consulta_externa', 'urgencia', 'emergencia'])->default('consulta_externa');
            $table->enum('status', ['pendiente', 'confirmada', 'atendida', 'perdida', 'cancelada', 'reagendada'])->default('pendiente');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('appointments');
            $table->foreignId('created_by')->constrained('users');
            
            // Campos de sustitución de doctores
            $table->foreignId('substitution_id')->nullable()->constrained('doctor_substitutions')->onDelete('set null');
            $table->boolean('is_substituted')->default(false);
            $table->timestamp('substituted_at')->nullable();
            $table->timestamp('returned_to_original_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['appointment_date', 'doctor_id']);
            $table->index(['status', 'appointment_date']);
            $table->index(['clinical_record_id', 'appointment_date']);
            $table->index(['substitution_id', 'is_substituted']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}; 