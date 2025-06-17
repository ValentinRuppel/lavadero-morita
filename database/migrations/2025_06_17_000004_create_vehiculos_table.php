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
            // Originalmente tenías 'marca' y 'modelo' como strings, ahora usaremos modelo_id
            // $table->string('marca'); // Eliminado
            // $table->string('modelo'); // Eliminado

            $table->foreignId('modelo_id')->nullable()->constrained('modelos')->onDelete('set null'); // <-- Añadido: foreign key a 'modelos'
            
            $table->integer('anio')->nullable();
            $table->string('patente')->unique();
            // $table->string('color')->nullable(); // <-- Eliminado: la migración de remover color lo quitaba

            // Clave Foránea para TipoVehiculo (ya estaba bien)
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculos')->onDelete('restrict');

            // Clave Foránea para Cliente (user_id) (ya estaba bien)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

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