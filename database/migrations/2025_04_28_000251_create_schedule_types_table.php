<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() 
    {
        Schema::create('schedule_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('specialty_id')->constrained();
            $table->json('days_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_patients');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'name']);
        });
    }

    public function down() {
        Schema::dropIfExists('schedule_types');
    }
}; 