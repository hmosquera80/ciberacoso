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
        Schema::table('reports', function (Blueprint $table) {
            // Se añade después de la creación inicial
            $table->foreignId('denuncia_estado_id')->default(1)->constrained('denuncia_estados')->after('evidencia_path'); // Por defecto 'Abierta' (ID 1)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['denuncia_estado_id']);
            $table->dropColumn('denuncia_estado_id');
        });
    }
};