<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoVehiculo;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoVehiculo::firstOrCreate(['nombre' => 'Auto'], ['precio' => 50.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Moto'], ['precio' => 25.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Camioneta'], ['precio' => 75.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'SUV'], ['precio' => 60.00]);
        // Agrega más tipos si es necesario
    }
}