<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            $table->enum('attention_type', ['emergencia', 'consulta_externa'])->default('consulta_externa')->after('status');
            $table->text('emergency_vital_signs')->nullable()->after('attention_type');
            $table->text('emergency_trauma_assessment')->nullable()->after('emergency_vital_signs');
            $table->text('emergency_treatment_plan')->nullable()->after('emergency_trauma_assessment');
            $table->text('consultation_physical_exam')->nullable()->after('emergency_treatment_plan');
            $table->text('consultation_treatment_plan')->nullable()->after('consultation_physical_exam');
            $table->enum('final_status', ['hospitalizado', 'egresado'])->nullable()->after('consultation_treatment_plan');
        });
    }

    public function down()
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            $table->dropColumn([
                'attention_type',
                'emergency_vital_signs',
                'emergency_trauma_assessment',
                'emergency_treatment_plan',
                'consultation_physical_exam',
                'consultation_treatment_plan',
                'final_status'
            ]);
        });
    }
}; 