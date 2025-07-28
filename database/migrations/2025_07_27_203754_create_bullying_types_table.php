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
        Schema::create('bullying_types', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique(); // Me envían mensajes feos, Publicaron fotos, etc. [cite: 15, 16]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bullying_types');
    }
};
