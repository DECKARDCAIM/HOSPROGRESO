<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_consultation_medication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_consultation_id')->constrained('medical_consultations')->onDelete('cascade');
            $table->foreignId('medication_id')->constrained('medications')->onDelete('cascade');
            $table->text('dosage')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_consultation_medication');
    }
}; 