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
        Schema::create('report_feeling', function (Blueprint $table) {
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('feeling_id')->constrained('feelings')->onDelete('cascade');
            $table->primary(['report_id', 'feeling_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_feeling');
    }
};