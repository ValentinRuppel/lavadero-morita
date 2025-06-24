<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposLavadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_lavados')->insert([
            [
                'nombre_lavado' => 'Lavado Rápido',
                'descripcion' => 'Enjuague y jabón básico para remover suciedad superficial.',
                'precio' => 5000.00,
                'duracion_estimada' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Espuma Activa',
                'descripcion' => 'Aplicación de espuma con aclarado. Ideal para suciedad media.',
                'precio' => 7000.00,
                'duracion_estimada' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Lavado Completo',
                'descripcion' => 'Espuma + aclarado + cera rápida para acabado brillante.',
                'precio' => 12000.00,
                'duracion_estimada' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Solo Cera',
                'descripcion' => 'Aplicación de cera rápida para protección de pintura.',
                'precio' => 6000.00,
                'duracion_estimada' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Limpieza de Llantas',
                'descripcion' => 'Chorro a presión y desengrasante para llantas.',
                'precio' => 5000.00,
                'duracion_estimada' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Combo Rápido Total',
                'descripcion' => 'Incluye todas las etapas: espuma, aclarado, llantas y cera.',
                'precio' => 15000.00,
                'duracion_estimada' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
