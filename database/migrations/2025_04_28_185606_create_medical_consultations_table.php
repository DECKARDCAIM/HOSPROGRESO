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
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('specialty_id')->constrained('specialties');
            $table->datetime('consultation_date');
            $table->text('consultation_reason');
            $table->text('medical_diagnosis')->nullable();
            $table->text('nursing_note')->nullable();
            $table->text('admission_note')->nullable();
            $table->text('prescribed_medications')->nullable();
            $table->text('reference_contrareference')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_consultations');
    }
}; 