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
        Schema::create('servicio_lavados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade'); // Clave foránea a la tabla 'boxes'
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade'); // Asumiendo que ya tienes la tabla 'vehiculos'
            $table->foreignId('tipo_lavado_id')->constrained('tipo_lavados')->onDelete('cascade'); // Clave foránea a la tabla 'tipo_lavados'
            $table->foreignId('administrador_id')->constrained('administrators')->onDelete('cascade'); // Asumiendo que los administradores están en la tabla 'users'
            
            $table->dateTime('fecha_hora_inicio');
            $table->dateTime('fecha_hora_fin')->nullable(); // Puede ser nulo si el servicio está en curso
            $table->decimal('precio_total_servicio', 8, 2)->nullable(); // Precio final, puede ser nulo inicialmente
            $table->enum('estado_servicio', ['en_curso', 'finalizado', 'cancelado'])->default('en_curso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_lavados');
    }
};