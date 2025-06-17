<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Ej: "Coche", "Moto", "Camioneta"
            $table->decimal('precio', 10, 2); // Precio asociado a este tipo de vehículo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_vehiculos');
    }
}