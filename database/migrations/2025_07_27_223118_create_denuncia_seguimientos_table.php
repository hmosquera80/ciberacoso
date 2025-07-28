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
        Schema::create('denuncia_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade'); // A qué denuncia se refiere
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quién hizo la anotación
            $table->text('anotacion'); // El texto de la anotación
            $table->foreignId('denuncia_estado_anterior_id')->nullable()->constrained('denuncia_estados'); // Estado antes de esta anotación
            $table->foreignId('denuncia_estado_nuevo_id')->constrained('denuncia_estados'); // Nuevo estado de la denuncia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncia_seguimientos');
    }
};