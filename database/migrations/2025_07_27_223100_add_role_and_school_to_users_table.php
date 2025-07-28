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
            // Define el campo 'role' con un valor por defecto para usuarios existentes
            $table->string('role')->default('supervisor')->after('email'); // super_admin, admin, supervisor

            // Agrega el campo 'colegio_id' como nullable (super_admin no tiene colegio asociado)
            $table->foreignId('colegio_id')->nullable()->constrained('colegios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['colegio_id']);
            // Luego eliminar las columnas
            $table->dropColumn('colegio_id');
            $table->dropColumn('role');
        });
    }
};