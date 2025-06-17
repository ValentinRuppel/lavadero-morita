<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoVehiculo; // <-- Importa el modelo TipoVehiculo

class MarcaModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los IDs de los tipos de vehículo existentes
        // Ajusta estos IDs según tus datos reales
        $autoTypeId = TipoVehiculo::where('nombre', 'Auto')->first()->id ?? 1; // Busca el ID del tipo 'Auto'
        $motoTypeId = TipoVehiculo::where('nombre', 'Moto')->first()->id ?? 2; // Busca el ID del tipo 'Moto'
        $camionetaTypeId = TipoVehiculo::where('nombre', 'Camioneta')->first()->id ?? 3; // Ejemplo
        // Añade más si tienes otros tipos...

        $marcas = [
            'Toyota' => [
                ['nombre' => 'Corolla', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Hilux', 'tipo_vehiculo_id' => $camionetaTypeId],
                ['nombre' => 'Yaris', 'tipo_vehiculo_id' => $autoTypeId],
            ],
            'Ford' => [
                ['nombre' => 'Focus', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Ranger', 'tipo_vehiculo_id' => $camionetaTypeId],
                ['nombre' => 'Fiesta', 'tipo_vehiculo_id' => $autoTypeId],
            ],
            'Chevrolet' => [
                ['nombre' => 'Onix', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Cruze', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'S10', 'tipo_vehiculo_id' => $camionetaTypeId],
            ],
            'Volkswagen' => [
                ['nombre' => 'Gol', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Amarok', 'tipo_vehiculo_id' => $camionetaTypeId],
                ['nombre' => 'Vento', 'tipo_vehiculo_id' => $autoTypeId],
            ],
            'Renault' => [
                ['nombre' => 'Clio', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Kangoo', 'tipo_vehiculo_id' => $camionetaTypeId], // O si es utilitario de otro tipo, ajústalo
                ['nombre' => 'Sandero', 'tipo_vehiculo_id' => $autoTypeId],
            ],
            'Fiat' => [
                ['nombre' => 'Cronos', 'tipo_vehiculo_id' => $autoTypeId],
                ['nombre' => 'Toro', 'tipo_vehiculo_id' => $camionetaTypeId],
                ['nombre' => 'Pulse', 'tipo_vehiculo_id' => $autoTypeId],
            ],
            // Ejemplo de Moto
            'Honda' => [
                ['nombre' => 'CB125F', 'tipo_vehiculo_id' => $motoTypeId],
                ['nombre' => 'Wave', 'tipo_vehiculo_id' => $motoTypeId],
            ],
        ];

        foreach ($marcas as $marcaNombre => $modelosData) {
            $marca = Marca::firstOrCreate(['nombre' => $marcaNombre]);

            foreach ($modelosData as $modeloData) {
                Modelo::firstOrCreate(
                    [
                        'marca_id' => $marca->id,
                        'nombre' => $modeloData['nombre'],
                    ],
                    [
                        'tipo_vehiculo_id' => $modeloData['tipo_vehiculo_id'],
                    ]
                );
            }
        }
    }
}