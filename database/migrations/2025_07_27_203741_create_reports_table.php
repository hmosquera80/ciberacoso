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
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // ID único para cada denuncia
            $table->string('denunciante_nombre_completo'); // Nombre completo del denunciante
            $table->date('denunciante_fecha_nacimiento'); // Fecha de nacimiento del denunciante
            $table->integer('denunciante_edad')->nullable(); // Edad (se calculará, nullable por si hay error)
            $table->string('denunciante_municipio')->nullable(); // Municipio del denunciante
            $table->string('denunciante_colegio')->nullable(); // Nombre del colegio
            $table->string('denunciante_curso_grado')->nullable(); // Curso / grado
            $table->string('denunciante_identificacion')->nullable(); // # de Identificación

            // ¿La persona afectada eres tú o alguien más? (única selección)
            $table->enum('afectado_quien', ['Soy yo', 'Es otra persona', 'Prefiero no decir', 'Otra persona y yo']);

            // ¿Conoces al agresor? (única selección)
            $table->enum('agresor_conocido', ['si', 'no', 'sospecho quien es']);
            $table->string('agresor_nombre')->nullable(); // Nombre del agresor (si aplica)

            // ¿Hace cuánto está pasando?
            $table->integer('tiempo_dias')->nullable(); // Días
            $table->integer('tiempo_meses')->nullable(); // Meses
            $table->integer('tiempo_anios')->nullable(); // Años

            // ¿Ya reportaste esto por otro medio? (única selección)
            $table->enum('reportado_otro_medio', ['No, esta es la primera vez que lo cuento', 'Sí, llamé a la línea telefónica', 'Sí, se lo conté a un profesor o adulto', 'No estoy seguro/a']);
            $table->string('reportado_cual_linea')->nullable(); // ¿cuál? (línea telefónica)

            $table->text('resumen_hechos'); // Realiza un breve resumen de los hechos

            // ¿Deseas que alguien te contacte o hable contigo? (única selección)
            $table->enum('contacto_deseado', ['Sí, quiero que me llamen o escriban', 'No por ahora, solo quería contar lo que me pasa', 'Me gustaría recibir ayuda después']);

            $table->boolean('tiene_pruebas')->default(false); // ¿TIENES PRUEBAS?

            // Este campo podría almacenar la ruta del archivo de evidencia.
            $table->string('evidencia_path')->nullable(); // Anexar evidencia

            $table->timestamps(); // `created_at` y `updated_at` para registrar cuándo se creó/actualizó la denuncia
        });
        // Y hasta aquí, dentro de las llaves del método up()
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};