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
        Schema::table('users', function (Blueprint $table) {
            // Agregar nuevos campos
            $table->string('cui', 13)->nullable()->unique()->after('email');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null')->after('cui');
            $table->boolean('is_active')->default(true)->after('role_id');
            
            // Eliminar la columna role anterior
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar la columna role
            $table->enum('role', ['emergencia', 'consulta_externa', 'admin'])->default('consulta_externa')->after('email');
            
            // Eliminar los nuevos campos
            $table->dropForeign(['role_id']);
            $table->dropColumn(['cui', 'role_id', 'is_active']);
        });
    }
};
