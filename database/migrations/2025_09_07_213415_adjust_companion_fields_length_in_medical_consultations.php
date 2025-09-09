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
            // Ajustar longitud de campos del acompañante
            $table->string('companion_phone', 8)->nullable()->change();
            $table->string('companion_dpi', 13)->nullable()->change();
            $table->string('guardian_phone', 8)->nullable()->change();
            $table->string('guardian_dpi', 13)->nullable()->change();
            $table->string('emergency_contact', 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            // Revertir a longitudes originales
            $table->string('companion_phone', 20)->nullable()->change();
            $table->string('companion_dpi', 20)->nullable()->change();
            $table->string('guardian_phone', 20)->nullable()->change();
            $table->string('guardian_dpi', 20)->nullable()->change();
            $table->string('emergency_contact', 20)->nullable()->change();
        });
    }
};
