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
        Schema::create('report_social_media', function (Blueprint $table) {
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('social_media_id')->constrained('social_media')->onDelete('cascade');
            $table->primary(['report_id', 'social_media_id']); // Clave primaria compuesta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_social_media');
    }
};