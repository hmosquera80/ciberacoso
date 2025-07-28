<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Colegio;
use App\Models\Municipio; // Importa el modelo Municipio

class ColegioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que el municipio 'Cartagena' exista
        $municipioCartagena = Municipio::firstOrCreate(['nombre' => 'Cartagena']);

        Colegio::firstOrCreate([
            'nombre' => 'Colegio Ejemplo 1',
            'municipio_id' => $municipioCartagena->id,
            'activo' => true
        ]);
        Colegio::firstOrCreate([
            'nombre' => 'Colegio Ejemplo 2',
            'municipio_id' => $municipioCartagena->id,
            'activo' => true
        ]);
    }
}