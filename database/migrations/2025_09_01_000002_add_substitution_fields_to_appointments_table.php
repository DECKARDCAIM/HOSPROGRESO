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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('substitution_id')->nullable()->constrained('doctor_substitutions')->onDelete('set null');
            $table->boolean('is_substituted')->default(false);
            $table->timestamp('substituted_at')->nullable();
            $table->timestamp('returned_to_original_at')->nullable();
            
            $table->index(['substitution_id', 'is_substituted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['substitution_id']);
            $table->dropColumn(['substitution_id', 'is_substituted', 'substituted_at', 'returned_to_original_at']);
        });
    }
};
