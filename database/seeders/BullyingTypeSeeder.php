<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BullyingType; // Importa el modelo BullyingType

class BullyingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bullyingTypes = [
            'Me envían mensajes feos o me insultan',
            'Publicaron fotos o videos míos sin permiso',
            'Me envían imágenes y/o mensajes obscenos',
            'Se burlan de mí por redes',
            'Me excluyen o bloquean de grupos',
            'Me amenazan o me mandan mensajes intimidantes',
            'Se hacen pasar por mí (suplantación de identidad)',
            'Me obligan a hacer cosas en línea que no quiero',
            'Otra cosa (escríbela)', // Asegúrate de que esta opción sea idéntica a la del formulario si la usas
        ];

        foreach ($bullyingTypes as $type) {
            // firstOrCreate intentará encontrar uno existente con el mismo 'description'
            // Si no lo encuentra, lo creará.
            BullyingType::firstOrCreate(['description' => $type]);
        }
    }
}