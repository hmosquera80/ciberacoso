<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReportChannel; // Importa el modelo

class ReportChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reportChannels = [
            'No, esta es la primera vez que lo cuento',
            'Sí, llamé a la línea telefónica',
            'Sí, se lo conté a un profesor o adulto',
            'No estoy seguro/a',
        ];

        foreach ($reportChannels as $channel) {
            ReportChannel::firstOrCreate(['name' => $channel]);
        }
    }
}