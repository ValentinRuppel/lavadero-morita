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
        TipoVehiculo::firstOrCreate(['nombre' => 'Coche'], ['precio' => 25.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Moto'], ['precio' => 15.00]);
        TipoVehiculo::firstOrCreate(['nombre' => 'Camioneta'], ['precio' => 35.00]);
    }
}