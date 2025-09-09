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
        Schema::create('doctor_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('substitute_doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('reason', ['vacaciones', 'licencia_medica', 'despido', 'otro']);
            $table->enum('status', ['activa', 'completada', 'cancelada'])->default('activa');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['original_doctor_id', 'status']);
            $table->index(['substitute_doctor_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_substitutions');
    }
};
