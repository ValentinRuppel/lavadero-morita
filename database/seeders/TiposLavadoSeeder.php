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
                'nombre_lavado' => 'Lavado Básico',
                'descripcion' => 'Lavado exterior rápido y secado.',
                'precio' => 100.00,
                'duracion_estimada' => 15, // 15 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Lavado Completo',
                'descripcion' => 'Lavado exterior, aspirado interior y limpieza de cristales.',
                'precio' => 250.00,
                'duracion_estimada' => 30, // 30 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Lavado Premium',
                'descripcion' => 'Lavado completo, encerado a mano y acondicionamiento de interiores.',
                'precio' => 500.00,
                'duracion_estimada' => 60, // 60 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Lavado y Pulido',
                'descripcion' => 'Lavado exterior detallado y pulido de carrocería.',
                'precio' => 800.00,
                'duracion_estimada' => 120, // 120 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Detallado Interior',
                'descripcion' => 'Limpieza profunda de tapicería, plásticos y alfombras.',
                'precio' => 700.00,
                'duracion_estimada' => 90, // 90 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_lavado' => 'Lavado de Motor',
                'descripcion' => 'Limpieza y desengrasado del compartimento del motor.',
                'precio' => 350.00,
                'duracion_estimada' => 45, // 45 minutos
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}