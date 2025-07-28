<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DenunciaEstado;

class DenunciaEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DenunciaEstado::firstOrCreate(['nombre' => 'Abierta']); // ID 1
        DenunciaEstado::firstOrCreate(['nombre' => 'En Trámite']); // ID 2
        DenunciaEstado::firstOrCreate(['nombre' => 'Pendiente de Cierre']); // ID 3
        DenunciaEstado::firstOrCreate(['nombre' => 'Cerrada']); // ID 4
    }
}