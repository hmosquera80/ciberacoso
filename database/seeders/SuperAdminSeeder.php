<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@ciberacoso.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'), // ¡CAMBIA ESTA CONTRASEÑA EN PRODUCCIÓN!
                'role' => 'super_admin',
                'colegio_id' => null, // SuperAdmin no tiene colegio asociado
            ]
        );
    }
}