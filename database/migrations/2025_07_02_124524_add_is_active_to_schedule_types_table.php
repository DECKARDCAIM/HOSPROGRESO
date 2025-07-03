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
        Schema::table('schedule_types', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('max_patients');
            $table->index(['is_active', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_types', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'name']);
            $table->dropColumn('is_active');
        });
    }
};
