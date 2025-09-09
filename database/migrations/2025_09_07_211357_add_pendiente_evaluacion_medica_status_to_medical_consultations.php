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
        // No necesitamos modificar la estructura, solo documentar el nuevo status
        // El status 'pendiente_evaluacion_medica' ya es compatible con el campo string existente
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay cambios estructurales que revertir
    }
};
