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
        TipoVehiculo::firstOrCreate(['nombre' => 'Auto'], ['precio' => 5000.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Moto'], ['precio' => 2500.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Camioneta'], ['precio' => 7500.00]);
    }
}
