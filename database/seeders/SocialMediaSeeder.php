<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialMedia; // Importa el modelo

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialMedia = [
            'WhatsApp',
            'Instagram',
            'Facebook',
            'TikTok',
            'Videojuegos',
            'Chat de clases virtuales',
            'Telegram',
            'Otro',
        ];

        foreach ($socialMedia as $media) {
            SocialMedia::firstOrCreate(['name' => $media]);
        }
    }
}