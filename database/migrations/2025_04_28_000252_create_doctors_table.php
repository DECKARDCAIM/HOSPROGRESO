<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('third_name')->nullable();
            $table->string('first_lastname');
            $table->string('second_lastname')->nullable();
            $table->string('married_lastname')->nullable();
            $table->string('cui', 13)->unique();
            $table->string('license_number')->unique();
            $table->foreignId('specialty_id')->constrained('specialties');
            $table->foreignId('schedule_type_id')->nullable()->constrained('schedule_types');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}; 