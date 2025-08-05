<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clinical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number')->unique();
            $table->string('old_registration_number')->nullable();
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('third_name')->nullable();
            $table->string('first_lastname');
            $table->string('second_lastname')->nullable();
            $table->string('married_lastname')->nullable();
            $table->string('cui', 13)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->foreignId('sex_id')->constrained('sexes');
            $table->foreignId('civil_status_id')->constrained('civil_statuses');
            $table->foreignId('linguistic_community_id')->constrained('linguistic_communities');
            $table->foreignId('ethnicity_id')->constrained('ethnicities');
            $table->date('birth_date');
            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('municipality_id')->constrained('municipalities');
            $table->text('specific_residence')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_records');
    }
}; 