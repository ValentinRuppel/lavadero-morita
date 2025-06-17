<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llama a otros seeders aquí
        $this->call([
            TipoVehiculoSeeder::class, // Primero los tipos de vehículo
            MarcaModeloSeeder::class,  // Luego marcas y modelos (que ahora dependen de tipos_vehiculos)
            // UserSeeder::class, // Si tienes un seeder de usuarios
        ]);
    }
}