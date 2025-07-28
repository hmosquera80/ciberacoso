<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SocialMediaSeeder::class,
            BullyingTypeSeeder::class,
            FeelingSeeder::class,
            ReportChannelSeeder::class,
            MunicipioSeeder::class,      // Nuevo
            ColegioSeeder::class,        // Nuevo
            DenunciaEstadoSeeder::class, // Nuevo
            SuperAdminSeeder::class,     // Nuevo
        ]);
    }
}