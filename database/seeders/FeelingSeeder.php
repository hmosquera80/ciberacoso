<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feeling; // Importa el modelo

class FeelingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feelings = [
            'Triste casi todos los días',
            'Tengo miedo de usar redes o el celular',
            'Ya no quiero ir al colegio',
            'Lloro o me encierro sin hablar',
            'Siento que no tengo salida',
            'Me quiero alejar de todos',
            'He pensado en suicidarme',
        ];

        foreach ($feelings as $feeling) {
            Feeling::firstOrCreate(['description' => $feeling]);
        }
    }
}