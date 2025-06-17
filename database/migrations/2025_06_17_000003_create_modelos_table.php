<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade'); // Clave foránea a la tabla 'marcas'
            $table->string('nombre'); // Nombre del modelo (ej: "Corolla", "Focus")
            $table->year('anio_inicio')->nullable(); // Año de inicio de producción del modelo (opcional)
            $table->year('anio_fin')->nullable(); // Año de fin de producción del modelo (opcional, para coherencia de año)
            
            // <-- ¡Añade esta línea para el tipo_vehiculo_id!
            // Asegúrate de que esta tabla se cree DESPUÉS de 'tipos_vehiculos'
            $table->foreignId('tipo_vehiculo_id')->nullable()->constrained('tipos_vehiculos')->onDelete('set null');

            $table->timestamps();

            // Un modelo con el mismo nombre no puede existir para la misma marca
            $table->unique(['marca_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos');
    }
};