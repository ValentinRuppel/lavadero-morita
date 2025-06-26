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
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade'); 
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->foreignId('tipo_lavado_id')->constrained('tipo_lavados')->onDelete('cascade');
            $table->foreignId('administrador_id')->constrained('administrators')->onDelete('cascade');
            
            $table->dateTime('fecha_hora_inicio');
            $table->dateTime('fecha_hora_fin')->nullable();
            $table->decimal('precio_total_servicio', 8, 2)->nullable();
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