<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('temporary_patients', function (Blueprint $table) {
            // Agregar campos de ID para filtros
            $table->unsignedBigInteger('sex_id')->nullable()->after('sex');
            $table->unsignedBigInteger('civil_status_id')->nullable()->after('civil_status');
            $table->unsignedBigInteger('linguistic_community_id')->nullable()->after('linguistic_community');
            $table->unsignedBigInteger('ethnicity_id')->nullable()->after('ethnicity');
            $table->unsignedBigInteger('country_id')->nullable()->after('country');
            $table->unsignedBigInteger('department_id')->nullable()->after('department');
            $table->unsignedBigInteger('municipality_id')->nullable()->after('municipality');
        });
    }

    public function down()
    {
        Schema::table('temporary_patients', function (Blueprint $table) {
            $table->dropColumn([
                'sex_id',
                'civil_status_id', 
                'linguistic_community_id',
                'ethnicity_id',
                'country_id',
                'department_id',
                'municipality_id'
            ]);
        });
    }
};