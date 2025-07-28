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
        Schema::create('report_bullying_type', function (Blueprint $table) {
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('bullying_type_id')->constrained('bullying_types')->onDelete('cascade');
            $table->primary(['report_id', 'bullying_type_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_bullying_type');
    }
};