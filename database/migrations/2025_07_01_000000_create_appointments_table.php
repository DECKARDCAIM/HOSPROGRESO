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
            $table->string('appointment_number')->unique(); // Número de cita único
            $table->foreignId('clinical_record_id')->constrained('clinical_records');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('specialty_id')->constrained('specialties');
            $table->foreignId('schedule_type_id')->constrained('schedule_types');
            $table->dateTime('appointment_date');
            $table->integer('slot_number'); // Número de turno dentro del horario
            $table->enum('attention_type', ['consulta_externa', 'urgencia', 'emergencia'])->default('consulta_externa');
            $table->enum('status', ['pendiente', 'confirmada', 'atendida', 'perdida', 'cancelada', 'reagendada'])->default('pendiente');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamp('confirmed_at')->nullable(); // Cuándo se confirmó
            $table->timestamp('attended_at')->nullable(); // Cuándo fue atendida
            $table->timestamp('cancelled_at')->nullable(); // Cuándo se canceló
            $table->string('cancelled_reason')->nullable(); // Razón de cancelación
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('appointments'); // Si fue reagendada desde otra cita
            $table->foreignId('created_by')->constrained('users'); // Usuario que creó la cita
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['appointment_date', 'doctor_id']);
            $table->index(['status', 'appointment_date']);
            $table->index(['clinical_record_id', 'appointment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}; 