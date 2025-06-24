<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoVehiculo;

class MarcaModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current year dynamically
        $currentYear = date('Y');

        // Obtener los IDs de los tipos de vehículo existentes
        // Ajusta estos IDs según tus datos reales
        $autoTypeId = TipoVehiculo::where('nombre', 'Auto')->first()->id ?? 1;
        $motoTypeId = TipoVehiculo::where('nombre', 'Moto')->first()->id ?? 2;
        $camionetaTypeId = TipoVehiculo::where('nombre', 'Camioneta')->first()->id ?? 3;
        // Añade más si tienes otros tipos...

        $marcas = [
            'Toyota' => [
                ['nombre' => 'Corolla', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1966, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Hilux', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1968, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Yaris', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1999, 'anio_fin' => null], // Production ongoing
            ],
            'Ford' => [
                ['nombre' => 'Focus', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1998, 'anio_fin' => null], // Set to null to use current year if desired, or keep specific end year if known. Changed from 2025
                ['nombre' => 'Ranger', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1982, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Fiesta', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1976, 'anio_fin' => 2023], // Production ended
            ],
            'Chevrolet' => [
                ['nombre' => 'Onix', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2012, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Cruze', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2008, 'anio_fin' => 2023], // Production ended
                ['nombre' => 'S10', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1982, 'anio_fin' => null], // Production ongoing in South America
            ],
            'Volkswagen' => [
                ['nombre' => 'Gol', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1980, 'anio_fin' => 2023], // Production ended
                ['nombre' => 'Amarok', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 2010, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Vento', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1991, 'anio_fin' => 2018], // In Latin America, some generations were called Vento
            ],
            'Renault' => [
                ['nombre' => 'Clio', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1990, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Kangoo', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1997, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Sandero', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2007, 'anio_fin' => null], // Production ongoing
            ],
            'Fiat' => [
                ['nombre' => 'Cronos', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2018, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Toro', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 2016, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Pulse', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2021, 'anio_fin' => null], // Production ongoing
            ],
            // Ejemplo de Moto
            'Honda' => [
                ['nombre' => 'CB125F', 'tipo_vehiculo_id' => $motoTypeId, 'anio_inicio' => 2015, 'anio_fin' => null], // Production ongoing
                ['nombre' => 'Wave', 'tipo_vehiculo_id' => $motoTypeId, 'anio_inicio' => 1995, 'anio_fin' => null], // Production ongoing
            ],
            'Peugeot' => [
                ['nombre' => '208', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2012, 'anio_fin' => null],
                ['nombre' => '308', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2007, 'anio_fin' => null],
                ['nombre' => 'Partner', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1996, 'anio_fin' => null],
            ],
            'Citroën' => [
                ['nombre' => 'C3', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2002, 'anio_fin' => null],
                ['nombre' => 'Berlingo', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1996, 'anio_fin' => null],
                ['nombre' => 'C4 Cactus', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2014, 'anio_fin' => null],
            ],
            'Nissan' => [
                ['nombre' => 'Versa', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 2011, 'anio_fin' => null],
                ['nombre' => 'Frontier', 'tipo_vehiculo_id' => $camionetaTypeId, 'anio_inicio' => 1997, 'anio_fin' => null],
                ['nombre' => 'Sentra', 'tipo_vehiculo_id' => $autoTypeId, 'anio_inicio' => 1982, 'anio_fin' => null],
            ],
            'Yamaha' => [
                ['nombre' => 'FZ-S', 'tipo_vehiculo_id' => $motoTypeId, 'anio_inicio' => 2008, 'anio_fin' => null],
                ['nombre' => 'YBR 125', 'tipo_vehiculo_id' => $motoTypeId, 'anio_inicio' => 2000, 'anio_fin' => null],
                ['nombre' => 'XTZ 125', 'tipo_vehiculo_id' => $motoTypeId, 'anio_inicio' => 2003, 'anio_fin' => null],
            ],
        ];

        foreach ($marcas as $marcaNombre => $modelosData) {
            $marca = Marca::firstOrCreate(['nombre' => $marcaNombre]);

            foreach ($modelosData as $modeloData) {
                // Determine the 'anio_fin' value
                $anioFinValue = $modeloData['anio_fin'] ?? $currentYear;

                Modelo::updateOrCreate( // Changed to updateOrCreate to ensure anio_fin is updated if needed
                    [
                        'marca_id' => $marca->id,
                        'nombre' => $modeloData['nombre'],
                    ],
                    [
                        'tipo_vehiculo_id' => $modeloData['tipo_vehiculo_id'],
                        'anio_inicio' => $modeloData['anio_inicio'],
                        'anio_fin' => $anioFinValue, // Use the determined anio_fin value
                    ]
                );
            }
        }
    }
}