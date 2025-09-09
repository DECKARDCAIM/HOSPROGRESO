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
            $table->foreignId('patient_status_id')->nullable()->constrained('patient_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_status_id');
        });
    }
};