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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del día festivo (ej: "Navidad", "Año Nuevo")
            $table->date('date'); // Fecha del día festivo
            $table->text('description')->nullable(); // Descripción opcional
            $table->boolean('is_recurring')->default(false); // Si se repite cada año
            $table->boolean('is_active')->default(true); // Si está activo
            $table->unsignedBigInteger('created_by'); // Usuario que lo creó
            $table->timestamps();
            
            // Índices
            $table->index('date');
            $table->index('is_active');
            $table->index('is_recurring');
            
            // Clave foránea
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
