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
        Schema::table('medical_consultations', function (Blueprint $table) {
            // Cambiar companion_relationship de string a foreign key
            $table->dropColumn('companion_relationship');
            $table->foreignId('companion_relationship_id')->nullable()->constrained('companion_relationships');
            
            // Cambiar guardian_relationship de string a foreign key
            $table->dropColumn('guardian_relationship');
            $table->foreignId('guardian_relationship_id')->nullable()->constrained('companion_relationships');
            
            // Cambiar contraceptive_method de string a foreign key
            $table->dropColumn('contraceptive_method');
            $table->foreignId('contraceptive_method_id')->nullable()->constrained('contraceptive_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_consultations', function (Blueprint $table) {
            // Revertir cambios
            $table->dropConstrainedForeignId('companion_relationship_id');
            $table->string('companion_relationship', 50)->nullable();
            
            $table->dropConstrainedForeignId('guardian_relationship_id');
            $table->string('guardian_relationship', 50)->nullable();
            
            $table->dropConstrainedForeignId('contraceptive_method_id');
            $table->string('contraceptive_method', 50)->nullable();
        });
    }
};