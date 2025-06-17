<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio')->nullable();
            $table->string('patente')->unique();
            $table->string('color')->nullable();

            // Clave Foránea para TipoVehiculo
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculos')->onDelete('restrict');

            // Clave Foránea para Cliente (ahora apuntando a la tabla 'users')
            // La relación 1:1 o 1:N que discutimos antes. Asumo 1:N aquí (un usuario/cliente puede tener muchos vehículos).
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Renombrado de 'cliente_id' a 'user_id' para mayor claridad.

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
        Schema::dropIfExists('vehiculos');
    }
}