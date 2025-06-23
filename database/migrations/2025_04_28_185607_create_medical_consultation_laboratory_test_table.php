<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_consultation_laboratory_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_consultation_id');
            $table->foreignId('laboratory_test_id');
            $table->timestamps();

            $table->foreign('medical_consultation_id', 'mc_lt_consultation_id_fk')->references('id')->on('medical_consultations')->onDelete('cascade');
            $table->foreign('laboratory_test_id')->references('id')->on('laboratory_tests')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_consultation_laboratory_test');
    }
}; 