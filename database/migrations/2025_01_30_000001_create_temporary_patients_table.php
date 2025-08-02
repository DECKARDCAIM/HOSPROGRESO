<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temporary_patients', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Número de registro del Excel
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('third_name')->nullable();
            $table->string('first_lastname');
            $table->string('second_lastname')->nullable();
            $table->string('married_lastname')->nullable();
            $table->string('cui', 13)->nullable();
            $table->string('sex')->nullable(); // Guardamos como string por ahora
            $table->string('civil_status')->nullable(); // Guardamos como string por ahora
            $table->string('linguistic_community')->nullable(); // Guardamos como string por ahora
            $table->string('ethnicity')->nullable(); // Guardamos como string por ahora
            $table->date('birth_date')->nullable();
            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
            $table->string('country')->nullable(); // Guardamos como string por ahora
            $table->string('department')->nullable(); // Guardamos como string por ahora
            $table->string('municipality')->nullable(); // Guardamos como string por ahora
            $table->text('specific_residence')->nullable();
            $table->boolean('is_processed')->default(false); // Para saber si ya fue procesado
            $table->json('import_errors')->nullable(); // Para guardar errores de validación
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temporary_patients');
    }
};