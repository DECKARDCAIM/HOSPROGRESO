<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clinical_file_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->onDelete('cascade');
            $table->boolean('is_printed')->default(false);
            $table->datetime('printed_at')->nullable();
            $table->foreignId('printed_by')->nullable()->constrained('users');
            $table->boolean('is_archived')->default(false);
            $table->datetime('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_file_tracking');
    }
};