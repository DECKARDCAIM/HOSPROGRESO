<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('clinical_record_disability', function (Blueprint $table) {
        $table->id();
        $table->foreignId('clinical_record_id')->constrained()->onDelete('cascade');
        $table->foreignId('disability_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
public function down()
{
    Schema::dropIfExists('clinical_record_disability');
}
};



